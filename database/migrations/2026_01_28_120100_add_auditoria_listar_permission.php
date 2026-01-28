<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissaos')->updateOrInsert(
            ['slug' => 'auditoria_listar'],
            [
                'nome' => 'Auditoria - Listar',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissaos')
            ->where('slug', 'auditoria_listar')
            ->delete();
    }
};
