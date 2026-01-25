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
            $table->decimal('frete_sts_cgb', 15, 7)->nullable()->after('armazenagem_sts');
            $table->decimal('diarias', 15, 7)->nullable()->after('frete_sts_cgb');
            $table->decimal('armaz_cgb', 15, 7)->nullable()->after('rep_sts');
            $table->decimal('rep_cgb', 15, 7)->nullable()->after('armaz_cgb');
            $table->decimal('demurrage', 15, 7)->nullable()->after('rep_cgb');
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
                'frete_sts_cgb',
                'diarias',
                'armaz_cgb',
                'rep_cgb',
                'demurrage'
            ]);
        });
    }
};
