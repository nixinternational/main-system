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
        Schema::table('processo_aereo_produtos', function (Blueprint $table) {
            // Adicionar colunas THC após acresc_frete_brl
            $table->decimal('thc_usd', 15, 2)->nullable()->after('acresc_frete_brl');
            $table->decimal('thc_brl', 15, 2)->nullable()->after('thc_usd');
            
            // Adicionar coluna codigo_giiro após origem
            $table->string('codigo_giiro')->nullable()->after('origem');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processo_aereo_produtos', function (Blueprint $table) {
            $table->dropColumn(['thc_usd', 'thc_brl', 'codigo_giiro']);
        });
    }
};
