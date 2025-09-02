<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'file_path', 'status', 'assessment', 'client_slug', 'client_name',
        'tokens_used', 'cost', 'ai_responses_metadata', 'verification_status',
        'verified_by', 'verified_at', 'verification_notes'
    ];

    protected $casts = [
        'assessment' => 'array',
        'cost' => 'decimal:4',
        'ai_responses_metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function tokenTransactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }

    // Calculate estimated token cost based on AI models used
    public function estimateTokenCost($prompt)
    {
        // Rough estimation: 1 token per 4 characters for input + response
        $inputTokens = strlen($prompt) / 4;
        $estimatedResponseTokens = 1500; // Average response length
        
        // Cost per model (adjust based on actual API pricing)
        $costs = [
            'claude' => 0.002,  // $0.002 per 1K tokens
            'gpt4' => 0.003,    // $0.003 per 1K tokens
        ];
        
        $totalCost = 0;
        foreach ($costs as $model => $pricePerK) {
            $totalCost += (($inputTokens + $estimatedResponseTokens) / 1000) * $pricePerK;
        }
        
        return round($totalCost, 4);
    }

    public function markAsVerified($verifiedBy, $notes = null)
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    public function markAsRejected($verifiedBy, $notes)
    {
        $this->update([
            'verification_status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUnverified($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
    }
}