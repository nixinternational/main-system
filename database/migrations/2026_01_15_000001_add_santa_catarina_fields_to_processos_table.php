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
            $table->decimal('multa_complem', 15, 7)->nullable()->after('multa');
            $table->decimal('dif_impostos', 15, 7)->nullable()->after('multa_complem');
            $table->decimal('frete_rodoviario', 15, 7)->nullable()->after('armazenagem_sts');
            $table->decimal('dif_frete_rodoviario', 15, 7)->nullable()->after('frete_rodoviario');
            $table->decimal('armazenagem_porto', 15, 7)->nullable()->after('armazenagem_sts');
            $table->decimal('rep_porto', 15, 7)->nullable()->after('rep_sts');
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
                'multa_complem',
                'dif_impostos',
                'frete_rodoviario',
                'dif_frete_rodoviario',
                'armazenagem_porto',
                'rep_porto'
            ]);
        });
    }
};
