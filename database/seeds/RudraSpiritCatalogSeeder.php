<?php

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RudraSpiritCatalogSeeder extends Seeder
{
    protected $mukhis = [
        ['n' => 1,  'deity' => 'Lord Shiva',          'benefit' => 'Supreme consciousness & focus',         'min' => 2499,  'max' => 10000],
        ['n' => 2,  'deity' => 'Ardhanareeshwar',      'benefit' => 'Unity, harmony & relationships',        'min' => 1999,  'max' => 6600],
        ['n' => 3,  'deity' => 'Agni (Fire)',          'benefit' => 'Release the past, renew energy',        'min' => 340,   'max' => 2500],
        ['n' => 4,  'deity' => 'Lord Brahma',          'benefit' => 'Knowledge, wit & creativity',           'min' => 450,   'max' => 2600],
        ['n' => 5,  'deity' => 'Kalagni Rudra',        'benefit' => 'Peace, calm & wellbeing',                'min' => 99,    'max' => 2500],
        ['n' => 6,  'deity' => 'Lord Kartikeya',       'benefit' => 'Willpower, grounding & focus',           'min' => 80,    'max' => 2800],
        ['n' => 7,  'deity' => 'Goddess Mahalakshmi',  'benefit' => 'Wealth & abundance',                     'min' => 799,   'max' => 7999],
        ['n' => 8,  'deity' => 'Lord Ganesha',         'benefit' => 'Removal of obstacles',                   'min' => 3999,  'max' => 16000],
        ['n' => 9,  'deity' => 'Goddess Durga',        'benefit' => 'Courage, energy & fearlessness',         'min' => 5500,  'max' => 18000],
        ['n' => 10, 'deity' => 'Lord Vishnu',          'benefit' => 'Protection from negativity',             'min' => 1200,  'max' => 9000],
        ['n' => 11, 'deity' => 'Lord Hanuman',         'benefit' => 'Strength, wisdom & vitality',            'min' => 6800,  'max' => 22000],
        ['n' => 12, 'deity' => 'Surya (Sun)',          'benefit' => 'Radiance, leadership & confidence',      'min' => 11000, 'max' => 46000],
        ['n' => 13, 'deity' => 'Kamadeva & Indra',     'benefit' => 'Charm, charisma & fulfilment',           'min' => 18000, 'max' => 60000],
        ['n' => 14, 'deity' => 'Devmani / Hanuman',    'benefit' => 'Intuition & the sixth sense',            'min' => 40000, 'max' => 201000],
    ];

    // Multiplier applied to the product's base price for each Size option.
    protected $sizeFactors = [
        'Small' => 1.0,
        'Medium - Regular' => 1.6,
        'Large' => 2.4,
        'Collector' => 4.0,
    ];

    // Additional multiplier applied on top of the size price for each Certificate option.
    protected $certFactors = [
        'Free Certificate' => 1.0,
        'No Certificate' => 0.9,
        'X-Ray' => 1.08,
        'X-Ray Certificate' => 1.15,
    ];

    protected function makeCategory(string $name, string $slug, int $parentId, int $level): Category
    {
        $category = Category::where('slug', $slug)->first();
        if ($category) {
            return $category;
        }

        $category = new Category();
        $category->forceFill([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId,
            'level' => $level,
            'featured' => 0,
            'hot_category' => 0,
            'top' => 0,
            'digital' => 0,
            'commision_rate' => 0,
        ]);
        $category->save();

        return $category;
    }

    protected function makeAttribute(string $name): Attribute
    {
        $attribute = Attribute::where('name', $name)->first();
        if ($attribute) {
            return $attribute;
        }

        $attribute = new Attribute();
        $attribute->forceFill(['name' => $name]);
        $attribute->save();

        return $attribute;
    }

    protected function makeAttributeValue(int $attributeId, string $value): void
    {
        $exists = AttributeValue::where('attribute_id', $attributeId)->where('value', $value)->exists();
        if ($exists) {
            return;
        }

        $attributeValue = new AttributeValue();
        $attributeValue->forceFill(['attribute_id' => $attributeId, 'value' => $value]);
        $attributeValue->save();
    }

    public function run()
    {
        $sizeAttribute = $this->makeAttribute('Size');
        $certAttribute = $this->makeAttribute('Certificate');
        $sizeValues = array_keys($this->sizeFactors);
        $certValues = array_keys($this->certFactors);

        foreach ($sizeValues as $sizeValue) {
            $this->makeAttributeValue($sizeAttribute->id, $sizeValue);
        }
        foreach ($certValues as $certValue) {
            $this->makeAttributeValue($certAttribute->id, $certValue);
        }

        $choiceOptions = json_encode([
            ['attribute_id' => $sizeAttribute->id, 'values' => $sizeValues],
            ['attribute_id' => $certAttribute->id, 'values' => $certValues],
        ]);
        $attributesJson = json_encode([$sizeAttribute->id, $certAttribute->id]);

        $category = $this->makeCategory('Rudraksha Beads', 'rudraksha-beads', 0, 0);

        foreach ($this->mukhis as $mukhi) {
            $n = $mukhi['n'];

            $productName = $n . ' Mukhi Rudraksha';
            $productSlug = Str::slug($productName . '-' . Str::random(4));

            if (Product::where('name', $productName)->exists()) {
                continue;
            }

            $basePrice = round(($mukhi['min'] + $mukhi['max']) / 2);

            $product = Product::create([
                'name' => $productName,
                'slug' => $productSlug,
                'added_by' => 'admin',
                'user_id' => 1,
                'category_id' => $category->id,
                'tags' => $mukhi['deity'],
                'description' => 'A natural, lab-certified bead sourced from Nepal. The ' . $productName . ' is revered for ' . $mukhi['benefit'] . ', blessed and energised before it reaches you.',
                'unit_price' => $basePrice,
                'purchase_price' => round($mukhi['min'] * 0.6),
                'variant_product' => 1,
                'attributes' => $attributesJson,
                'choice_options' => $choiceOptions,
                'colors' => '[]',
                'published' => 1,
                'draft' => 0,
                'approved' => 1,
                'cash_on_delivery' => 1,
                'featured' => 0,
                'current_stock' => 50,
                'unit' => 'pc',
                'min_qty' => 1,
                'discount' => 0,
                'discount_type' => 'amount',
                'tax' => 0,
                'tax_type' => 'percent',
                'shipping_type' => 'free',
                'shipping_cost' => 0,
                'num_of_sale' => rand(5, 120),
                'rating' => 0,
                'digital' => 0,
            ]);

            $product->categories()->attach([$category->id]);

            foreach ($sizeValues as $sizeValue) {
                foreach ($certValues as $certValue) {
                    $variant = str_replace(' ', '', $sizeValue) . '-' . str_replace(' ', '', $certValue);
                    $price = round($basePrice * $this->sizeFactors[$sizeValue] * $this->certFactors[$certValue]);

                    ProductStock::create([
                        'product_id' => $product->id,
                        'variant' => $variant,
                        'sku' => 'RS-' . $n . '-' . Str::slug($sizeValue) . '-' . Str::slug($certValue),
                        'price' => $price,
                        'qty' => rand(5, 25),
                    ]);
                }
            }
        }
    }
}
