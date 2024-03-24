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
    use Transformable, Configurable;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->isGetRequest($request) || !$this->isHtmlResponse($response)) {
            return $response;
        }

        if (!$this->getConfig('transform_all_markup', true)) {
            return $response;
        }

        $content = $response->getContent();
        $response->setContent($this->transformMarkup($content));

        return $response;
    }

    private function isGetRequest(Request $request): bool
    {
        return $request->method() === 'GET';
    }

    private function isHtmlResponse(Response $response): bool
    {
        return Str::of($response->headers->get('content-type'))->contains('text/html');
    }
}
