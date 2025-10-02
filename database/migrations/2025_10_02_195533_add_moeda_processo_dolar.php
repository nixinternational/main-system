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
            $table->decimal('moeda_processo_dolar', 15, 4)->nullable();
        });
    }

    public function down()
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            $table->dropColumn(['moeda_processo_dolar' ]);
        });
    }
};
