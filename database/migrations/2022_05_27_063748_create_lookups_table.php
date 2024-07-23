<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        if (!Schema::hasTable('lookups')) {
            Schema::create('lookups', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('lookup_type');
                $table->boolean('is_active')->default(true); // Changed to boolean
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
        // Optionally, you might want to drop the table if it exists
        if (Schema::hasTable('lookups')) {
            Schema::dropIfExists('lookups');
        }
    }
};
