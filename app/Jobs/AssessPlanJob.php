<?php

namespace App\Jobs;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Services\AssessmentService;
use Illuminate\Support\Facades\DB;

class AssessPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plan;
    public $timeout = 300; // 5 minutes timeout

    public function __construct(Plan|int $plan)
    {
        $this->plan = is_int($plan) ? Plan::findOrFail($plan) : $plan;
    }

    public function handle(): void
    {
        try {
            // Check if user has sufficient tokens before processing
            $user = $this->plan->user;
            $tokenBalance = $user->getTokenBalance();
            
            // Estimate tokens needed (sum of all AI models)
            $estimatedTokens = 75; // 25 + 30 + 20 from AssessmentService
            
            if (!$tokenBalance->hasEnoughTokens($estimatedTokens)) {
                $this->plan->update([
                    'status' => 'failed',
                    'assessment' => ['error' => 'Insufficient tokens. Please purchase more tokens to continue.']
                ]);
                return;
            }

            $pdfPath = storage_path("app/private/" . $this->plan->file_path);
            \Log::info('Processing PDF: ' . $pdfPath);

            if (!file_exists($pdfPath)) {
                throw new \Exception("File does not exist: $pdfPath");
            }

            // Convert PDF to image
            $imagePath = storage_path("app/private/plans/converted_{$this->plan->id}.png");
            try {
                (new Pdf($pdfPath))->saveImage($imagePath);
            } catch (\Exception $e) {
                throw new \Exception("Failed to convert PDF to image: " . $e->getMessage());
            }

            // OCR processing
            try {
                $ocrText = (new TesseractOCR($imagePath))->run();
                
                // Clean up temporary image file
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            } catch (\Exception $e) {
                throw new \Exception("OCR failed: " . $e->getMessage());
            }

            // Prepare prompt
            $prompt = $this->buildAssessmentPrompt($ocrText);

            // Call AI Assessment Service with token deduction
            $service = app(AssessmentService::class);
            $results = $service->askAllModels($prompt, $this->plan);

            // Create combined assessment
            $combined = $this->formatCombinedAssessment($results);

            // Update plan status
            $this->plan->update([
                'status' => 'completed',
                'assessment' => $combined,
            ]);

            \Log::info("Plan {$this->plan->id} assessment completed successfully");

        } catch (\Exception $e) {
            \Log::error("Plan assessment failed for plan {$this->plan->id}: " . $e->getMessage());
            
            $this->plan->update([
                'status' => 'failed',
                'assessment' => [
                    'error' => 'Assessment failed: ' . $e->getMessage(),
                    'failed_at' => now()->toISOString()
                ]
            ]);

            // Optionally retry the job
            if ($this->attempts() < 3) {
                $this->release(30); // Retry after 30 seconds
            }
        }
    }

    protected function buildAssessmentPrompt($ocrText): string
    {
        $data = ['ocr_text' => $ocrText];
        $structuredData = json_encode($data, JSON_PRETTY_PRINT);

        return <<<EOT
You are a qualified building plan compliance assessor. A registered architect has uploaded a building plan for review. You will receive OCR-scanned text from a technical building plan PDF. Use the latest SANS10400.

Your job is divided into 3 parts:

---

# 🔍 PART 1: ADMINISTRATIVE CHECKS

Please extract the following from the text:
- **Architect Name:** (if found, otherwise say "❌ Not Found")
- **Architect Company Name:** 
- **Architect Registration/PSAT Number:**
- **Owner/Client Name:** 

Also determine if the plan includes a **signature** or **approval block** for:
- The Architect (✅/❌)
- The Owner (✅/❌)

---

# 🏗️ PART 2: SANS 10400 COMPLIANCE CHECK

Assess the extracted data for compliance with SANS 10400. Use ✅ for compliant and ❌ for non-compliant items. Include:
- Reference to the relevant part of SANS (e.g. SANS 10400-N)
- A brief explanation
- A clear recommendation if non-compliant

Format each compliance item as:
**Item:** [Description]
**Status:** ✅/❌
**Reference:** [SANS reference]
**Notes:** [Brief explanation]

---

# 📜 PART 3: NATIONAL BUILDING REGULATIONS COMPLIANCE CHECK

Same as above – assess using ✅ / ❌ with clear reasons.

Format each compliance item as:
**Item:** [Description]
**Status:** ✅/❌
**Reference:** [Regulation reference]
**Notes:** [Brief explanation]

---

# 📋 FINAL SECTION: OVERALL RECOMMENDATION

Provide a summary of the general status of the plan and highlight any missing or urgent issues – especially if signature or identity details are missing.

**Overall Status:** [Pass/Conditional Pass/Fail]
**Critical Issues:** [List any critical issues]
**Recommendations:** [List recommendations]

---

Use this reference data:

OCR Text:
{$structuredData}

Respond professionally, clearly, and in a format readable by registered architects.
EOT;
    }

    private function formatCombinedAssessment(array $results): string
    {
        $timestamp = now()->setTimezone('Africa/Johannesburg')->format('Y-m-d H:i:s');
        
        $output = <<<EOT
# 🏗️ BUILDING PLAN COMPLIANCE ASSESSMENT REPORT

**Assessment Date:** {$timestamp}
**Status:** Completed
**Tokens Used:** {$this->plan->tokens_used}
**Assessment Cost:** R{$this->plan->cost}

---

## 🤖 AI ASSESSMENT #1 - SACAPSA AI-1

{$results['SACAPSA-Ai-Model1']}

---

## 🤖 AI ASSESSMENT #2 - SACAPSA AI-2

{$results['SACAPSA-Ai-Model2']} 

---

## 🎯 CONSOLIDATED OVERALL ASSESSMENT

Based on the analysis from both AI systems, here is the consolidated assessment:

### 📊 Summary Comparison
- **SACAPSA AI-1 Assessment:** [To be extracted from individual assessments]
- **SACAPSA AI-2 Assessment:** [To be extracted from individual assessments]

### 🔍 Key Findings
{$this->extractKeyFindings($results)}

### ⚠️ Critical Issues Identified
{$this->extractCriticalIssues($results)}

### ✅ Recommendations
{$this->extractRecommendations($results)}

### 📋 Final Status
{$this->determineFinalStatus($results)}

---

*This assessment was generated using AI technology and should be reviewed by a qualified professional. Both AI systems have analyzed the building plan independently to provide comprehensive coverage.*

**Quality Assurance:** This assessment is pending verification by a SACAP administrator.

EOT;

        return $output;
    }

    // Keep existing helper methods...
    private function extractKeyFindings(array $results): string
    {
        $findings = [];
        
        foreach ($results as $aiName => $response) {
            if (strpos($response, '❌') !== false) {
                $findings[] = "• {$aiName} identified non-compliant items";
            }
            if (strpos($response, '✅') !== false) {
                $findings[] = "• {$aiName} identified compliant items";
            }
        }
        
        return empty($findings) ? "• No specific findings extracted" : implode("\n", array_unique($findings));
    }

    private function extractCriticalIssues(array $results): string
    {
        $issues = [];
        
        foreach ($results as $aiName => $response) {
            if (strpos($response, 'Not Found') !== false || strpos($response, '❌') !== false) {
                $issues[] = "• {$aiName} flagged potential compliance issues";
            }
            if (strpos($response, 'signature') !== false && strpos($response, '❌') !== false) {
                $issues[] = "• Missing signature blocks identified by {$aiName}";
            }
        }
        
        return empty($issues) ? "• No critical issues identified" : implode("\n", array_unique($issues));
    }

    private function extractRecommendations(array $results): string
    {
        $recommendations = [];
        
        foreach ($results as $aiName => $response) {
            if (strpos($response, 'recommendation') !== false || strpos($response, 'Recommendation') !== false) {
                $recommendations[] = "• Review recommendations provided by {$aiName}";
            }
        }
        
        $recommendations[] = "• Verify all findings with a qualified professional";
        $recommendations[] = "• Address any identified non-compliant items";
        $recommendations[] = "• Ensure all required signatures and documentation are present";
        
        return implode("\n", $recommendations);
    }

    private function determineFinalStatus(array $results): string
    {
        $hasIssues = false;
        
        foreach ($results as $response) {
            if (strpos($response, '❌') !== false || strpos($response, 'Not Found') !== false) {
                $hasIssues = true;
                break;
            }
        }
        
        if ($hasIssues) {
            return "**CONDITIONAL PASS** - Issues identified that require attention";
        } else {
            return "**PRELIMINARY PASS** - No major issues identified (professional review recommended)";
        }
    }

    public function failed(\Exception $exception)
    {
        \Log::error("AssessPlanJob failed for plan {$this->plan->id}: " . $exception->getMessage());
        
        $this->plan->update([
            'status' => 'failed',
            'assessment' => [
                'error' => 'Assessment job failed: ' . $exception->getMessage(),
                'failed_at' => now()->toISOString()
            ]
        ]);
    }
}