<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->string('company_name', 255)->nullable()->after('company_id');
            $table->string('title');
            $table->enum('work_mode', ['remote', 'office', 'hybrid'])->nullable();
            $table->string('location')->nullable();
            $table->text('link_job')->nullable();
            $table->string('platform')->nullable();
            $table->enum('status', ['pending', 'negative', 'positive', 'interview', 'offer', 'no_response'])->default('pending');
            $table->timestamp('status_changed_at')->nullable();
            $table->date('interview_date')->nullable();
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
