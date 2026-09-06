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
        if (!Schema::hasTable('sysfield_ex')) {
            Schema::create('sysfield_ex', function (Blueprint $table) {
                $table->increments('recno');
                $table->string('tablename', 70)->nullable()->index('tablename');
                $table->integer('field_number')->nullable()->index('field_number');
                $table->string('field_name', 70)->nullable()->index('field_name');
                $table->string('field_comment', 125)->nullable();
                $table->string('field_type', 15)->nullable();
                $table->integer('field_length')->nullable();
                $table->integer('decimal_point')->default(0);
                $table->char('list_show', 1)->default('N');
                $table->integer('list_num')->nullable();
                $table->char('app_show', 1)->default('N');
                $table->string('app_page', 70)->nullable();
                $table->string('app_compo', 15)->nullable();
                $table->string('app_var', 125)->nullable();
                $table->string('app_showe', 70)->nullable();
                $table->string('app_showt', 512)->nullable();
                $table->text('des1')->nullable();
                $table->char('hint_show', 1)->default('N');
                $table->integer('app_shownum')->nullable();
                $table->char('xml_show', 1)->default('N');
                $table->integer('xml_num')->nullable();
                $table->string('xml_tac1', 255)->nullable();
                $table->string('xml_tac2', 255)->default('|');
                $table->char('flatfile_show', 1)->default('N');
                $table->unsignedInteger('flatfile_num')->nullable();
                $table->char('status', 1)->nullable();
                $table->string('print1', 35)->nullable();
                $table->string('print2', 35)->nullable();
                $table->string('default1', 225)->nullable();
                $table->string('print3', 35)->nullable();
                $table->string('print4', 35)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysfield_ex');
    }
};
