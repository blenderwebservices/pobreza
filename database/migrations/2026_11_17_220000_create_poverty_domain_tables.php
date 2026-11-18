<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poverty_levels', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('bmi_analogy_label');
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('color')->default('gray');
            $table->string('badge_hex')->default('#64748b');
            $table->text('description');
            $table->text('implications');
            $table->integer('display_order')->default(1);
            $table->timestamps();
        });

        Schema::create('policy_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poverty_level_id')->constrained('poverty_levels')->cascadeOnDelete();
            $table->string('target_segment')->default('todos'); // general, femenina, masculina, todos
            $table->string('dimension'); // ingreso, vivienda, servicios, salud, educacion, alimentacion, genero
            $table->string('title');
            $table->text('technical_action');
            $table->text('ideal_framework_rationale');
            $table->string('priority')->default('alta'); // critica, alta, media, preventiva
            $table->string('expected_impact');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('folio_code')->unique();
            $table->string('name');
            $table->string('subject_type')->default('hogar'); // individuo, hogar, grupo_comunitario
            $table->string('state');
            $table->string('municipality');
            $table->string('zone_type')->default('urbana'); // urbana, rural
            $table->unsignedInteger('total_members')->default(4);
            $table->unsignedInteger('female_members')->default(2);
            $table->unsignedInteger('male_members')->default(2);
            $table->unsignedInteger('dependent_children')->default(1);
            $table->boolean('female_headed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('batch_code')->unique();
            $table->string('source_name'); // Ej. ENIGH 2024 - INEGI
            $table->string('filename');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->string('status')->default('completado');
            $table->json('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('poverty_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('poverty_level_id')->nullable()->constrained('poverty_levels')->nullOnDelete();

            $table->unsignedSmallInteger('year')->index();
            $table->string('quarter')->default('T4');
            $table->string('evaluation_source')->default('cuestionario'); // cuestionario, importacion_enigh

            // Ingresos y Gastos Mensuales (MXN)
            $table->decimal('monthly_income_total', 12, 2)->default(0);
            $table->decimal('monthly_income_female', 12, 2)->default(0);
            $table->decimal('monthly_income_male', 12, 2)->default(0);
            $table->decimal('monthly_expense_total', 12, 2)->default(0);
            $table->decimal('monthly_expense_food', 12, 2)->default(0);
            $table->decimal('monthly_expense_housing_services', 12, 2)->default(0);
            $table->decimal('monthly_expense_health_education', 12, 2)->default(0);
            $table->decimal('female_unpaid_care_hours_weekly', 6, 2)->default(28);
            $table->decimal('male_unpaid_care_hours_weekly', 6, 2)->default(10);

            // Condiciones físicas de la vivienda y carencias (escala 0 = sin carencia, 100 = carencia crítica)
            $table->unsignedTinyInteger('housing_floor_wall_deprivation')->default(0);
            $table->unsignedTinyInteger('housing_overcrowding_deprivation')->default(0);
            $table->unsignedTinyInteger('basic_services_deprivation')->default(0);
            $table->unsignedTinyInteger('education_lag_deprivation')->default(0);
            $table->unsignedTinyInteger('health_security_deprivation')->default(0);
            $table->unsignedTinyInteger('food_insecurity_deprivation')->default(0);

            // Indicadores Calculados (Motor de Índice de Pobreza tipo IMC)
            $table->decimal('per_capita_income', 12, 2)->default(0);
            $table->decimal('income_to_expense_ratio', 8, 4)->default(1);
            $table->decimal('economic_vulnerability_score', 5, 2)->default(0);
            $table->decimal('multidimensional_deprivation_score', 5, 2)->default(0);

            // Índices Segmentados (0.00 a 100.00)
            $table->decimal('poverty_index_general', 5, 2)->index();
            $table->decimal('poverty_index_female', 5, 2)->index();
            $table->decimal('poverty_index_male', 5, 2)->index();

            // Clasificación en Niveles Progresivos
            $table->string('level_slug_general')->index();
            $table->string('level_slug_female');
            $table->string('level_slug_male');

            // Snapshots explicativos y técnicos
            $table->json('dimension_breakdown')->nullable();
            $table->json('recommended_policies_snapshot')->nullable();
            $table->timestamps();

            $table->index(['household_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poverty_evaluations');
        Schema::dropIfExists('import_batches');
        Schema::dropIfExists('households');
        Schema::dropIfExists('policy_recommendations');
        Schema::dropIfExists('poverty_levels');
    }
};
