<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

// 默认首页路由
Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

// 自动加载所有模块路由
$moduleRouteService = new \App\Service\ModuleRouteService();
$moduleRouteService->loadAllModuleRoutes();

// 模块管理路由 - 放在模块路由之后
Router::addGroup('/api/modules', function () {
    Router::get('/', 'App\Controller\ModuleManagerController@index');
    Router::get('/{moduleName}', 'App\Controller\ModuleManagerController@show');
    Router::post('/reload', 'App\Controller\ModuleManagerController@reload');
});

Router::get('/favicon.ico', function () {
    return '';
});
