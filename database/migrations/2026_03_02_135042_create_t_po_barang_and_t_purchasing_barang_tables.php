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
        Schema::create('t_po_barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_detail_permintaan');
            $table->string('nomor_po', 100)->nullable();
            // draft | on_process | done | cancelled
            $table->string('status_po', 30)->default('draft');
            $table->decimal('amount', 18, 2)->default(0);
            $table->unsignedInteger('id_currency')->nullable();
            $table->decimal('rate_to_idr', 18, 6)->nullable();
            $table->decimal('amount_idr', 18, 2)->nullable();
            $table->date('tanggal_po')->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->string('created_by', 30);
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_detail_permintaan');
            $table->index('id_currency');
        });

        Schema::create('t_purchasing_barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_detail_permintaan');
            $table->string('vendor', 120)->nullable();
            // on_buy | bought | transit | direct_workshop | delivered_logistik
            $table->string('status_purchasing', 40)->default('on_buy');
            $table->decimal('amount', 18, 2)->default(0);
            $table->unsignedInteger('id_currency')->nullable();
            $table->decimal('rate_to_idr', 18, 6)->nullable();
            $table->decimal('amount_idr', 18, 2)->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->string('shipping_mode', 40)->nullable(); // transit | direct_workshop
            $table->string('shipping_point', 120)->nullable(); // "Transit pada ..."
            $table->text('keterangan')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->string('created_by', 30);
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_detail_permintaan');
            $table->index('id_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_purchasing_barang');
        Schema::dropIfExists('t_po_barang');
    }
};
