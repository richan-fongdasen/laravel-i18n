<?php

namespace RichanFongdasen\I18n\Tests;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;

class MacroTests extends DatabaseTestCase
{
    /** @test */
    public function eloquent_collections_are_translateable()
    {
        $products = Product::where('product_category_id', 1)->get();

        // $this->assertTrue(method_exists($products, 'translate'));
        $products->translate('de');

        foreach ($products as $product) {
            $translation = \DB::table('product_translations')
                ->where('product_id', $product->getKey())
                ->where('locale', 'de')
                ->first();

            $this->assertEquals($translation->title, $product->title);
            $this->assertEquals($translation->description, $product->description);
        }
    }
}
