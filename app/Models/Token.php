<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'balance', 
        'credit_balance'
    ];

    protected $casts = [
        'credit_balance' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class, 'user_id', 'user_id');
    }

    // Balance checking methods
    public function hasEnoughTokens($required)
    {
        return $this->balance >= $required;
    }

    public function canAfford($tokenCost)
    {
        return $this->hasEnoughTokens($tokenCost);
    }

    // Transaction methods
    public function deductTokens($amount, $description, $planId = null, $metadata = [])
    {
        if (!$this->hasEnoughTokens($amount)) {
            throw new \Exception("Insufficient tokens. Required: {$amount}, Available: {$this->balance}");
        }

        \DB::transaction(function () use ($amount, $description, $planId, $metadata) {
            $this->decrement('balance', $amount);

            TokenTransaction::create([
                'user_id' => $this->user_id,
                'plan_id' => $planId,
                'type' => 'usage',
                'tokens' => -$amount, // Negative for usage
                'amount' => 0,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });

        return $this;
    }

    public function addTokens($amount, $description, $cost = 0, $metadata = [])
    {
        \DB::transaction(function () use ($amount, $description, $cost, $metadata) {
            $this->increment('balance', $amount);

            TokenTransaction::create([
                'user_id' => $this->user_id,
                'plan_id' => null,
                'type' => 'purchase',
                'tokens' => $amount, // Positive for addition
                'amount' => $cost,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });

        return $this;
    }

    public function refundTokens($amount, $description, $refundAmount = 0, $metadata = [])
    {
        \DB::transaction(function () use ($amount, $description, $refundAmount, $metadata) {
            $this->increment('balance', $amount);

            TokenTransaction::create([
                'user_id' => $this->user_id,
                'plan_id' => null,
                'type' => 'refund',
                'tokens' => $amount,
                'amount' => $refundAmount,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });

        return $this;
    }

    public function adminAdjustment($amount, $description, $metadata = [])
    {
        \DB::transaction(function () use ($amount, $description, $metadata) {
            if ($amount > 0) {
                $this->increment('balance', $amount);
            } else {
                $this->decrement('balance', abs($amount));
            }

            TokenTransaction::create([
                'user_id' => $this->user_id,
                'plan_id' => null,
                'type' => 'admin_adjustment',
                'tokens' => $amount,
                'amount' => 0,
                'description' => $description,
                'metadata' => array_merge($metadata, ['admin_id' => auth()->id()]),
            ]);
        });

        return $this;
    }

    // Statistics methods
    public function getTotalPurchased()
    {
        return $this->transactions()
                   ->where('type', 'purchase')
                   ->sum('tokens');
    }

    public function getTotalUsed()
    {
        return abs($this->transactions()
                       ->where('type', 'usage')
                       ->sum('tokens'));
    }

    public function getTotalSpent()
    {
        return $this->transactions()
                   ->where('type', 'purchase')
                   ->sum('amount');
    }

    public function getAverageTokensPerAssessment()
    {
        return $this->transactions()
                   ->where('type', 'usage')
                   ->whereNotNull('plan_id')
                   ->avg(\DB::raw('ABS(tokens)'));
    }

    // Accessors
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance) . ' tokens';
    }

    public function getFormattedCreditBalanceAttribute()
    {
        return 'R' . number_format($this->credit_balance, 2);
    }

    public function getStatusAttribute()
    {
        if ($this->balance <= 0) {
            return 'empty';
        } elseif ($this->balance < 50) {
            return 'low';
        } elseif ($this->balance < 200) {
            return 'medium';
        } else {
            return 'good';
        }
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'empty' => 'red',
            'low' => 'orange',
            'medium' => 'yellow',
            'good' => 'green',
            default => 'gray'
        };
    }
}