<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendicions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('archivo')->nullable();
            $table->string('id_tramite')->nullable();
            $table->string('estado_tramite')->nullable();
            $table->string('fecha_tramite')->nullable();
            $table->string('dominio')->nullable();
            $table->string('acta')->nullable();
            $table->string('codigo')->nullable();
            $table->decimal('importe', 30, 2)->nullable();
            $table->string('id_comprobante')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendicions');
    }
};
