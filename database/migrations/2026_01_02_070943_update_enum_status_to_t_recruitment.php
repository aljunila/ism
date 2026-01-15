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
        DB::statement("
            ALTER TABLE t_recruitment
            MODIFY status ENUM('A','N','D')
            NOT NULL DEFAULT 'N'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
