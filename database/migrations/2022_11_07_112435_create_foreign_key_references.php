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
        Schema::table('watchlists', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('event_id')->references('id')->on('events');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('artist_event', function (Blueprint $table) {
            $table->foreign('artist_id')->references('id')->on('artists');
            $table->foreign('event_id')->references('id')->on('events');
        });

        Schema::table('artist_tag', function (Blueprint $table) {
            $table->foreign('artist_id')->references('id')->on('artists');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }
};
