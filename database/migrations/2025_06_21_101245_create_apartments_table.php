<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // definisanje kolona u tabeli baze podataka
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ko je vlasnik apartmana
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->decimal('price_per_night', 8, 2);
            $table->integer('number_of_beds');
            $table->string('image')->nullable(); // Za URL slike
            $table->timestamps();
        });
    }
    // ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
