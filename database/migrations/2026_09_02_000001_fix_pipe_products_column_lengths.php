<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Drop unique index first using raw SQL (handles if exists)
            try {
                DB::statement('ALTER TABLE pipe_products DROP INDEX uq_cat_sap_spec_thread');
            } catch (\Exception $e) {
                // Index doesn't exist, that's fine
            }

            // Alter columns using raw SQL
            DB::statement('ALTER TABLE pipe_products MODIFY sap_code VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE pipe_products MODIFY nominal_size VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE pipe_products MODIFY spec_name VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE pipe_products MODIFY material_code VARCHAR(100) NULL');

            // Recreate unique index
            try {
                DB::statement('CREATE UNIQUE INDEX uq_cat_sap_spec_thread ON pipe_products (pipe_category_id, sap_code, spec_name, is_threaded)');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        }
    }

    public function down(): void
    {
        // No-op, column sizes are fine either way
    }
};
