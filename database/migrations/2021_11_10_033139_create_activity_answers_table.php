<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->text('content');
            $table->string('file')->nullable();
            $table->integer('score')->nullable();
            $table->unsignedBigInteger('scored_by')->nullable();
            $table->timestamps();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('scored_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_answers');
    }
}
