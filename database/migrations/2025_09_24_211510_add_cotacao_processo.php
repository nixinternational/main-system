<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('taxa_dolar');

            $table->json('cotacao_moeda_processo')->nullable();
            $table->date('data_cotacao_processo')->nullable();
            $table->string('moeda_processo', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn(['cotacao_moeda_processo', 'data_cotacao_processo', 'moeda_processo']);

            $table->decimal('taxa_dolar', 15, 4)->nullable();
        });
    }
};
