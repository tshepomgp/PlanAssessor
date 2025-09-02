<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'company', 'registration_number', 
        'role_id', 'status', 'notes'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function tokens()
    {
        return $this->hasOne(Token::class);
    }

    public function tokenTransactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }

    public function verifiedPlans()
    {
        return $this->hasMany(Plan::class, 'verified_by');
    }

    // Get or create token balance
    public function getTokenBalance()
    {
        if (!$this->tokens) {
            $this->tokens()->create(['balance' => 0, 'credit_balance' => 0]);
            $this->load('tokens');
        }
        return $this->tokens;
    }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isArchitect()
    {
        return $this->hasRole('architect');
    }

    public function canUploadPlans()
    {
        return $this->hasPermission('upload_plans') && $this->status === 'active';
    }

    public function canPurchaseTokens()
    {
        return $this->hasPermission('purchase_tokens') && $this->status === 'active';
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeArchitects($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'architect');
        });
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'admin');
        });
    }
}