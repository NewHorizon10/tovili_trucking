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
        // Check if the designations table exists
        if (!Schema::hasTable('designations')) {
            Schema::create('designations', function (Blueprint $table) {
                $table->id();
                $table->integer('department_id')->nullable();
                $table->integer('employer_id')->nullable();
                $table->integer('activity_by')->nullable();
                $table->string('name')->nullable();
                $table->integer('is_active')->default(1); // Changed default to integer value
                $table->integer('is_deleted')->default(0); // Changed default to integer value
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
        // Check if the designations table exists before attempting to drop it
        if (Schema::hasTable('designations')) {
            Schema::dropIfExists('designations');
        }
    }
};
