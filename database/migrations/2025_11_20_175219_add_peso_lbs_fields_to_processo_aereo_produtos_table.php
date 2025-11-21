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
            $table->decimal('peso_liq_lbs', 15, 6)->nullable()->after('quantidade');
            $table->decimal('peso_liq_total_kg', 15, 6)->nullable()->after('peso_liquido_total');
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
            $table->dropColumn(['peso_liq_lbs', 'peso_liq_total_kg']);
        });
    }
};
