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
            // Colunas do pedido anterior (calculadas)
            $table->decimal('dez_porcento', 15, 7)->nullable()->after('custo_total_final');
            $table->decimal('custo_com_margem', 15, 7)->nullable()->after('dez_porcento');
            $table->decimal('vlr_ipi_mg', 15, 7)->nullable()->after('custo_com_margem');
            $table->decimal('vlr_icms_mg', 15, 7)->nullable()->after('vlr_ipi_mg');
            $table->decimal('pis_mg', 15, 7)->nullable()->after('vlr_icms_mg');
            $table->decimal('cofins_mg', 15, 7)->nullable()->after('pis_mg');
            $table->decimal('custo_total_final_credito', 15, 7)->nullable()->after('cofins_mg');
            $table->decimal('custo_unit_credito', 15, 7)->nullable()->after('custo_total_final_credito');
            
            // Colunas deste pedido
            $table->decimal('mva_mg', 15, 7)->nullable()->after('custo_unit_credito');
            $table->decimal('icms_st_mg', 15, 7)->nullable()->after('mva_mg');
            $table->decimal('bc_icms_st_mg', 15, 7)->nullable()->after('icms_st_mg');
            $table->decimal('vlr_icms_st_mg', 15, 7)->nullable()->after('bc_icms_st_mg');
            $table->decimal('custo_total_c_icms_st', 15, 7)->nullable()->after('vlr_icms_st_mg');
            $table->decimal('custo_unit_c_icms_st', 15, 7)->nullable()->after('custo_total_c_icms_st');
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
            $table->dropColumn([
                'dez_porcento',
                'custo_com_margem',
                'vlr_ipi_mg',
                'vlr_icms_mg',
                'pis_mg',
                'cofins_mg',
                'custo_total_final_credito',
                'custo_unit_credito',
                'mva_mg',
                'icms_st_mg',
                'bc_icms_st_mg',
                'vlr_icms_st_mg',
                'custo_total_c_icms_st',
                'custo_unit_c_icms_st'
            ]);
        });
    }
};
