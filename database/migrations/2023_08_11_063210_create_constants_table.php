<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bankrates', function (Blueprint $table) {
            $table->id();
            $table->string('rate'); // Use lowercase 'string' instead of 'String'
            $table->decimal('value', 10, 2); // Specify total digits (10) and decimal places (2)
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
        Schema::dropIfExists('constants');
    }
}
