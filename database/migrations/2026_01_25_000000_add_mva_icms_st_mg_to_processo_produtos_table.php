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
            if (!Schema::hasColumn('processo_produtos', 'mva_mg')) {
                $table->decimal('mva_mg', 15, 7)->nullable()->after('custo_unit_credito');
            }
            if (!Schema::hasColumn('processo_produtos', 'icms_st_mg')) {
                $table->decimal('icms_st_mg', 15, 7)->nullable()->after('mva_mg');
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
            if (Schema::hasColumn('processo_produtos', 'mva_mg')) {
                $table->dropColumn('mva_mg');
            }
            if (Schema::hasColumn('processo_produtos', 'icms_st_mg')) {
                $table->dropColumn('icms_st_mg');
            }
        });
    }
};
