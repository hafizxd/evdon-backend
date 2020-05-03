<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_posts', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('name');
            $table->date('date');
            $table->string('organizer')->nullable();
            $table->string('place')->nullable();
            $table->string('important_people')->nullable();
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
        Schema::dropIfExists('event_posts');
    }
}
