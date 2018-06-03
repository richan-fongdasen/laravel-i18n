<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_category_id');
            $table->string('locale', 8);
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->foreign('product_category_id')
                  ->references('id')->on('product_categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_category_translations');
    }
}
