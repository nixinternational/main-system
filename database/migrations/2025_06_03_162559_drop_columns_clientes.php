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
            $table->dropColumn('credenciamento_radar_final');
            $table->dropColumn('marinha_mercante_final');
            $table->dropColumn('afrmm_bb_final');
            $table->dropColumn('afrmm_bb_inicial');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->addColumn('boolean','afrmm_bb')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
