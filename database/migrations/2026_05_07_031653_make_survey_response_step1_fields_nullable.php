<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable()->change();
            $table->enum('gender', ['Male', 'Female'])->nullable()->change();
            $table->enum('customer_type', [
                'Business',
                'Citizen',
                'Government',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable(false)->change();
            $table->enum('gender', ['Male', 'Female'])->nullable(false)->change();
            $table->enum('customer_type', [
                'Business',
                'Citizen',
                'Government',
            ])->nullable(false)->change();
        });
    }
};
