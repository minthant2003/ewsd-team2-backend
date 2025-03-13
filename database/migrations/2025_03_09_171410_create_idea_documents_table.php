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
        Schema::create('idea_documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('public_file_url');
            $table->foreignId('idea_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea_documents');
    }
};
