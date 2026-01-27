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
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->decimal('tx_def_li', 15, 2)->nullable()->after('multa');
            $table->decimal('taxa_siscomex', 15, 2)->nullable()->after('tx_def_li');
            $table->decimal('rep_itj', 15, 2)->nullable()->after('dape');
            $table->decimal('frete_nvg_x_gyn', 15, 2)->nullable()->after('rep_itj');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->dropColumn([
                'tx_def_li',
                'taxa_siscomex',
                'rep_itj',
                'frete_nvg_x_gyn'
            ]);
        });
    }
};
