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
        Schema::create('threads_messages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string("threads_id");
            $table->string("role");
            $table->longText("content");
            $table->string("assistant_id")->nullable();
            $table->string("run_id")->nullable();
            $table->timestamps();

            $table->foreign('threads_id')->references('id')->on('threads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads_messages');
    }
};
