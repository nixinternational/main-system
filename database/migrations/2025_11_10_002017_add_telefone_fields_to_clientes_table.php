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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('telefone_fixo_responsavel_legal')->nullable()->after('telefone_responsavel_legal');
            $table->string('telefone_celular_responsavel_legal')->nullable()->after('telefone_fixo_responsavel_legal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['telefone_fixo_responsavel_legal', 'telefone_celular_responsavel_legal']);
        });
    }
};
