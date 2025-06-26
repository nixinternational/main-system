<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('nome');
            $table->string('modelo')->nullable()->after('id');
            $table->string('codigo')->nullable()->after('modelo');
            $table->string('ncm')->nullable()->after('descricao');
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['modelo', 'codigo', 'ncm']);
            $table->string('nome')->after('id');
        });
    }
};
