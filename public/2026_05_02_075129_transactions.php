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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->char('num_factur', 10)
                ->unique()
                ->default('NF-0001');
            $table->string('name_customer');
            $table->string('no_telephone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('recipient')->nullable();
            $table->foreignId('id_products')->constrained('products')->onDelete('restrict');
            $table->integer('total_item')->default(1);
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};