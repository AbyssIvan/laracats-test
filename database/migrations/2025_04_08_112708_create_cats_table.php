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
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('gender', 10);
            $table->integer('mother_id')->nullable();
            $table->timestamps();
            
            $table->foreign('mother_id')
                ->references('id')
                ->on('cats')
                ->onDelete('set null');
        });

        Schema::create('cat_fathers', function (Blueprint $table) {
            $table->id();
            $table->integer('cat');
            $table->integer('father');

            $table->primary('id');
            
            $table->foreign('cat')
                ->references('id')
                ->on('cats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            
            $table->foreign('father')
                ->references('id')
                ->on('cats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_fathers', function (Blueprint $table) {
            $table->dropForeign(['cat']);
            $table->dropForeign(['father']);
        });
        Schema::dropIfExists('cat_fathers');
        Schema::dropIfExists('cats');
    }
};
