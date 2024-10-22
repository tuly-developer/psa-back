<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('htms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('archivo')->nullable();
            $table->string('dependencia')->nullable();
            $table->string('fecha')->nullable();
            $table->string('tercero')->nullable();
            $table->string('periodo_rendicion_inicio')->nullable();
            $table->string('periodo_rendicion_fin')->nullable();
            $table->string('numero_de_rendicion')->nullable();
            $table->string('fecha_hora_proceso')->nullable();
            $table->string('banco')->nullable();
            $table->string('numero_cuenta_bancaria')->nullable();
            $table->string('detalle_cuenta_bancaria')->nullable();
            $table->string('cbu')->nullable();
            $table->string('cuit')->nullable();
            $table->string('municipalidad_provincia')->nullable();
            $table->decimal('total_infracciones', 30, 2)->nullable();
            $table->decimal('gastos_bancarios_recaudadora', 30, 2)->nullable();
            $table->decimal('subtotal', 30, 2)->nullable();
            $table->string('lugar_nombre')->nullable();
            $table->decimal('lugar_porcentaje', 5, 2)->nullable();
            $table->decimal('lugar_total', 30, 2)->nullable();
            $table->decimal('acara_porcentaje', 5, 2)->nullable();
            $table->decimal('acara_total', 30, 2)->nullable();
            $table->decimal('ente_cooperador_sugit_porcentaje', 5, 2)->nullable();
            $table->decimal('ente_cooperador_sugit_total', 30, 2)->nullable();
            $table->decimal('ente_cooperador_porcentaje', 5, 2)->nullable();
            $table->decimal('ente_cooperador_total', 30, 2)->nullable();
            $table->decimal('total_depositado', 30, 2)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('htms');
    }
};
