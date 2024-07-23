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
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_number')->unique();
                $table->string('account_holder_name');
                $table->string('bank_name');
                $table->string('branch_name')->nullable();
                $table->decimal('balance', 15, 2)->default(0.00);
                $table->enum('account_type', ['Checking', 'Savings', 'Business'])->default('Checking');
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
        if (Schema::hasTable('bank_accounts')) {
            Schema::dropIfExists('bank_accounts');
        }
    }
};
