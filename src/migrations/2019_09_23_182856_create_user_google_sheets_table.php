<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGoogleSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_google_sheets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->text('token_details');
            $table->text('sheet_folder_id')->nullable();
            $table->text('sheet_folder_name')->nullable();
            $table->text('sheet_id')->nullable();
            $table->text('sheet_name')->nullable();
            $table->boolean('is_gsheet_activated')->default(0);
            $table->string('refresh_token');
            $table->timestamps();
        });

        Schema::table('user_google_sheets', function (Blueprint $table) {            
            $table->foreign('user_id') ->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_google_sheets');
    }
}
