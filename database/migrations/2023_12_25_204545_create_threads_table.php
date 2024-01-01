<?php

use App\Models\Threads;
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
        Schema::create('threads', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            $table->string('assistant_id')->nullable();
            $table->string("title")->default("New conversation");
            $table->string("status")->default(Threads::STATUS_ACTIVE);
            $table->foreignIdFor(User::class);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
