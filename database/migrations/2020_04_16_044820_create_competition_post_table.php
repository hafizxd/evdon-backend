<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('rank')->nullable();
            $table->string('name');
            $table->date('date');
            $table->string('level')->nullable();
            $table->string('place')->nullable();
            $table->string('specific_place')->nullable();
            $table->integer('participants')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('competition_posts');
    }
}
