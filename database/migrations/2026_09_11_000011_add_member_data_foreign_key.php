<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('members_data_id');
            $table->unique('members_data_id');
            $table->foreign('members_data_id')->references('id')->on('members_data')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('member_accounts', function (Blueprint $table) {
            $table->dropForeign(['members_data_id']);
            $table->dropColumn('members_data_id');
        });
    }
};
