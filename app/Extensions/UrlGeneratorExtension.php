<?php

namespace App\Extensions;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlGeneratorExtension extends UrlGenerator
{
    const MICROSITE_URLS = [
        'product',
        'products.category',
        'products.subcategory',
        'products.subsubcategory',
        'search',
    ];
    protected $micrositeRouteGenerator = null;
    /**
     * Get the URL to a named route.
     *
     * @param string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        if (isMicrosite() && in_array($name, self::MICROSITE_URLS) && !is_null($route = $this->routes->getByName($name))) {
            return $this->toMicrositeRoute($route, $parameters, $absolute);
        }

        return parent::route($name, $parameters, $absolute);
    }

    /**
     * Get the URL for a given route instance.
     *
     * @param Route $route
     * @param  mixed  $parameters
     * @param bool $absolute
     * @return string
     *
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function toMicrositeRoute(Route $route, $parameters, bool $absolute): string
    {
        return $this->micrositeRouteUrl()->to(
            $route, $this->formatParameters($parameters), $absolute
        );
    }

    /**
     * Get the Route URL generator instance.
     */
    protected function micrositeRouteUrl(): RouteUrlGenerator
    {
        if (! $this->micrositeRouteGenerator) {
            $this->micrositeRouteGenerator = new RouteUrlGeneratorExtension($this, $this->request);
        }

        return $this->micrositeRouteGenerator;
    }
}
