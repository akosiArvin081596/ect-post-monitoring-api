<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('client_uuid', 36)->unique();

            // Consent
            $table->boolean('consent_agreed')->default(false);

            // Beneficiary info
            $table->string('beneficiary_name');
            $table->string('respondent_name');
            $table->string('relationship_to_beneficiary');
            $table->string('relationship_specify')->nullable();
            $table->date('birthdate');
            $table->tinyInteger('age')->unsigned();
            $table->json('beneficiary_classification');
            $table->string('household_id_no')->nullable();
            $table->string('sex');
            $table->json('demographic_classification');
            $table->string('ip_specify')->nullable();
            $table->string('highest_educational_attainment');
            $table->string('educational_attainment_specify')->nullable();

            // Address
            $table->string('province');
            $table->string('district');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('sitio_purok_street')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->decimal('accuracy', 10, 2)->nullable();

            // Utilization
            $table->string('utilization_type');
            $table->decimal('amount_received', 12, 2);
            $table->date('date_received');

            // Expenses
            $table->decimal('expense_food', 12, 2)->default(0);
            $table->decimal('expense_educational', 12, 2)->default(0);
            $table->decimal('expense_house_rental', 12, 2)->default(0);
            $table->json('livelihood_types')->nullable();
            $table->string('livelihood_specify')->nullable();
            $table->decimal('expense_livelihood', 12, 2)->default(0);
            $table->decimal('expense_medical', 12, 2)->default(0);
            $table->decimal('expense_non_food_items', 12, 2)->default(0);
            $table->decimal('expense_utilities', 12, 2)->default(0);
            $table->decimal('expense_shelter_materials', 12, 2)->default(0);
            $table->decimal('expense_transportation', 12, 2)->default(0);
            $table->string('expense_others_specify')->nullable();
            $table->decimal('expense_others', 12, 2)->default(0);
            $table->decimal('total_utilization', 12, 2);
            $table->decimal('unutilized_variance', 12, 2);
            $table->text('reason_not_fully_utilized')->nullable();

            // Interviewer
            $table->string('interviewed_by');
            $table->string('position');
            $table->string('survey_modality');
            $table->string('modality_specify')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
