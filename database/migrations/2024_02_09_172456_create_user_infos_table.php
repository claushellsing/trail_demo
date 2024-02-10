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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->text('address');
            $table->string('country');
            $table->string('city');
            $table->date('date_of_birth');
            $table->boolean('is_married');
            $table->date('date_of_marriage')->nullable();
            $table->string('country_of_marriage')->nullable();
            $table->boolean('is_widowed')->nullable();
            $table->boolean('has_been_married')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
