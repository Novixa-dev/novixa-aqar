<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // App\Models\SavedSearch has always existed (fillable: user_id,
        // criteria) and is actively queried by PropertyRecommendationService
        // and, through it, AIInvestmentAnalysisService -- but no migration
        // ever created this table. Every property-detail page view silently
        // failed with "Base table or view not found: saved_searches"
        // (caught and swallowed with a fallback in
        // AIInvestmentAnalysisService::analyzeProperty(), so no visible
        // crash, but real AI investment analysis never actually ran).
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('criteria');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
