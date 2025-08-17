<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::table('processos', function (Blueprint $table) {

        $table->date('data_moeda_frete_internacional')->nullable();
        $table->date('data_moeda_seguro_internacional')->nullable();
        $table->date('data_moeda_acrescimo_frete')->nullable();


        });
    }

    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('data_moeda_frete_internacional');
            $table->dropColumn('data_moeda_seguro_internacional');
            $table->dropColumn('data_moeda_acrescimo_frete');
        });
    }
};
