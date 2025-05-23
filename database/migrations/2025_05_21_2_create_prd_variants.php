<?php
/**
 * GB Developer
 *
 * @category GB_Developer
 * @package  GB
 *
 * @copyright Copyright (c) 2025 GB Developer.
 *
 * @author Geovan Brambilla <geovangb@gmail.com>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prd_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product')->constrained('products')->onDelete('cascade');
            $table->string('variant')->nullable();
            $table->string('name_variant')->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->default(0);
            $table->float('price', 10, 2)->default(0);
            $table->float('price_for', 10, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('prd_variants');
    }
};
