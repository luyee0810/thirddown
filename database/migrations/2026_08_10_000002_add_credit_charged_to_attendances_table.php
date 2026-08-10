<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tracks whether this row has taken a credit, so status changes can
            // charge or refund exactly once.
            $table->boolean('credit_charged')->default(false)->after('status');
        });

        // Existing rows: treat attending statuses as already paid for, so an
        // edit doesn't charge them a second time. Balances are left as they are.
        DB::table('attendances')->whereIn('status', ['present', 'late'])->update(['credit_charged' => true]);
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('credit_charged');
        });
    }
};
