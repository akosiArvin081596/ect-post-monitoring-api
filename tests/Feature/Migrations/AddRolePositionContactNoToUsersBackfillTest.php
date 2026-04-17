<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Covers the upgrade path that RegisterTest.php cannot reach: an existing
 * production DB already has the admin@dswd.gov.ph row BEFORE the new migration
 * runs. The migration's DB::table()->update() backfill must set
 * role/position/contact_no on that row.
 *
 * RegisterTest uses RefreshDatabase, so migrations run with an empty users
 * table — the backfill WHERE matches zero rows there. This test explicitly
 * rolls back the new migration, inserts a pre-upgrade admin row, then
 * re-applies the migration so the backfill actually runs.
 */

test('new migration backfills role, position, contact_no on existing admin row', function () {
    Artisan::call('migrate:fresh');
    Artisan::call('migrate:rollback', ['--step' => 1]);

    expect(Schema::hasColumn('users', 'role'))->toBeFalse();
    expect(Schema::hasColumn('users', 'position'))->toBeFalse();
    expect(Schema::hasColumn('users', 'contact_no'))->toBeFalse();

    DB::table('users')->insert([
        'name' => 'Admin',
        'email' => 'admin@dswd.gov.ph',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Artisan::call('migrate');

    $admin = DB::table('users')->where('email', 'admin@dswd.gov.ph')->first();
    expect($admin)->not->toBeNull();
    expect($admin->role)->toBe('admin');
    expect($admin->position)->toBe('Administrator');
    expect($admin->contact_no)->toBe('N/A');
});
