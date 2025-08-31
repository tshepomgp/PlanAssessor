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

class AssessPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plan;

public function __construct(Plan|int $plan)
    {
	    $this->plan = is_int($plan) ? Plan::findOrFail($plan) : $plan;
    }

    protected function loadSansReferences(): string
    {
        $sansFolder = storage_path('app/sans_refs_txt');
        $text = '';

        foreach (scandir($sansFolder) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                $filePath = $sansFolder . DIRECTORY_SEPARATOR . $file;
                $contents = file_get_contents($filePath);
                $text .= "\n\n--- [" . basename($file) . "] ---\n" . $contents;
            }
        }

        return $text;
    }

    public function handle(): void
    {
        $pdfPath = storage_path("app/private/" . $this->plan->file_path);
        \Log::info('PDF Path: ' . $pdfPath);

        if (!file_exists($pdfPath)) {
            \Log::error("File does not exist: $pdfPath");
            return;
        }

        // Convert PDF to image
        $imagePath = storage_path("app/private/plans/converted_{$this->plan->id}.png");
        try {
            (new Pdf($pdfPath))->saveImage($imagePath);
        } catch (\Exception $e) {
            \Log::error("Failed to convert PDF to image: " . $e->getMessage());
            return;
        }

        // OCR processing
        try {
            $ocrText = (new TesseractOCR($imagePath))->run();
        } catch (\Exception $e) {
            \Log::error("OCR failed: " . $e->getMessage());
            return;
        }

        // Combine OCR text with SANS references
        $data = ['ocr_text' => $ocrText];
        $structuredData = json_encode($data, JSON_PRETTY_PRINT);

        $prompt = <<<EOT
You are a qualified building plan compliance assessor. A registered architect has uploaded a building plan for review. You will receive OCR-scanned text from a technical building plan PDF. Use the latest SANS10400.

Your job is divided into 3 parts:

---

# 🔍 PART 1: ADMINISTRATIVE CHECKS

Please extract the following from the text:
- **Architect Name:** (if found, otherwise say "✘ Not Found")
- **Architect Company Name:** 
- **Architect Registration/PSAT Number:**
- **Owner/Client Name:** 

Also determine if the plan includes a **signature** or **approval block** for:
- The Architect (✔/✘)
- The Owner (✔/✘)

---

# 📏 PART 2: SANS 10400 COMPLIANCE CHECK

Assess the extracted data for compliance with SANS 10400. Use ✔ for compliant and ✘ for non-compliant items. Include:
- Reference to the relevant part of SANS (e.g. SANS 10400-N)
- A brief explanation
- A clear recommendation if non-compliant

Format each compliance item as:
**Item:** [Description]
**Status:** ✔/✘
**Reference:** [SANS reference]
**Notes:** [Brief explanation]

---

# 📜 PART 3: NATIONAL BUILDING REGULATIONS COMPLIANCE CHECK

Same as above — assess using ✔ / ✘ with clear reasons.

Format each compliance item as:
**Item:** [Description]
**Status:** ✔/✘
**Reference:** [Regulation reference]
**Notes:** [Brief explanation]

---

# 📌 FINAL SECTION: OVERALL RECOMMENDATION

Provide a summary of the general status of the plan and highlight any missing or urgent issues — especially if signature or identity details are missing.

**Overall Status:** [Pass/Conditional Pass/Fail]
**Critical Issues:** [List any critical issues]
**Recommendations:** [List recommendations]

---

Use this reference data:

OCR Text:
{$structuredData}

Respond professionally, clearly, and in a format readable by registered architects.
EOT;

        // Call AssessmentService
        $service = app(AssessmentService::class);
        $results = $service->askAllModels($prompt);

        // Create combined assessment with better formatting
        $combined = $this->formatCombinedAssessment($results);

        $this->plan->update([
            'status' => 'completed',
            'assessment' => $combined,
        ]);
    }

    private function formatCombinedAssessment(array $results): string
    {
	    $timestamp = now()->setTimezone('Africa/Johannesburg')->format('Y-m-d H:i:s');
        
        $output = <<<EOT
# 🏗️ BUILDING PLAN COMPLIANCE ASSESSMENT REPORT


**Assessment Date:** {$timestamp}
**Status:** Completed

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

EOT;

        return $output;
    }

    private function extractKeyFindings(array $results): string
    {
        // Extract common findings between both AI responses
        $findings = [];
        
        // Look for common patterns like "✔" and "✘" in both responses
        foreach ($results as $aiName => $response) {
            if (strpos($response, '✘') !== false) {
                $findings[] = "• {$aiName} identified non-compliant items";
            }
            if (strpos($response, '✔') !== false) {
                $findings[] = "• {$aiName} identified compliant items";
            }
        }
        
        return empty($findings) ? "• No specific findings extracted" : implode("\n", array_unique($findings));
    }

    private function extractCriticalIssues(array $results): string
    {
        $issues = [];
        
        foreach ($results as $aiName => $response) {
            // Look for common critical issue indicators
            if (strpos($response, 'Not Found') !== false || strpos($response, '✘') !== false) {
                $issues[] = "• {$aiName} flagged potential compliance issues";
            }
            if (strpos($response, 'signature') !== false && strpos($response, '✘') !== false) {
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
            if (strpos($response, '✘') !== false || strpos($response, 'Not Found') !== false) {
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
}
