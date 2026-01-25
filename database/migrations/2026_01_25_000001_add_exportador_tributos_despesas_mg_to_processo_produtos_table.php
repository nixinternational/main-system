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
        Schema::table('processo_produtos', function (Blueprint $table) {
            // Verificar se as colunas já existem antes de adicionar
            if (!Schema::hasColumn('processo_produtos', 'exportador_mg')) {
                $table->decimal('exportador_mg', 15, 7)->nullable()->after('custo_unit_c_icms_st');
            }
            if (!Schema::hasColumn('processo_produtos', 'tributos_mg')) {
                $table->decimal('tributos_mg', 15, 7)->nullable()->after('exportador_mg');
            }
            if (!Schema::hasColumn('processo_produtos', 'despesas_mg')) {
                $table->decimal('despesas_mg', 15, 7)->nullable()->after('tributos_mg');
            }
            if (!Schema::hasColumn('processo_produtos', 'total_pago_mg')) {
                $table->decimal('total_pago_mg', 15, 7)->nullable()->after('despesas_mg');
            }
            if (!Schema::hasColumn('processo_produtos', 'percentual_s_fob_mg')) {
                $table->decimal('percentual_s_fob_mg', 15, 7)->nullable()->after('total_pago_mg');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            if (Schema::hasColumn('processo_produtos', 'exportador_mg')) {
                $table->dropColumn('exportador_mg');
            }
            if (Schema::hasColumn('processo_produtos', 'tributos_mg')) {
                $table->dropColumn('tributos_mg');
            }
            if (Schema::hasColumn('processo_produtos', 'despesas_mg')) {
                $table->dropColumn('despesas_mg');
            }
            if (Schema::hasColumn('processo_produtos', 'total_pago_mg')) {
                $table->dropColumn('total_pago_mg');
            }
            if (Schema::hasColumn('processo_produtos', 'percentual_s_fob_mg')) {
                $table->dropColumn('percentual_s_fob_mg');
            }
        });
    }
};
