<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('processo_produtos', function (Blueprint $table) {

            $table->decimal('icms_st', 1000, 7)->nullable();
        });
    }

    public function down()
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            $table->dropColumn('icms_st');
        });
    }
};
