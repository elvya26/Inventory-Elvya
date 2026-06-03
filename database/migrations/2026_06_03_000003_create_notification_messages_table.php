<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('recipient', 160);
            $table->string('channel', 30);
            $table->text('message');
            $table->string('status', 30)->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
