<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_barangays', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->string('district');
            $table->string('municipality');
            $table->string('barangay');
            $table->timestamps();

            $table->index(['province', 'district', 'municipality'], 'addr_barangays_pdm_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_barangays');
    }
};
