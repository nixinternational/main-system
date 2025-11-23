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
            $table->decimal('peso_liq_kg', 15, 6)->nullable()->after('peso_liq_lbs');
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
            $table->dropColumn('peso_liq_kg');
        });
    }
};
