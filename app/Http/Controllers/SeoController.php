<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Support\Str;

class SeoController extends Controller
{
    /**
     * XML sitemap of the public, indexable URLs.
     */
    public function sitemap()
    {
        $urls = [];
        $base = rtrim(config('app.url') ?: url('/'), '/');

        $add = function ($loc, $changefreq = 'weekly', $priority = '0.6', $lastmod = null) use (&$urls) {
            $urls[] = compact('loc', 'changefreq', 'priority', 'lastmod');
        };

        // Static / key pages
        $add(route('home'), 'daily', '1.0');
        $this->safe(fn () => $add(route('categories.all'), 'weekly', '0.7'));
        $this->safe(fn () => $add(route('blog'), 'weekly', '0.6'));
        $this->safe(fn () => $add(route('faq'), 'monthly', '0.5'));
        $this->safe(fn () => $add(route('rudraspirit.about'), 'monthly', '0.5'));

        $this->safe(function () use ($add) {
            foreach (Category::where('level', 0)->get() as $category) {
                $add(route('products.category', $category->slug), 'weekly', '0.7');
            }
        });
        $this->safe(function () use ($add) {
            Product::where('published', 1)->where('approved', 1)
                ->select('slug', 'updated_at')->orderBy('id', 'desc')
                ->chunk(500, function ($products) use ($add) {
                    foreach ($products as $p) {
                        $add(route('product', $p->slug), 'weekly', '0.8', optional($p->updated_at)->toAtomString());
                    }
                });
        });
        $this->safe(function () use ($add) {
            foreach (Blog::where('status', 1)->get() as $b) {
                $add(route('blog.details', $b->slug), 'monthly', '0.5', optional($b->updated_at)->toAtomString());
            }
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>" . e($u['lastmod']) . "</lastmod>\n";
            }
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Google Merchant Center product feed (RSS 2.0 + g: namespace) so products can
     * appear in Google Shopping / the Shopping tab.
     */
    public function merchantFeed()
    {
        $currency = optional(Currency::find(get_setting('system_default_currency')))->code ?: 'INR';
        $shopName = get_setting('website_name', 'RudraSpirit');

        $items = '';
        $this->safe(function () use (&$items, $currency) {
            Product::with('brand')->where('published', 1)->where('approved', 1)
                ->chunk(300, function ($products) use (&$items, $currency) {
                    foreach ($products as $p) {
                        $items .= $this->feedItem($p, $currency);
                    }
                });
        });

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= "<channel>\n";
        $xml .= '  <title>' . $this->cdata($shopName) . "</title>\n";
        $xml .= '  <link>' . e(rtrim(config('app.url') ?: url('/'), '/')) . "</link>\n";
        $xml .= '  <description>' . $this->cdata(get_setting('meta_description') ?: $shopName) . "</description>\n";
        $xml .= $items;
        $xml .= "</channel>\n</rss>";

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function feedItem(Product $p, string $currency): string
    {
        $name = $p->getTranslation('name');
        $desc = trim(strip_tags($p->getTranslation('description') ?: $name));
        $desc = Str::limit($desc, 4500, '');
        $image = $p->thumbnail ? get_image($p->thumbnail) : uploaded_asset($p->thumbnail_img);
        $price = number_format((float) $p->unit_price, 2, '.', '') . ' ' . $currency;
        $availability = ($p->current_stock ?? 0) > 0 ? 'in_stock' : 'out_of_stock';
        $brand = optional($p->brand)->name ?: get_setting('website_name', 'RudraSpirit');

        $item  = "  <item>\n";
        $item .= '    <g:id>' . e($p->id) . "</g:id>\n";
        $item .= '    <g:title>' . $this->cdata($name) . "</g:title>\n";
        $item .= '    <g:description>' . $this->cdata($desc) . "</g:description>\n";
        $item .= '    <g:link>' . e(route('product', $p->slug)) . "</g:link>\n";
        $item .= '    <g:image_link>' . e($image) . "</g:image_link>\n";
        $item .= '    <g:availability>' . $availability . "</g:availability>\n";
        $item .= '    <g:price>' . e($price) . "</g:price>\n";
        $item .= '    <g:brand>' . $this->cdata($brand) . "</g:brand>\n";
        $item .= '    <g:condition>new</g:condition>\n';
        $item .= '    <g:identifier_exists>no</g:identifier_exists>\n';
        $item .= "  </item>\n";

        return $item;
    }

    private function cdata($value): string
    {
        return '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', (string) $value) . ']]>';
    }

    private function safe(callable $fn): void
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            // keep the feed/sitemap valid even if one section fails
        }
    }
}
