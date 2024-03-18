<?php

namespace PicPerf\StatamicPicPerf\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PicPerf\StatamicPicPerf\Trait\Transformable;

class TransformHtml
{
    use Transformable;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // $config = config('picperf');

        // dd($config);

        // if ($response instanceof Response) {
        //     $content = $response->getContent();
        //     $modifiedContent = $this->transformMarkup($content);
        //     $response->setContent($modifiedContent);
        // }

        return $response;
    }
}
