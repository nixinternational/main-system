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
            $table->decimal('thc_capatazia', 15, 2)->nullable()->after('honorarios_nix');
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
            $table->dropColumn('thc_capatazia');
        });
    }
};
