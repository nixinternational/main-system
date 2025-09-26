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
            $table->decimal('fob_unit_moeda_estrangeira', 15, 7)->nullable()->after('fob_unit_usd');
            $table->decimal('fob_total_moeda_estrangeira', 15, 7)->nullable()->after('fob_total_usd');
        });
    }

    public function down()
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            $table->dropColumn(['fob_unit_moeda_estrangeira', 'fob_total_moeda_estrangeira']);
        });
    }
};
