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
        Schema::create('detail_template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('detail_template_id');
            $table->string('cell_id', 50);
            $table->string('field_name', 255);
            $table->string('custom_text', 255)->nullable();
            $table->unsignedInteger('seq')->default(1);
            $table->timestamps();

            $table->foreign('detail_template_id')
                  ->references('detail_template_id')
                  ->on('detail_templates')
                  ->onDelete('cascade');

            $table->index(['detail_template_id', 'cell_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_template_items');
    }
};
