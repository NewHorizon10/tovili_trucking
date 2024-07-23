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
        if (!Schema::hasTable('project_investments')) {
            Schema::create('project_investments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('investor_id');
                $table->decimal('amount', 15, 2);
                $table->enum('investment_type', ['Equity', 'Debt', 'Convertible']);
                $table->text('description')->nullable();
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                $table->foreign('investor_id')->references('id')->on('investors')->onDelete('cascade');
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
        if (Schema::hasTable('project_investments')) {
            Schema::dropIfExists('project_investments');
        }
    }
};
