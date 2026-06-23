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
        Schema::create('header_template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('header_template_id');
            $table->string('cell_id', 50);
            $table->string('field_name', 255);
            $table->unsignedInteger('seq')->default(1);
            $table->timestamps();

            $table->foreign('header_template_id')
                  ->references('header_template_id')
                  ->on('header_templates')
                  ->onDelete('cascade');

            $table->index(['header_template_id', 'cell_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_template_items');
    }
};
