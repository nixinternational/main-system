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
            $table->string('service_charges_moeda', 3)->nullable()->after('service_charges');
            $table->decimal('service_charges_usd', 15, 7)->nullable()->after('service_charges_moeda');
            $table->decimal('service_charges_brl', 15, 7)->nullable()->after('service_charges_usd');
            $table->decimal('cotacao_service_charges', 15, 4)->nullable()->after('service_charges_brl');
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
            $table->dropColumn(['service_charges_moeda', 'service_charges_usd', 'service_charges_brl', 'cotacao_service_charges']);
        });
    }
};
