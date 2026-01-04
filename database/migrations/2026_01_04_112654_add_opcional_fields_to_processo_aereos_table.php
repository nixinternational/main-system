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
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->decimal('opcional_1_valor', 15, 2)->nullable()->after('honorarios_nix');
            $table->string('opcional_1_descricao', 255)->nullable()->after('opcional_1_valor');
            $table->boolean('opcional_1_compoe_despesas')->default(false)->after('opcional_1_descricao');
            $table->decimal('opcional_2_valor', 15, 2)->nullable()->after('opcional_1_compoe_despesas');
            $table->string('opcional_2_descricao', 255)->nullable()->after('opcional_2_valor');
            $table->boolean('opcional_2_compoe_despesas')->default(false)->after('opcional_2_descricao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processo_aereos', function (Blueprint $table) {
            $table->dropColumn([
                'opcional_1_valor',
                'opcional_1_descricao',
                'opcional_1_compoe_despesas',
                'opcional_2_valor',
                'opcional_2_descricao',
                'opcional_2_compoe_despesas'
            ]);
        });
    }
};
