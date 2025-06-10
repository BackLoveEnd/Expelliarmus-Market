<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            'CREATE VIEW product_min_prices AS
                   SELECT 
                        p.id as product_id, 
                        COALESCE(MIN(price),0) as min_price,
                        COALESCE(MAX(price),0) as max_price
                   FROM (
                        SELECT product_id, price FROM product_variations
                        UNION ALL
                        SELECT product_id, price FROM product_attribute_values
                        UNION ALL
                        SELECT product_id, default_price as price FROM warehouses
                   ) as all_prices
                   JOIN products p ON p.id = all_prices.product_id
                   WHERE p.status = 0
                   GROUP BY p.id',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS product_min_prices');
    }
};
