<?php

namespace IO\Middlewares;

use IO\Api\ResponseCode;
use IO\Controllers\CategoryController;
use IO\Controllers\StaticPagesController;
use IO\Helper\RouteConfig;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Middleware;

class CheckNotFound extends Middleware
{
    public static $FORCE_404 = false;

    /**
     * @param Request $request
     */
    public function before(Request $request)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \ErrorException
     */
    public function after(Request $request, Response $response)
    {
        if ($response->status() == ResponseCode::NOT_FOUND) {
            $routeActive = RouteConfig::isActive(RouteConfig::PAGE_NOT_FOUND);
            $sbCategoryId = RouteConfig::getCategoryId(RouteConfig::PAGE_NOT_FOUND);

            if ($routeActive || $sbCategoryId > 0 || self::$FORCE_404) {
                if ($sbCategoryId > 0) {
                    /** @var CategoryController $controller */
                    $controller = pluginApp(CategoryController::class);
                    $content = $controller->showCategoryById($sbCategoryId);
                } else {
                    /** @var StaticPagesController $controller */
                    $controller = pluginApp(StaticPagesController::class);
                    $content = $controller->showPageNotFound();
                }

                $response = $response->make(
                    $content,
                    ResponseCode::NOT_FOUND
                );
                $response->forceStatus(ResponseCode::NOT_FOUND);
            }
        }

        $paths = [
            'https://cdn02.plentymarkets.com/jpx0tvae1136/plugin/31/ceres/css/ceres-icons.css',
            'https://cdn02.plentymarkets.com/jpx0tvae1136/plugin/31/ceres/css/ceres-base.css',
            'https://cdn02.plentymarkets.com/jpx0tvae1136/plugin/31/ceres/js/dist/ceres-client.min.js'
        ];

        $response->header('Link', implode(',', array_map(function($path) {
            $as = substr($path, -3) === '.js' ? 'script' : 'style';
            return '<' . $path . '>; rel=preload; as=' . $as;
        }, $paths)));

        return $response;
    }
}
