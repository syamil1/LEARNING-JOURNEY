<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_evaluations', function (Blueprint $table) {
            $table->id();

            // employee_id dari tabel employees
            $table->unsignedBigInteger('employee_id');

            // KPI tahun ini
            $table->decimal('kpi_june', 5, 2)->nullable();
            $table->decimal('kpi_december', 5, 2)->nullable();

            // Link hasil assessment
            $table->string('assessment_link')->nullable();

            // KPI tahun lalu
            $table->decimal('last_year_kpi_june', 5, 2)->nullable();
            $table->decimal('last_year_kpi_december', 5, 2)->nullable();

            $table->timestamps();

            // Foreign Key
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_evaluations');
    }
};
