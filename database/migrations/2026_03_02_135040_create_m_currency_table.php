<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();
            $table->string('name', 50);
            $table->string('symbol', 10)->nullable();
            $table->tinyInteger('is_base')->default(0);
            $table->tinyInteger('is_delete')->default(0);
        });

        DB::table('m_currency')->insert([
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'is_base' => 1, 'is_delete' => 0],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => 0, 'is_delete' => 0],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'is_base' => 0, 'is_delete' => 0],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_currency');
    }
};
