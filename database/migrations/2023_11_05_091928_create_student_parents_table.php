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
        Schema::create('student_parents', function (Blueprint $table) {
            $table->bigIncrements('id');
            //complete name, phones, dui e informacion del encargado
            $table->string('parent_data')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('civil_marriage_date')->nullable();
            $table->string('religious_marriage_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('are_together')->nullable();
            $table->string('address')->nullable();
            $table->string('house_type')->nullable();
            //remesas
            $table->string('consignment')->nullable();
            $table->string('family_group')->nullable();
            $table->string('address')->nullable();

            //Global scopes
            $table->date('date_scope')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
