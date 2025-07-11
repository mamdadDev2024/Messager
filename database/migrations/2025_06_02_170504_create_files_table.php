<?php

use App\Models\User;
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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedBigInteger('size');
            $table->foreignIdFor(User::class)->constrained('users')->cascadeOnDelete();
            $table->string('file_name');
            $table->boolean('processed')->default(false);
            $table->boolean('visible')->default(true);
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
