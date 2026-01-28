<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->string('district');
            $table->string('municipality');
            $table->timestamps();

            $table->index(['province', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_municipalities');
    }
};
