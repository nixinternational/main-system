<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->string('frete_internacional_moeda')->nullable();
            $table->string('seguro_internacional_moeda')->nullable();
            $table->string('acrescimo_frete_moeda')->nullable();
            $table->string('descricao')->max(120)->nullable();
            $table->string('status')->max(120)->nullable();
            $table->string('canal')->max(120)->nullable();
            $table->string('data_desembaraco_inicio')->nullable();
            $table->string('data_desembaraco_fim')->nullable();

            $table->decimal('outras_taxas_agente')->nullable();
            $table->decimal('liberacao_bl')->nullable();
            $table->decimal('desconsolidacao')->nullable();
            $table->decimal('isps_code')->nullable();
            $table->decimal('handling',)->nullable();
            $table->decimal('capatazia')->nullable();
            $table->decimal('afrmm',)->nullable();
            $table->decimal('armazenagem_sts')->nullable();
            $table->decimal('frete_dta_sts_ana')->nullable();
            $table->decimal('sda')->nullable();
            $table->decimal('rep_sts')->nullable();
            $table->decimal('armaz_ana')->nullable();
            $table->decimal('lavagem_container')->nullable();
            $table->decimal('rep_anapolis')->nullable();
            $table->decimal('li_dta_honor_nix')->nullable();
            $table->decimal('honorarios_nix')->nullable();
        });
    }

    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('frete_internacional_moeda');
            $table->dropColumn('seguro_internacional_moeda');
            $table->dropColumn('acrescimo_frete_moeda');
            $table->dropColumn('descricao');
            $table->dropColumn('status');
            $table->dropColumn('canal');
            $table->dropColumn('data_desembaraco_inicio');
            $table->dropColumn('data_desembaraco_fim');
            $table->dropColumn('outras_taxas_agente');
            $table->dropColumn('liberacao_bl');
            $table->dropColumn('desconsolidacao');
            $table->dropColumn('isps_code');
            $table->dropColumn('handling',);
            $table->dropColumn('capatazia');
            $table->dropColumn('afrmm',);
            $table->dropColumn('armazenagem_sts');
            $table->dropColumn('frete_dta_sts_ana');
            $table->dropColumn('sda');
            $table->dropColumn('rep_sts');
            $table->dropColumn('armaz_ana');
            $table->dropColumn('lavagem_container');
            $table->dropColumn('rep_anapolis');
            $table->dropColumn('li_dta_honor_nix');
            $table->dropColumn('honorarios_nix');
        });
    }
};
