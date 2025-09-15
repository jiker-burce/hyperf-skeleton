<?php

declare(strict_types=1);
/**
 * 开发环境配置
 * 控制模拟数据开关和模块配置
 */

return [
    // 模拟数据开关
    'mock_mode' => true,
    
    // 开发环境标识
    'environment' => 'development',
    
    // 模块配置
    'modules' => [
        'module-a' => [
            'name' => '用户管理模块',
            'developer' => '程序员A',
            'mock_data' => 'modules/module-a/mock/data.json',
            'enabled' => true,
            'version' => '1.0.0',
            'endpoints' => [
                'users' => '/api/module-a/users',
                'user_detail' => '/api/module-a/users/{id}',
            ],
        ],
        'module-b' => [
            'name' => '商品管理模块',
            'developer' => '程序员B',
            'mock_data' => 'modules/module-b/mock/data.json',
            'enabled' => true,
            'version' => '1.0.0',
            'endpoints' => [
                'products' => '/api/module-b/products',
                'product_detail' => '/api/module-b/products/{id}',
                'update_stock' => '/api/module-b/products/{id}/stock',
            ],
        ],
        'module-c' => [
            'name' => '订单管理模块',
            'developer' => '程序员C',
            'mock_data' => 'modules/module-c/mock/data.json',
            'enabled' => true,
            'version' => '1.0.0',
            'endpoints' => [
                'orders' => '/api/module-c/orders',
                'order_detail' => '/api/module-c/orders/{id}',
                'pay_order' => '/api/module-c/orders/{id}/pay',
                'cancel_order' => '/api/module-c/orders/{id}/cancel',
            ],
        ],
    ],
    
    // 模拟数据配置
    'mock' => [
        'enabled' => true,
        'fallback_to_real' => false, // 是否在模拟数据不可用时回退到真实数据
        'cache_duration' => 300, // 模拟数据缓存时间（秒）
    ],
    
    // 集成测试配置
    'integration' => [
        'enabled' => false,
        'test_modules' => ['module-a', 'module-b', 'module-c'],
        'real_data_mode' => true,
    ],
];
