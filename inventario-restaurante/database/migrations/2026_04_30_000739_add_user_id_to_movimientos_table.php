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
    Schema::table('movimientos', function (Blueprint $table) {
        // Agrega la columna después de producto_id
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('movimientos', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
