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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->string('prenom', 255);
            $table->string('sexe', 10);
            $table->date('date_naissance');
            $table->string('telephone', 20);
            $table->string('nom_utilisateur', 255)->unique();
            $table->string('mot_de_passe', 255);
            $table->string('photo_de_profile', 255);
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
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
        Schema::dropIfExists('users');
    }
};
