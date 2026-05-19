<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('journal_entries')
            ->whereNull('journal_number')
            ->orderBy('id')
            ->chunkById(100, function ($entries) {
                foreach ($entries as $entry) {
                    $period = str_replace('-', '', substr((string) $entry->entry_date, 0, 7));

                    DB::table('journal_entries')
                        ->where('id', $entry->id)
                        ->update([
                            'journal_number' => 'JRN-' . $period . '-LEGACY' . str_pad((string) $entry->id, 6, '0', STR_PAD_LEFT),
                            'posted_at' => $entry->created_at ?? now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('journal_entries')
            ->where('journal_number', 'like', '%-LEGACY%')
            ->update([
                'journal_number' => null,
                'posted_at' => null,
            ]);
    }
};
