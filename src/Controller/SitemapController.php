<?php

namespace PicPerf\StatamicPicPerf\Controller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use PicPerf\StatamicPicPerf\Trait\Configurable;
use Statamic\Http\Controllers\Controller;

class SitemapController extends Controller
{
    use Configurable;

    public function index()
    {
        $host = $this->getConfig('host', false);

        if (!$host || !$this->getConfig('register_sitemap', true)) {
            return (new Response('Sitemap not found.', 404));
        }

        $domain = parse_url($host)["host"];
        $response = Http::get("https://picperf.io/sitemap/$domain");
        $contents = $response->body();

        return (new Response($contents, $response->status()))->header('Content-Type', 'application/xml');
    }
}
