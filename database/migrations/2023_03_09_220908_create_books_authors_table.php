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
        Schema::create('books_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('books_id')->constrained('books');
            $table->foreignId('authors_id')->constrained('authors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_authors');
    }
};
