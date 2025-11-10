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
        Schema::table('processos', function (Blueprint $table) {
            $table->decimal('frete_internacional_usd', 15, 2)->nullable()->after('frete_internacional');
            $table->decimal('frete_internacional_brl', 15, 2)->nullable()->after('frete_internacional_usd');
            $table->decimal('seguro_internacional_usd', 15, 2)->nullable()->after('seguro_internacional');
            $table->decimal('seguro_internacional_brl', 15, 2)->nullable()->after('seguro_internacional_usd');
            $table->decimal('acrescimo_frete_usd', 15, 2)->nullable()->after('acrescimo_frete');
            $table->decimal('acrescimo_frete_brl', 15, 2)->nullable()->after('acrescimo_frete_usd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn([
                'frete_internacional_usd',
                'frete_internacional_brl',
                'seguro_internacional_usd',
                'seguro_internacional_brl',
                'acrescimo_frete_usd',
                'acrescimo_frete_brl'
            ]);
        });
    }
};
