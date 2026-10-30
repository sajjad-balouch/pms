<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Town;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke()
    {
        // Host key mein: cache kisi aur domain ke URLs na de
        $xml = Cache::remember('sitemap.' . request()->getHost(), 3600, fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function build(): string
    {
        $urls = collect([
            'home', 'housing-schemes', 'available-plots', 'about-us', 'contact', 'disclaimer', 'blog.index',
            'tools.legal-scanner', 'tools.tax-calculator', 'tools.land-converter', 'tools.bayana-generator',
            'tools.society-directory', 'tools.inheritance-calculator', 'tools.rent-generator',
            'tools.zakat-calculator', 'tools.cv-maker',
        ])->map(fn ($name) => [route($name), null]);

        Page::where('is_active', true)->where('slug', '!=', 'about-us')->get(['slug', 'updated_at'])
            ->each(fn ($p) => $urls->push([route('dynamic.page', $p->slug), $p->updated_at]));

        Post::where('status', 'published')->get(['slug', 'updated_at'])
            ->each(fn ($p) => $urls->push([route('blog.show', $p->slug), $p->updated_at]));

        Town::get(['id', 'updated_at'])
            ->each(fn ($t) => $urls->push([route('housing-schemes.show', $t->id), $t->updated_at]));

        Property::where('is_active', 'active')->whereIn('status', ['available', 'under_offer'])->get(['id', 'updated_at'])
            ->each(fn ($p) => $urls->push([route('property.details', $p->id), $p->updated_at]));

        $body = $urls->map(function ($url) {
            $lastmod = $url[1] ? '<lastmod>' . $url[1]->toAtomString() . '</lastmod>' : '';

            return '<url><loc>' . e($url[0]) . '</loc>' . $lastmod . '</url>';
        })->implode("\n");

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . $body . "\n</urlset>\n";
    }
}
