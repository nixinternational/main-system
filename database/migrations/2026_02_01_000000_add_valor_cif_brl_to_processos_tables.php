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
        // Adicionar valor_cif_brl na tabela processos
        Schema::table('processos', function (Blueprint $table) {
            $table->decimal('valor_cif_brl', 15, 2)->nullable()->after('valor_cif');
        });
        
        // Adicionar valor_cif_brl na tabela processo_aereos
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->decimal('valor_cif_brl', 15, 2)->nullable()->after('valor_cif');
        });
        
        // Adicionar valor_cif_brl na tabela processo_rodoviarios
        Schema::table('processo_rodoviarios', function (Blueprint $table) {
            $table->decimal('valor_cif_brl', 15, 2)->nullable()->after('valor_cif');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('valor_cif_brl');
        });
        
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->dropColumn('valor_cif_brl');
        });
        
        Schema::table('processo_rodoviarios', function (Blueprint $table) {
            $table->dropColumn('valor_cif_brl');
        });
    }
};
