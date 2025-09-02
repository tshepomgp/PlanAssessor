<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create user_roles table (if it doesn't exist)
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->json('permissions')->nullable();
                $table->timestamps();
            });
        }

        // Create tokens table
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('balance')->default(0);
            $table->decimal('credit_balance', 10, 2)->default(0.00);
            $table->timestamps();
            
            $table->index('user_id');
        });

        // Create token_transactions table
        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['purchase', 'usage', 'refund', 'admin_adjustment']);
            $table->integer('tokens')->default(0);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });

        // Create token_packages table
        Schema::create('token_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('tokens');
            $table->decimal('price', 10, 2);
            $table->decimal('bonus_percentage', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('user_roles')->onDelete('set null');
            $table->enum('status', ['active', 'suspended', 'pending'])->default('pending');
            $table->timestamp('last_login_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('notes')->nullable();
        });

        // Add columns to plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 8, 4)->default(0.0000);
            $table->json('ai_responses_metadata')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
        });
    }

    public function down()
    {
        // Remove columns from plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'tokens_used', 'cost', 'ai_responses_metadata', 
                'verification_status', 'verified_at', 'verification_notes'
            ]);
            $table->dropForeign(['verified_by']);
            $table->dropColumn('verified_by');
        });

        // Remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id', 'status', 'last_login_at', 
                'phone', 'company', 'registration_number', 'notes'
            ]);
        });

        // Drop token tables
        Schema::dropIfExists('token_transactions');
        Schema::dropIfExists('token_packages');
        Schema::dropIfExists('tokens');
        
        // Drop user_roles table if needed
        Schema::dropIfExists('user_roles');
    }
};