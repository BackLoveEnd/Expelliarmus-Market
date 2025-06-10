<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('', function (Blueprint $table) {
            DB::statement('ALTER TABLE products ADD COLUMN document_search tsvector');

            DB::statement(
                "CREATE OR REPLACE FUNCTION update_full_test_search_on_product_creation()
                      RETURNS TRIGGER AS $$
                      DECLARE
                          brand_name TEXT;
                      BEGIN
                       SELECT b.name INTO brand_name
                       FROM brands b
                          WHERE b.id = NEW.brand_id;
                          
                       NEW.document_search :=
                              setweight(to_tsvector(coalesce(NEW.title, '')), 'A') ||
                              setweight(to_tsvector(coalesce(NEW.product_article, '')), 'B') ||
                              setweight(to_tsvector(coalesce(brand_name, '')), 'C');

                            RETURN NEW;
                      END
                      $$ LANGUAGE plpgsql;"
            );

            DB::statement(
                '
                CREATE OR REPLACE TRIGGER trigger_update_full_text_search_when_product_create_or_update
                BEFORE INSERT OR UPDATE ON products
                FOR EACH ROW
                EXECUTE FUNCTION update_full_test_search_on_product_creation();'
            );

            DB::statement('CREATE INDEX full_text_search_products_idx ON products USING gin(document_search);');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'DROP TRIGGER IF EXISTS trigger_update_full_text_search_when_product_create_or_update ON products'
        );
        DB::statement('DROP FUNCTION IF EXISTS update_full_test_search_on_product_creation');
        DB::statement('DROP INDEX full_text_search_products_idx');
    }
};
