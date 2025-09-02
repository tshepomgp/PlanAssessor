<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokenPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'tokens', 
        'price', 
        'bonus_percentage', 
        'is_active', 
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'bonus_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getTotalTokensAttribute()
    {
        $bonus = ($this->tokens * $this->bonus_percentage) / 100;
        return $this->tokens + (int)$bonus;
    }

    public function getBonusTokensAttribute()
    {
        return $this->total_tokens - $this->tokens;
    }

    public function getPricePerTokenAttribute()
    {
        return $this->total_tokens > 0 ? $this->price / $this->total_tokens : 0;
    }

    public function getFormattedPriceAttribute()
    {
        return 'R' . number_format($this->price, 2);
    }

    public function getFormattedPricePerTokenAttribute()
    {
        return 'R' . number_format($this->price_per_token, 4);
    }

    public function getEstimatedAssessmentsAttribute()
    {
        // Assuming 75 tokens per assessment on average
        return floor($this->total_tokens / 75);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')
                    ->orderBy('price');
    }

    // Static methods
    public static function getActivePackages()
    {
        return self::active()
                  ->ordered()
                  ->get();
    }

    public static function getMostPopular()
    {
        // You can track purchases and return the most popular
        // For now, return the one with best value (lowest price per token)
        return self::active()
                  ->get()
                  ->sortBy('price_per_token')
                  ->first();
    }

    public static function getBestValue()
    {
        return self::active()
                  ->where('bonus_percentage', '>', 0)
                  ->orderBy('bonus_percentage', 'desc')
                  ->first();
    }

    // Helper methods
    public function hasBonus()
    {
        return $this->bonus_percentage > 0;
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function calculateSavings($comparedToPackage = null)
    {
        if (!$comparedToPackage) {
            // Compare to the most basic package (highest price per token)
            $comparedToPackage = self::active()
                                   ->get()
                                   ->sortBy('price_per_token')
                                   ->last();
        }

        if (!$comparedToPackage || $comparedToPackage->id === $this->id) {
            return 0;
        }

        $standardCost = $this->total_tokens * $comparedToPackage->price_per_token;
        return $standardCost - $this->price;
    }
}