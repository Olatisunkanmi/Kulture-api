<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->string('referral_code')->unique();
            $table->integer('no_of_referrals')->default(0);
            $table->timestamps();
    
            // Define foreign keys
            $table->ulid('referred_by')->references('id')->on('users')->onDelete('cascade');
            $table->ulid('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    
        
    }
    

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
