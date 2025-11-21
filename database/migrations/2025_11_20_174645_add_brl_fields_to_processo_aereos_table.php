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
            $table->decimal('delivery_fee_brl', 15, 2)->nullable()->after('delivery_fee');
            $table->decimal('collect_fee_brl', 15, 2)->nullable()->after('collect_fee');
            $table->decimal('dai_brl', 15, 2)->nullable()->after('dai');
            $table->decimal('dape_brl', 15, 2)->nullable()->after('dape');
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
            $table->dropColumn(['delivery_fee_brl', 'collect_fee_brl', 'dai_brl', 'dape_brl']);
        });
    }
};
