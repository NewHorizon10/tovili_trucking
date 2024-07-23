<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Team name
                $table->string('description')->nullable(); // Optional description of the team
                $table->unsignedBigInteger('leader_id')->nullable(); // Optional reference to a team leader
                $table->boolean('is_active')->default(true); // Status of the team
                $table->timestamps();

                // Foreign key constraint for leader_id if necessary
                $table->foreign('leader_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, drop the table if it exists
        if (Schema::hasTable('teams')) {
            Schema::dropIfExists('teams');
        }
    }
};
