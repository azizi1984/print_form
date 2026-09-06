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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Adjust name column if present from default Laravel migration
                if (Schema::hasColumn('users', 'name')) {
                    $table->string('name')->nullable()->change();
                }

                // Adjust email column to nullable to match actual database
                if (Schema::hasColumn('users', 'email')) {
                    $table->string('email', 255)->nullable()->change();
                }

                if (!Schema::hasColumn('users', 'profile_id')) {
                    $table->string('profile_id', 5)->nullable()->after('id');
                }

                if (!Schema::hasColumn('users', 'comp_tax')) {
                    $table->string('comp_tax', 15)->nullable()->after('profile_id');
                }

                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username', 25)->after('comp_tax');
                }

                if (!Schema::hasColumn('users', 'firstname')) {
                    $table->string('firstname', 255)->nullable()->after('username');
                }

                if (!Schema::hasColumn('users', 'lastname')) {
                    $table->string('lastname', 255)->nullable()->after('firstname');
                }

                if (!Schema::hasColumn('users', 'temporary_token')) {
                    $table->string('temporary_token', 255)->nullable()->after('remember_token');
                }

                if (!Schema::hasColumn('users', 'remark')) {
                    $table->string('remark', 255)->nullable()->after('temporary_token');
                }

                if (!Schema::hasColumn('users', 'lsp_tax_no')) {
                    $table->string('lsp_tax_no', 13)->nullable()->after('remark');
                }

                if (!Schema::hasColumn('users', 'lsp_comp_nmt')) {
                    $table->string('lsp_comp_nmt', 255)->nullable()->after('lsp_tax_no');
                }

                if (!Schema::hasColumn('users', 'lsp')) {
                    $table->string('lsp', 2)->nullable()->after('lsp_comp_nmt');
                }

                if (!Schema::hasColumn('users', 'status')) {
                    $table->unsignedTinyInteger('status')->default(0)->after('lsp');
                }

                if (!Schema::hasColumn('users', 'deleted_at')) {
                    $table->softDeletes()->after('updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columns = [
                    'profile_id',
                    'comp_tax',
                    'username',
                    'firstname',
                    'lastname',
                    'temporary_token',
                    'remark',
                    'lsp_tax_no',
                    'lsp_comp_nmt',
                    'lsp',
                    'status',
                    'deleted_at',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
