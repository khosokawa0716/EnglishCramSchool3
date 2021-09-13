<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WordQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('word_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id');
            // onDelete('cascade')を指定することで、groupsのレコードが削除されたときに紐づいているレコードを削除する
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->string('japanese');
            $table->string('choice1');
            $table->string('choice2');
            $table->string('choice3');
            $table->integer('answer');
            $table->boolean('delete_flg')->default(false);
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
        Schema::dropIfExists('word_questions');
    }
}
