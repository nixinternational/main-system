<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs_clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 50)->index();
            $table->string('auditable_type', 191);
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->string('process_type', 20)->nullable()->index();
            $table->unsignedBigInteger('process_id')->nullable()->index();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->json('changed_fields')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedInteger('changed_fields_count')->default(0);
            $table->string('context', 100)->nullable()->index();
            $table->string('request_id', 100)->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs_catalogos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 50)->index();
            $table->string('auditable_type', 191);
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->string('process_type', 20)->nullable()->index();
            $table->unsignedBigInteger('process_id')->nullable()->index();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->json('changed_fields')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedInteger('changed_fields_count')->default(0);
            $table->string('context', 100)->nullable()->index();
            $table->string('request_id', 100)->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url', 2048)->nullable();
            $table->timestamps();
        });

        $columns = 'id, user_id, action, auditable_type, auditable_id, process_type, process_id, client_id, changed_fields, old_values, new_values, changed_fields_count, context, request_id, ip, user_agent, url, created_at, updated_at';

        DB::statement("
            INSERT INTO audit_logs_clientes ($columns)
            SELECT $columns
            FROM audit_logs
            WHERE process_type = 'cliente'
               OR auditable_type IN (
                   'App\\\\Models\\\\Cliente',
                   'App\\\\Models\\\\ClienteDocumento',
                   'App\\\\Models\\\\BancoCliente',
                   'App\\\\Models\\\\ClienteEmail',
                   'App\\\\Models\\\\ClienteResponsavelProcesso',
                   'App\\\\Models\\\\ClienteAduana'
               )
        ");
        DB::statement("
            DELETE FROM audit_logs
            WHERE process_type = 'cliente'
               OR auditable_type IN (
                   'App\\\\Models\\\\Cliente',
                   'App\\\\Models\\\\ClienteDocumento',
                   'App\\\\Models\\\\BancoCliente',
                   'App\\\\Models\\\\ClienteEmail',
                   'App\\\\Models\\\\ClienteResponsavelProcesso',
                   'App\\\\Models\\\\ClienteAduana'
               )
        ");

        DB::statement("
            INSERT INTO audit_logs_catalogos ($columns)
            SELECT $columns
            FROM audit_logs
            WHERE process_type = 'catalogo'
               OR auditable_type IN (
                   'App\\\\Models\\\\Catalogo',
                   'App\\\\Models\\\\Produto'
               )
        ");
        DB::statement("
            DELETE FROM audit_logs
            WHERE process_type = 'catalogo'
               OR auditable_type IN (
                   'App\\\\Models\\\\Catalogo',
                   'App\\\\Models\\\\Produto'
               )
        ");

        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('audit_logs_clientes', 'id'),
                COALESCE((SELECT MAX(id) FROM audit_logs_clientes), 1),
                true
            )
        ");
        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('audit_logs_catalogos', 'id'),
                COALESCE((SELECT MAX(id) FROM audit_logs_catalogos), 1),
                true
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs_clientes');
        Schema::dropIfExists('audit_logs_catalogos');
    }
};
