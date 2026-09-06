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
        if (!Schema::hasTable('profile_templates')) {
            Schema::create('profile_templates', function (Blueprint $table) {
                $table->increments('profile_template_id');
                $table->string('profile_id', 5)->nullable();
                $table->string('profile_template_name', 255);
                $table->string('description', 255)->nullable();
                $table->boolean('status')->nullable();
                $table->integer('created_by')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->integer('updated_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->integer('deleted_by')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_templates');
    }
};
