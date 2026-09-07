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
        Schema::table('profile_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('profile_templates', 'header_template_id')) {
                $table->unsignedInteger('header_template_id')->nullable()->after('description');
            }
            if (!Schema::hasColumn('profile_templates', 'detail_template_id')) {
                $table->unsignedInteger('detail_template_id')->nullable()->after('header_template_id');
            }
            if (!Schema::hasColumn('profile_templates', 'footer_template_id')) {
                $table->unsignedInteger('footer_template_id')->nullable()->after('detail_template_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_templates', function (Blueprint $table) {
            $table->dropColumn(['header_template_id', 'detail_template_id', 'footer_template_id']);
        });
    }
};
