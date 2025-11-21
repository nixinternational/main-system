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
        Schema::table('processos', function (Blueprint $table) {
            // Campo para valor EXW (Ex Works) específico do transporte aéreo
            $table->decimal('valor_exw', 15, 2)->nullable()->after('valor_fob');
            $table->decimal('valor_exw_brl', 15, 2)->nullable()->after('valor_exw');
            // Campos para delivery_fee e collect_fee (valores base para distribuição proporcional)
            $table->decimal('delivery_fee', 15, 2)->nullable()->after('valor_exw_brl');
            $table->decimal('collect_fee', 15, 2)->nullable()->after('delivery_fee');
            // Campos DAI e DAPE
            $table->decimal('dai', 15, 2)->nullable()->after('collect_fee');
            $table->decimal('dape', 15, 2)->nullable()->after('dai');
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
            $table->dropColumn([
                'valor_exw',
                'valor_exw_brl',
                'delivery_fee',
                'collect_fee',
                'dai',
                'dape'
            ]);
        });
    }
};
