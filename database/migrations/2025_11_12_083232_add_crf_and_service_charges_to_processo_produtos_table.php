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
            $table->decimal('vlr_crf_total', 15, 7)->nullable()->after('acresc_frete_brl');
            $table->decimal('vlr_crf_unit', 15, 7)->nullable()->after('vlr_crf_total');
            $table->decimal('service_charges', 15, 7)->nullable()->after('vlr_crf_unit');
            $table->decimal('service_charges_brl', 15, 7)->nullable()->after('service_charges');
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
            $table->dropColumn(['vlr_crf_total', 'vlr_crf_unit', 'service_charges', 'service_charges_brl']);
        });
    }
};
