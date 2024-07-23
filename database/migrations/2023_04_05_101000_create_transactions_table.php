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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bank_account_id');
                $table->decimal('amount', 15, 2);
                $table->enum('transaction_type', ['Deposit', 'Withdrawal', 'Transfer']);
                $table->text('description')->nullable();
                $table->timestamp('transaction_date')->useCurrent();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
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
        if (Schema::hasTable('transactions')) {
            Schema::dropIfExists('transactions');
        }
    }
};
