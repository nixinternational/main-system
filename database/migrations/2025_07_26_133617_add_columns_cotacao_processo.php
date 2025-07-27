<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::table('processos', function (Blueprint $table) {

        $table->decimal('cotacao_frete_internacional', 15, 5)->nullable();
        $table->decimal('cotacao_seguro_internacional', 15, 5)->nullable();
        $table->decimal('cotacao_acrescimo_frete', 15, 5)->nullable();


        });
    }

    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('cotacao_frete_internacional');
            $table->dropColumn('cotacao_seguro_internacional');
            $table->dropColumn('cotacao_acrescimo_frete');
        });
    }
};
