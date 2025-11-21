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
        // Remover dai_brl e dape_brl da tabela processo_aereos
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->dropColumn(['dai_brl', 'dape_brl']);
        });

        // Remover dai_brl e dape_brl da tabela processo_aereo_produtos
        Schema::table('processo_aereo_produtos', function (Blueprint $table) {
            $table->dropColumn(['dai_brl', 'dape_brl']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverter: adicionar dai_brl e dape_brl de volta
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->decimal('dai_brl', 15, 2)->nullable()->after('dai');
            $table->decimal('dape_brl', 15, 2)->nullable()->after('dape');
        });

        Schema::table('processo_aereo_produtos', function (Blueprint $table) {
            $table->decimal('dai_brl', 15, 2)->nullable()->after('dai');
            $table->decimal('dape_brl', 15, 2)->nullable()->after('dape');
        });
    }
};
