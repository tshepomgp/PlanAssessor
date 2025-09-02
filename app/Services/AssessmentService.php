<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Plan;

class AssessmentService
{
    // Token costs per model (adjust based on actual API pricing)
    protected $tokenCosts = [
        'claude' => 25,    // tokens per assessment
        'gpt4' => 30,      // tokens per assessment
        'deepseek' => 20,  // tokens per assessment
    ];

    public function askAllModels(string $prompt, Plan $plan): array
    {
        $user = $plan->user;
        $tokenBalance = $user->getTokenBalance();
        
        // Calculate total tokens needed
        $totalTokensNeeded = array_sum($this->tokenCosts);
        
        if (!$tokenBalance->hasEnoughTokens($totalTokensNeeded)) {
            throw new \Exception("Insufficient tokens. Need {$totalTokensNeeded}, have {$tokenBalance->balance}");
        }

        $results = [];
        $totalTokensUsed = 0;
        $totalCost = 0;

        // Process each model
        foreach ($this->tokenCosts as $model => $cost) {
            try {
                switch ($model) {
                    case 'claude':
                        $result = $this->askClaude($prompt);
                        break;
                    case 'gpt4':
                        $result = $this->askOpenAI($prompt);
                        break;
                    case 'deepseek':
                        $result = $this->askDeepSeek($prompt);
                        break;
                }

                // Deduct tokens for successful API call
                $tokenBalance->deductTokens(
                    $cost,
                    "AI Assessment - " . ucfirst($model),
                    $plan->id,
                    [
                        'model' => $model,
                        'prompt_length' => strlen($prompt),
                        'response_length' => strlen($result),
                        'api_successful' => true
                    ]
                );

                $totalTokensUsed += $cost;
                $totalCost += $cost * 0.01; // Assuming R0.01 per token

                $results['SACAPSA-Ai-Model' . (array_search($model, array_keys($this->tokenCosts)) + 1)] = $result;

            } catch (\Exception $e) {
                // Log error but don't deduct tokens for failed requests
                \Log::error("AI Assessment failed for {$model}: " . $e->getMessage());
                $results['SACAPSA-Ai-Model' . (array_search($model, array_keys($this->tokenCosts)) + 1)] = "❌ {$model} failed: " . $e->getMessage();
            }
        }

        // Update plan with token usage information
        $plan->update([
            'tokens_used' => $totalTokensUsed,
            'cost' => $totalCost,
            'ai_responses_metadata' => [
                'models_used' => array_keys($this->tokenCosts),
                'tokens_per_model' => $this->tokenCosts,
                'total_tokens' => $totalTokensUsed,
                'total_cost' => $totalCost,
                'assessment_date' => now()->toISOString()
            ]
        ]);

        return $results;
    }

    protected function askOpenAI(string $prompt): string
    {
        try {
            $response = Http::timeout(60)
                ->withToken(env('OPENAI_API_KEY'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 2000,
                ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API request failed: ' . $response->status());
            }

            return $response['choices'][0]['message']['content'] ?? '❌ GPT-4o returned no content';

        } catch (\Exception $e) {
            \Log::error('OpenAI API Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function askClaude(string $prompt): string
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'x-api-key' => env('ANTHROPIC_API_KEY'),
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'max_tokens' => 2000,
                    'messages' => [
                        [
                            'role' => 'user', 
                            'content' => $prompt
                        ]
                    ],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Claude API request failed: ' . $response->status());
            }

            $data = $response->json();
            return $data['content'][0]['text'] ?? '❌ Claude returned no content';
            
        } catch (\Exception $e) {
            \Log::error('Claude API Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function askDeepSeek(string $prompt): string
    {
        try {
            $response = Http::withOptions([
                'connect_timeout' => 10,
                'timeout' => 60,
            ])
            ->withToken(env('DEEPSEEK_API_KEY'))
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 2000,
            ]);

            if (!$response->successful()) {
                throw new \Exception('DeepSeek API request failed: ' . $response->status());
            }

            $json = $response->json();

            if (isset($json['choices'][0]['message']['content'])) {
                return $json['choices'][0]['message']['content'];
            } else {
                throw new \Exception('DeepSeek returned unexpected response format');
            }
            
        } catch (\Exception $e) {
            \Log::error("DeepSeek API Exception: " . $e->getMessage());
            throw $e;
        }
    }
}