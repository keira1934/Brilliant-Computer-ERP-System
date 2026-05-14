<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE purchases MODIFY status ENUM('Draft','Ordered','Received','Paid','Cancelled') NOT NULL DEFAULT 'Draft'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE purchases MODIFY status ENUM('Draft','Ordered','Received','Cancelled') NOT NULL DEFAULT 'Draft'");
        }
    }
};
