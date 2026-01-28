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
        Schema::table('processo_rodoviarios', function (Blueprint $table) {
            $table->decimal('tx_def_li', 15, 2)->nullable()->after('multa');
            $table->decimal('taxa_siscomex', 15, 2)->nullable()->after('tx_def_li');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processo_rodoviarios', function (Blueprint $table) {
            $table->dropColumn(['tx_def_li', 'taxa_siscomex']);
        });
    }
};
