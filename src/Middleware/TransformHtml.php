<?php

namespace PicPerf\StatamicPicPerf\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use PicPerf\StatamicPicPerf\Trait\Configurable;
use PicPerf\StatamicPicPerf\Trait\Transformable;

class TransformHtml
{
    use Configurable, Transformable;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isGetRequest($request) || ! $this->isHtmlResponse($response)) {
            return $response;
        }

        if (! $this->getConfig('transform_all_markup', true)) {
            return $response;
        }

        $response->setContent(
            $this->transformMarkup(
                $response->getContent(),
                $this->getSitemapPath($request)
            ),
        );

        return $response;
    }

    private function getSitemapPath(Request $request): ?string
    {
        if (! $this->getConfig('add_sitemap_paths', false)) {
            return null;
        }

        return $this->getPath($request);
    }

    private function isGetRequest(Request $request): bool
    {
        return $request->method() === 'GET';
    }

    private function isHtmlResponse(Response $response): bool
    {
        return Str::of($response->headers->get('content-type'))->contains('text/html');
    }

    private function getPath(Request $request): string
    {
        $path = $request->path();

        // Ensure a leading slash.
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return $path;
    }
}
