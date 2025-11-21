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
            // Campos específicos do transporte aéreo
            $table->decimal('delivery_fee', 15, 2)->nullable()->after('thc_brl');
            $table->decimal('delivery_fee_brl', 15, 2)->nullable()->after('delivery_fee');
            $table->decimal('collect_fee', 15, 2)->nullable()->after('delivery_fee_brl');
            $table->decimal('collect_fee_brl', 15, 2)->nullable()->after('collect_fee');
            $table->decimal('dai', 15, 2)->nullable()->after('collect_fee_brl');
            $table->decimal('dai_brl', 15, 2)->nullable()->after('dai');
            $table->decimal('dape', 15, 2)->nullable()->after('dai_brl');
            $table->decimal('dape_brl', 15, 2)->nullable()->after('dape');
            // Campo para valor CFR (CIF) unitário e total específico do aéreo
            $table->decimal('vlr_cfr_unit', 15, 2)->nullable()->after('frete_brl');
            $table->decimal('vlr_cfr_total', 15, 2)->nullable()->after('vlr_cfr_unit');
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
                'delivery_fee',
                'delivery_fee_brl',
                'collect_fee',
                'collect_fee_brl',
                'dai',
                'dai_brl',
                'dape',
                'dape_brl',
                'vlr_cfr_unit',
                'vlr_cfr_total'
            ]);
        });
    }
};
