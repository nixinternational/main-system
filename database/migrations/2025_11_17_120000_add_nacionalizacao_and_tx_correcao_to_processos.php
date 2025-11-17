<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            if (!Schema::hasColumn('processos', 'nacionalizacao')) {
                $table->string('nacionalizacao')
                    ->default('outros')
                    ->after('status');
            }

            if (!Schema::hasColumn('processos', 'tx_correcao_lacre')) {
                $table->decimal('tx_correcao_lacre', 15, 2)
                    ->nullable()
                    ->after('capatazia');
            }
        });

        Schema::table('processo_produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('processo_produtos', 'tx_correcao_lacre')) {
                $table->decimal('tx_correcao_lacre', 15, 7)
                    ->nullable()
                    ->after('capatazia');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            if (Schema::hasColumn('processo_produtos', 'tx_correcao_lacre')) {
                $table->dropColumn('tx_correcao_lacre');
            }
        });

        Schema::table('processos', function (Blueprint $table) {
            if (Schema::hasColumn('processos', 'tx_correcao_lacre')) {
                $table->dropColumn('tx_correcao_lacre');
            }

            if (Schema::hasColumn('processos', 'nacionalizacao')) {
                $table->dropColumn('nacionalizacao');
            }
        });
    }
};

