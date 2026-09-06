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
        if (!Schema::hasTable('application_partners')) {
            Schema::create('application_partners', function (Blueprint $table) {
                $table->string('application_id', 4)->primary();
                $table->string('application_name', 255)->nullable();
                $table->string('application_code', 255)->nullable();
                $table->string('token', 500)->nullable();
                $table->boolean('status')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_partners');
    }
};
