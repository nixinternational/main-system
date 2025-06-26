<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cliente_documentos', function (Blueprint $table) {
            $table->dropForeign(['tipo_documento_id']);
            $table->dropColumn('tipo_documento_id');
            $table->string('tipo_documento')->nullable()->after('cliente_id');
        });
        Schema::table('catalogos', function (Blueprint $table) {
            $table->dropColumn('nome');
        });
    }

    public function down()
    {
        Schema::table('cliente_documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_documento_id')->after('cliente_id');
            $table->foreign('tipo_documento_id')->references('id')->on('tipo_documentos');
            $table->dropColumn('tipo_documento');
        });
    }
};
