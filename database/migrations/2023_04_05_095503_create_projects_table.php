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
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('project_name', 200);
                $table->string('project_size', 200);
                $table->string('location', 300);
                $table->string('property_dimension', 300);
                $table->text('description');
                $table->text('project_terms');
                $table->enum('verification_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->enum('deleted', ['No', 'Yes'])->default('No');
                $table->timestamps();
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
        if (Schema::hasTable('projects')) {
            Schema::dropIfExists('projects');
        }
    }
};
