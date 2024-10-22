<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('n_rendicion')->nullable();
            $table->string('tipo')->nullable();
            $table->string('estado')->nullable();
            $table->string('entidad_bancaria')->nullable();
            $table->string('cuenta')->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('total', 30, 2)->nullable();
            $table->decimal('sist_reg', 30, 2)->nullable();
            $table->decimal('safit', 30, 2)->nullable();
            $table->decimal('cenat', 30, 2)->nullable();
            $table->decimal('ci', 30, 2)->nullable();
            $table->decimal('pv', 30, 2)->nullable();
            $table->decimal('trgs', 30, 2)->nullable();
            $table->decimal('trgs_pv', 30, 2)->nullable();
            $table->decimal('sirto', 30, 2)->nullable();
            $table->decimal('renatedu', 30, 2)->nullable();
            $table->date('fecha_rendicion')->nullable();
            $table->date('fecha_registro')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safits');
    }
};
