<?php

declare(strict_types=1);

use App\Service\PTUserDataService;
use App\Service\PTProductDataService;

return [
    // 用户数据透传服务
    PTUserDataService::class => PTUserDataService::class,
    
    // 商品数据透传服务
    PTProductDataService::class => PTProductDataService::class,
];