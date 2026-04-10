<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temple_id')
                ->constrained('temples')
                ->cascadeOnDelete();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country')->default('Thailand');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();

            $table->timestamps();

            $table->unique('temple_id');
            $table->index('province');
            $table->index('district');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temple_addresses');
    }
};