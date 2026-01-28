<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
