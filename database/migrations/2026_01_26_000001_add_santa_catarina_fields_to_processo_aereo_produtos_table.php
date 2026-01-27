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
            $table->decimal('rep_itj', 15, 2)->nullable()->after('dape_brl');
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
        Schema::table('processo_aereo_produtos', function (Blueprint $table) {
            $table->dropColumn([
                'rep_itj',
                'frete_nvg_x_gyn'
            ]);
        });
    }
};
