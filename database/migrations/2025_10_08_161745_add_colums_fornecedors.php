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
        Schema::table('fornecedors', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->nullable()->after('id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedBigInteger('fornecedor_id')->nullable()->after('id');
            $table->foreign('fornecedor_id')->references('id')->on('fornecedors');
        });
    }

    public function down()
    {
        Schema::table('fornecedors', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
        });
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropForeign(['fornecedor_id']);
            $table->dropColumn('fornecedor_id');
        });
    }
};
