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
        if (!Schema::hasTable('financial_data')) {
            Schema::create('financial_data', function (Blueprint $table) {
                $table->id();
                $table->string('account_name');
                $table->decimal('balance', 15, 2); // Assuming a large balance amount with 2 decimal places
                $table->decimal('revenue', 15, 2)->default(0.00);
                $table->decimal('expenses', 15, 2)->default(0.00);
                $table->string('currency', 10)->default('USD');
                $table->enum('status', ['active', 'inactive'])->default('active');
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
        if (Schema::hasTable('financial_data')) {
            Schema::dropIfExists('financial_data');
        }
    }
};
