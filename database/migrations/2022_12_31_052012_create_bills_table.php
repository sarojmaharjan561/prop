<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id');
            $table->dateTime('date', $precision = 0);
            $table->dateTime('paid_date', $precision = 0);
            $table->string('shop_name');
            $table->decimal('sub_total', 10, 4);
            $table->decimal('discount', 10, 4);
            $table->decimal('total_amount', 10, 4);
            $table->text('description')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
