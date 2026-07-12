<?php

namespace App\Services;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\Color;
use App\Models\ProductStock;
use App\Utility\ProductUtility;
use Illuminate\Support\Facades\Log;

class ProductStockService
{
    public function store(array $data, $product)
    {
        //Log::info('Product Stock Request:', $data);
        $collection = collect($data);

        // Row-based variation editor (WooCommerce style): each variant row is
        // submitted explicitly instead of being generated as a cartesian product.
        if (request()->input('variant_rows_mode') == 1) {
            $this->storeVariantRows($collection, $product);
            return;
        }

        $options = ProductUtility::get_attribute_options($collection);

        //Generates the combinations of customer choice options
        $combinations = (new CombinationService())->generate_combination($options);

        $variant = '';
        if (count($combinations) > 0) {
            $product->variant_product = 1;
            $product->save();
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->variant = $str;
                $product_stock->price = request()['price_' . str_replace('.', '_', $str)];
                $product_stock->sku = request()['sku_' . str_replace('.', '_', $str)];
                $product_stock->qty = request()['qty_' . str_replace('.', '_', $str)];
                $product_stock->image = request()['img_' . str_replace('.', '_', $str)];
                $product_stock->save();
            }
        } else {
            $this->storeSimpleStock($collection, $product);
        }
    }

    /**
     * Store explicit variant rows posted by the row-based variation editor.
     *
     * Expected request arrays (keyed by row uid):
     *   variant_color[uid]          color code (when colors are active)
     *   variant_opt[uid][attr_id]   chosen attribute value
     *   variant_price[uid], variant_sku[uid], variant_qty[uid], variant_img[uid]
     *
     * The variant string format matches ProductUtility::get_combination_string
     * (color name first, then attribute values with spaces stripped) so the
     * frontend product page keeps resolving stocks the same way.
     */
    protected function storeVariantRows($collection, $product)
    {
        $rowColors = (array) request()->input('variant_color', []);
        $rowOpts   = (array) request()->input('variant_opt', []);
        $prices    = (array) request()->input('variant_price', []);
        $skus      = (array) request()->input('variant_sku', []);
        $qtys      = (array) request()->input('variant_qty', []);
        $imgs      = (array) request()->input('variant_img', []);

        $colorsActive = isset($collection['colors_active']) && $collection['colors_active']
            && isset($collection['colors']) && count((array) $collection['colors']) > 0;
        $choiceNos = (array) ($collection['choice_no'] ?? []);

        $colorNames = $colorsActive
            ? Color::whereIn('code', array_filter(array_values($rowColors)))->pluck('name', 'code')->toArray()
            : [];

        $made = [];
        foreach (array_keys($prices) as $uid) {
            $parts = [];
            if ($colorsActive) {
                $code = $rowColors[$uid] ?? '';
                if ($code === '' || !isset($colorNames[$code])) {
                    continue; // incomplete row
                }
                $parts[] = $colorNames[$code];
            }
            $complete = true;
            foreach ($choiceNos as $attrId) {
                $value = $rowOpts[$uid][$attrId] ?? '';
                if ($value === '') {
                    $complete = false;
                    break;
                }
                $parts[] = str_replace(' ', '', $value);
            }
            if (!$complete || count($parts) === 0) {
                continue;
            }
            $str = implode('-', $parts);
            if (isset($made[$str])) {
                continue; // duplicate combination, keep the first row
            }
            $made[$str] = true;

            $product_stock = new ProductStock();
            $product_stock->product_id = $product->id;
            $product_stock->variant = $str;
            $product_stock->price = ($prices[$uid] !== '' && $prices[$uid] !== null) ? $prices[$uid] : $collection['unit_price'];
            $product_stock->sku = ($skus[$uid] ?? '') !== '' ? $skus[$uid] : null;
            $product_stock->qty = (int) ($qtys[$uid] ?? 0);
            $product_stock->image = ($imgs[$uid] ?? '') !== '' ? $imgs[$uid] : null;
            $product_stock->save();
        }

        if (count($made) > 0) {
            $product->variant_product = 1;
            $product->save();
        } else {
            $this->storeSimpleStock($collection, $product);
        }
    }

    protected function storeSimpleStock($collection, $product)
    {
        $product->variant_product = 0;
        $product->save();
        unset($collection['colors_active'], $collection['colors'], $collection['choice_no']);
        $variant = '';
        $qty = $collection['current_stock'] ?? 0;
        $price = $collection['unit_price'];
        unset($collection['current_stock']);

        $data = $collection->merge(compact('variant', 'qty', 'price'))->toArray();

        ProductStock::create($data);
    }

    public function product_duplicate_store($product_stocks , $product_new)
    {
        foreach ($product_stocks as $key => $stock) {
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product_new->id;
            $product_stock->variant     = $stock->variant;
            $product_stock->price       = $stock->price;
            $product_stock->sku         = null;
            $product_stock->qty         = $stock->qty;
            $product_stock->save();
        }
    }
}
