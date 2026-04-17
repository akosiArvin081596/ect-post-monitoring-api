<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'surveyor'])
                ->default('surveyor')
                ->after('password');
            $table->string('position')->default('')->after('role');
            $table->string('contact_no', 50)->default('')->after('position');

            $table->index('role');
        });

        DB::table('users')
            ->where('email', 'admin@dswd.gov.ph')
            ->update([
                'role' => 'admin',
                'position' => 'Administrator',
                'contact_no' => 'N/A',
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'position', 'contact_no']);
        });
    }
};
