<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('status', 'pending')->update(['status' => 'placed']);
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'dispatched']);
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'placed')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'dispatched')->update(['status' => 'shipped']);
    }
};
