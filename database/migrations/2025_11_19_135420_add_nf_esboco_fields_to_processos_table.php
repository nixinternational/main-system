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
            $table->foreignId('fornecedor_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('fornecedors')
                ->nullOnDelete();

            $table->string('transportadora_nome')
                ->nullable()
                ->after('fornecedor_id');

            $table->string('transportadora_endereco')
                ->nullable()
                ->after('transportadora_nome');

            $table->string('transportadora_municipio')
                ->nullable()
                ->after('transportadora_endereco');

            $table->string('transportadora_cnpj', 32)
                ->nullable()
                ->after('transportadora_municipio');

            $table->text('info_complementar_nf')
                ->nullable()
                ->after('transportadora_cnpj');
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
            if (Schema::hasColumn('processos', 'fornecedor_id')) {
                $table->dropForeign(['fornecedor_id']);
            }

            $table->dropColumn([
                'fornecedor_id',
                'transportadora_nome',
                'transportadora_endereco',
                'transportadora_municipio',
                'transportadora_cnpj',
                'info_complementar_nf',
            ]);
        });
    }
};
