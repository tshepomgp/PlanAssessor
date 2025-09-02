<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    public function run(): void
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
            UserRole::updateOrCreate(
                ['name' => $roleData['name']], 
                $roleData
            );
        }

        $this->command->info('User roles created successfully!');
    }
}