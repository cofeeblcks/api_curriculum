<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('available');
        });

        try {
            Schema::table('profiles', function (Blueprint $table) {
                $table->renameColumn('avatar', 'avatar_path');
            });
        } catch (\Throwable) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('avatar');
                $table->string('avatar_path')->nullable()->after('logo_path');
            });
        }
    }

    public function down(): void
    {
        try {
            Schema::table('profiles', function (Blueprint $table) {
                $table->renameColumn('avatar_path', 'avatar');
            });
        } catch (\Throwable) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('avatar_path');
                $table->string('avatar')->nullable()->after('available');
            });
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
