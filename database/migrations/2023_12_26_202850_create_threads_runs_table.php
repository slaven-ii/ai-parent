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
        Schema::create('threads_runs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('threads_id');
            $table->string('assistant_id');
            $table->string('status');
            $table->string("required_action")->nullable();
            $table->string("last_error")->nullable();
            $table->integer("expires_at");
            $table->integer("started_at")->nullable();
            $table->integer("cancelled_at")->nullable();
            $table->integer("failed_at")->nullable();
            $table->integer("completed_at")->nullable();
            $table->string('model');
            $table->text('instructions');
            $table->timestamps();

            $table->foreign('threads_id')->references('id')->on('threads');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads_runs');
    }
};
