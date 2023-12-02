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
       Schema::create('messages', function (Blueprint $table) {
           $table->id();
           $table->integer('sender');
           $table->integer('reciver');
           $table->text('message');
           $table->string('subject');
           $table->integer('recieved_flag')->nullable();
           $table->timestamps();
       });
   }


   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
       Schema::dropIfExists('mesaages');
   }
};
