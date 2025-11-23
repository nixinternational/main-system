<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Verificar se a coluna peso_liq_lbs existe, se não existir, criar
        if (!Schema::hasColumn('processo_aereo_produtos', 'peso_liq_lbs')) {
            Schema::table('processo_aereo_produtos', function (Blueprint $table) {
                $table->decimal('peso_liq_lbs', 15, 6)->nullable()->after('quantidade');
            });
        }

        // Verificar se a coluna peso_liq_total_kg existe, se não existir, criar
        if (!Schema::hasColumn('processo_aereo_produtos', 'peso_liq_total_kg')) {
            Schema::table('processo_aereo_produtos', function (Blueprint $table) {
                $table->decimal('peso_liq_total_kg', 15, 6)->nullable()->after('peso_liquido_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Não remover as colunas no down, pois podem estar em uso
        // Se necessário remover, usar a migration original
    }
};
