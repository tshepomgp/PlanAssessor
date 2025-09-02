<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // Permission methods
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
        return $this;
    }

    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
        return $this;
    }

    public function syncPermissions(array $permissions)
    {
        $this->update(['permissions' => $permissions]);
        return $this;
    }

    // Static methods for default roles
    public static function createDefaultRoles()
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System Administrator',
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'manage_tokens',
                    'verify_plans',
                    'view_analytics',
                    'manage_settings',
                    'manage_token_packages',
                    'admin_adjustments',
                    'view_all_plans',
                    'export_data'
                ]
            ],
            [
                'name' => 'architect',
                'description' => 'Licensed Architect',
                'permissions' => [
                    'upload_plans',
                    'view_assessments',
                    'purchase_tokens',
                    'view_token_history',
                    'download_reports',
                    'retry_assessments'
                ]
            ],
            [
                'name' => 'viewer',
                'description' => 'Read-only Access',
                'permissions' => [
                    'view_assessments',
                    'view_token_history'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            self::updateOrCreate(
                ['name' => $roleData['name']], 
                $roleData
            );
        }
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    public function isArchitect()
    {
        return $this->name === 'architect';
    }

    public function isViewer()
    {
        return $this->name === 'viewer';
    }

    // Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return ucfirst($this->name);
    }

    public function getPermissionCountAttribute()
    {
        return count($this->permissions ?? []);
    }

    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }
}