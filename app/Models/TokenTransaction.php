<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokenTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'plan_id', 
        'type', 
        'tokens', 
        'amount', 
        'description', 
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Scopes
    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeUsage($query)
    {
        return $query->where('type', 'usage');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    public function scopeAdminAdjustments($query)
    {
        return $query->where('type', 'admin_adjustment');
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

    // Accessors & Mutators
    public function getFormattedAmountAttribute()
    {
        return 'R' . number_format($this->amount, 2);
    }

    public function getFormattedTokensAttribute()
    {
        return ($this->tokens > 0 ? '+' : '') . number_format($this->tokens);
    }

    public function getTypeDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    // Helper methods
    public function isPurchase()
    {
        return $this->type === 'purchase';
    }

    public function isUsage()
    {
        return $this->type === 'usage';
    }

    public function isRefund()
    {
        return $this->type === 'refund';
    }

    public function isAdminAdjustment()
    {
        return $this->type === 'admin_adjustment';
    }
}