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
            // Colunas para valores na moeda estrangeira original
            $table->decimal('frete_moeda_estrangeira', 15, 7)->nullable()->after('frete_brl');
            $table->decimal('seguro_moeda_estrangeira', 15, 7)->nullable()->after('seguro_brl');
            $table->decimal('acrescimo_moeda_estrangeira', 15, 7)->nullable()->after('acresc_frete_brl');
            
            // Colunas para armazenar as moedas utilizadas (opcional, para auditoria)
            $table->string('frete_moeda', 10)->nullable()->after('frete_moeda_estrangeira');
            $table->string('seguro_moeda', 10)->nullable()->after('seguro_moeda_estrangeira');
            $table->string('acrescimo_moeda', 10)->nullable()->after('acrescimo_moeda_estrangeira');
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
                'frete_moeda_estrangeira',
                'seguro_moeda_estrangeira',
                'acrescimo_moeda_estrangeira',
                'frete_moeda',
                'seguro_moeda',
                'acrescimo_moeda'
            ]);
        });
    }
};
