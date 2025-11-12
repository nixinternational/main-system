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
            $table->decimal('service_charges_moeda_estrangeira', 15, 7)->nullable()->after('service_charges_brl');
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
            $table->dropColumn('service_charges_moeda_estrangeira');
        });
    }
};
