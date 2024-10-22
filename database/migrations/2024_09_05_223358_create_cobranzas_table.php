<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobranzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('sistema_origen')->nullable();
            $table->string('id_tramite')->nullable();
            $table->string('estado_tramite')->nullable();
            $table->string('fecha_tramite')->nullable();
            $table->decimal('importe', 30, 2)->nullable();
            $table->string('rendicion')->nullable();
            $table->string('tipo')->nullable();
            $table->string('entidad_bancaria')->nullable();
            $table->string('estado_pago')->nullable();
            $table->decimal('monto_pagado', 30, 2)->nullable();
            $table->string('fecha_pago')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sugits');
    }
};