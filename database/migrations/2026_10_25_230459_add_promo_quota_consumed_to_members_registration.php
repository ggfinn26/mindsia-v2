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
        Schema::table('members_registration', function (Blueprint $table) {
            $table->boolean('promo_quota_consumed')->default(false)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members_registration', function (Blueprint $table) {
            $table->dropColumn('promo_quota_consumed');
        });
    }
};
