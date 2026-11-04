<?php
namespace App\Extensions;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RouteUrlGeneratorExtension extends RouteUrlGenerator
{
    /**
     * Generate a URL for the given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function to($route, $parameters = [], $absolute = false)
    {
        $domain = $this->getRouteDomain($route, $parameters);
        $uri = $route->uri();
        if (isMicrosite()) {
            $uri = 'shop/' . getMicrositeSlug() . '/' . $uri;
        }

        // First we will construct the entire URI including the root and query string. Once it
        // has been constructed, we'll make sure we don't have any missing parameters or we
        // will need to throw the exception to let the developers know one was not given.
        $uri = $this->addQueryString($this->url->format(
            $root = $this->replaceRootParameters($route, $domain, $parameters),
            $this->replaceRouteParameters($uri, $parameters),
            $route
        ), $parameters);

        if (preg_match('/\{.*?\}/', $uri)) {
            throw UrlGenerationException::forMissingParameters($route);
        }

        // Once we have ensured that there are no missing parameters in the URI we will encode
        // the URI and prepare it for returning to the developer. If the URI is supposed to
        // be absolute, we will return it as-is. Otherwise we will remove the URL's root.
        $uri = strtr(rawurlencode($uri), $this->dontEncode);

        if (! $absolute) {
            $uri = preg_replace('#^(//|[^/?])+#', '', $uri);

            if ($base = $this->request->getBaseUrl()) {
                $uri = preg_replace('#^'.$base.'#i', '', $uri);
            }

            return '/'.ltrim($uri, '/');
        }

        return $uri;
    }
}