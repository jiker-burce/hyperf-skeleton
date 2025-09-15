<?php

declare(strict_types=1);
/**
 * 模块代理控制器
 * 根据环境配置路由到不同的模块实现
 */

namespace App\Controller;

use App\Service\MockDataService;
use Hyperf\Di\Annotation\Inject;

class ModuleProxyController extends AbstractController
{
    #[Inject]
    protected MockDataService $mockDataService;

    /**
     * 代理到模块A
     */
    public function moduleA(string $action = 'index', string $id = '')
    {
        if (!$this->mockDataService->isModuleEnabled('module-a')) {
            return $this->error('模块A未启用');
        }

        $moduleConfig = $this->mockDataService->getModuleConfig('module-a');
        
        switch ($action) {
            case 'users':
                return $this->getUsers();
            case 'user':
                return $this->getUser($id);
            case 'create-user':
                return $this->createUser();
            default:
                return $this->getModuleInfo('module-a', $moduleConfig);
        }
    }

    /**
     * 代理到模块B
     */
    public function moduleB(string $action = 'index', string $id = '')
    {
        if (!$this->mockDataService->isModuleEnabled('module-b')) {
            return $this->error('模块B未启用');
        }

        $moduleConfig = $this->mockDataService->getModuleConfig('module-b');
        
        switch ($action) {
            case 'products':
                return $this->getProducts();
            case 'product':
                return $this->getProduct($id);
            case 'create-product':
                return $this->createProduct();
            case 'update-stock':
                return $this->updateStock($id);
            default:
                return $this->getModuleInfo('module-b', $moduleConfig);
        }
    }

    /**
     * 代理到模块C
     */
    public function moduleC(string $action = 'index', string $id = '')
    {
        if (!$this->mockDataService->isModuleEnabled('module-c')) {
            return $this->error('模块C未启用');
        }

        $moduleConfig = $this->mockDataService->getModuleConfig('module-c');
        
        switch ($action) {
            case 'orders':
                return $this->getOrders();
            case 'order':
                return $this->getOrder($id);
            case 'create-order':
                return $this->createOrder();
            case 'pay-order':
                return $this->payOrder($id);
            case 'cancel-order':
                return $this->cancelOrder($id);
            default:
                return $this->getModuleInfo('module-c', $moduleConfig);
        }
    }

    // 模块A相关方法
    public function getUsers()
    {
        $users = $this->mockDataService->getModuleData('module-a', 'users');
        return $this->success('获取用户列表成功', $users);
    }

    public function getUser(string $id)
    {
        $users = $this->mockDataService->getModuleData('module-a', 'users');
        $user = array_filter($users, fn($u) => $u['id'] == $id);
        $user = reset($user);
        
        if (!$user) {
            return $this->error('用户不存在');
        }
        
        return $this->success('获取用户详情成功', $user);
    }

    public function createUser()
    {
        $name = $this->request->input('name', '');
        $email = $this->request->input('email', '');
        
        return $this->success('创建用户成功', [
            'id' => rand(100, 999),
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // 模块B相关方法
    public function getProducts()
    {
        $products = $this->mockDataService->getModuleData('module-b', 'products');
        return $this->success('获取商品列表成功', $products);
    }

    public function getProduct(string $id)
    {
        $products = $this->mockDataService->getModuleData('module-b', 'products');
        $product = array_filter($products, fn($p) => $p['id'] == $id);
        $product = reset($product);
        
        if (!$product) {
            return $this->error('商品不存在');
        }
        
        return $this->success('获取商品详情成功', $product);
    }

    public function createProduct()
    {
        $name = $this->request->input('name', '');
        $price = $this->request->input('price', 0);
        $stock = $this->request->input('stock', 0);
        
        return $this->success('创建商品成功', [
            'id' => rand(100, 999),
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function updateStock(string $id)
    {
        $stock = $this->request->input('stock', 0);
        
        return $this->success('更新库存成功', [
            'id' => $id,
            'stock' => $stock,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // 模块C相关方法
    public function getOrders()
    {
        $orders = $this->mockDataService->getModuleData('module-c', 'orders');
        return $this->success('获取订单列表成功', $orders);
    }

    public function getOrder(string $id)
    {
        $orders = $this->mockDataService->getModuleData('module-c', 'orders');
        $order = array_filter($orders, fn($o) => $o['id'] == $id);
        $order = reset($order);
        
        if (!$order) {
            return $this->error('订单不存在');
        }
        
        return $this->success('获取订单详情成功', $order);
    }

    public function createOrder()
    {
        $userId = $this->request->input('user_id', 0);
        $productId = $this->request->input('product_id', 0);
        $amount = $this->request->input('amount', 0);
        
        return $this->success('创建订单成功', [
            'id' => rand(100, 999),
            'order_no' => 'ORD' . date('YmdHis') . rand(1000, 9999),
            'user_id' => $userId,
            'product_id' => $productId,
            'amount' => $amount,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function payOrder(string $id)
    {
        return $this->success('订单支付成功', [
            'id' => $id,
            'status' => 'paid',
            'payment_time' => date('Y-m-d H:i:s')
        ]);
    }

    public function cancelOrder(string $id)
    {
        return $this->success('订单取消成功', [
            'id' => $id,
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s')
        ]);
    }

    // 通用方法
    protected function getModuleInfo(string $module, array $config)
    {
        return $this->success('模块信息', [
            'module' => $module,
            'name' => $config['name'],
            'developer' => $config['developer'],
            'version' => $config['version'],
            'enabled' => $config['enabled'],
            'mock_mode' => $this->mockDataService->isMockMode(),
            'endpoints' => $config['endpoints']
        ]);
    }

    protected function success(string $message, array $data = [])
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ];
    }

    protected function error(string $message, int $code = 400)
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'timestamp' => time()
        ];
    }

    /**
     * 获取所有模块信息
     */
    public function getAllModules()
    {
        $modules = $this->mockDataService->getAllModules();
        $result = [];

        foreach ($modules as $key => $config) {
            $result[$key] = [
                'name' => $config['name'],
                'developer' => $config['developer'],
                'version' => $config['version'],
                'enabled' => $config['enabled'],
                'endpoints' => $config['endpoints'],
                'mock_mode' => $this->mockDataService->isMockMode(),
            ];
        }

        return $this->success('获取所有模块信息成功', [
            'modules' => $result,
            'total' => count($result),
            'mock_mode' => $this->mockDataService->isMockMode(),
            'environment' => 'development'
        ]);
    }
}
