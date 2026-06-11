<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
