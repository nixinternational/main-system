<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE processos ALTER COLUMN quantidade TYPE NUMERIC(20,4) USING quantidade::numeric;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE processos ALTER COLUMN quantidade TYPE INTEGER USING quantidade::integer;');
    }
};
