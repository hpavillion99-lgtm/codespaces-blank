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
    Schema::create('media', function (Blueprint $table) {
        $table->id();
        $table->string('name');          // Original name of the file (e.g., photo.jpg)
        $table->string('file_path');     // Where it's saved inside the storage folder
        $table->string('file_type');     // The extension or MIME type (e.g., image/jpeg, pdf)
        $table->bigInteger('file_size'); // Size in bytes
        $table->timestamps();            // created_at and updated_at timestamps
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
