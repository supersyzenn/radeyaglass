<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('store_settings')
            ->where('id', 1)
            ->where('name', 'Radeya Glass dan Aluminium')
            ->update(['name' => 'Radeya Glass & Aluminium']);
    }

    public function down(): void
    {
        DB::table('store_settings')
            ->where('id', 1)
            ->where('name', 'Radeya Glass & Aluminium')
            ->update(['name' => 'Radeya Glass dan Aluminium']);
    }
};
