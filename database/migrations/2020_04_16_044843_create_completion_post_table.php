<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletionPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completion_posts', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('object');
            $table->date('date');
            $table->string('title')->nullable();
            $table->string('place')->nullable();
            $table->string('link')->nullable();
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->unsignedBigInteger('post_id');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('completion_posts');
    }
}
