<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            if (!Schema::hasColumn('images', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('images', 'file_name')) {
                $table->string('file_name')->nullable();
            }
            if (!Schema::hasColumn('images', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $columns = ['mime_type', 'file_name', 'file_path'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('images', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
