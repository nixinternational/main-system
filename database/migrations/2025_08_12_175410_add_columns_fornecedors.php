<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fornecedors', function (Blueprint $table) {

            $table->string('nome_contato')->nullable();
            $table->string('email_contato')->nullable();
            $table->string('telefone_contato')->nullable();
        });
    }

    public function down()
    {
        Schema::table('fornecedors', function (Blueprint $table) {
            $table->dropColumn('nome_contato');
            $table->dropColumn('email_contato');
            $table->dropColumn('telefone_contato');
        });
    }
};
