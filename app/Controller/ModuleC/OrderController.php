<?php

declare(strict_types=1);
/**
 * 订单管理模块 - 程序员C负责
 * 
 * @author 程序员C
 * @date 2024
 */

namespace App\Controller\ModuleC;

use App\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * 获取订单列表
     */
    public function index()
    {
        return [
            'module' => 'ModuleC',
            'controller' => 'OrderController',
            'action' => 'index',
            'message' => '订单列表 - 由程序员C开发',
            'data' => [
                ['id' => 1, 'user_id' => 1, 'product_id' => 1, 'amount' => 5999, 'status' => 'paid'],
                ['id' => 2, 'user_id' => 2, 'product_id' => 2, 'amount' => 12999, 'status' => 'pending'],
            ]
        ];
    }

    /**
     * 创建订单
     */
    public function create()
    {
        $userId = $this->request->input('user_id', 0);
        $productId = $this->request->input('product_id', 0);
        $amount = $this->request->input('amount', 0);

        return [
            'module' => 'ModuleC',
            'controller' => 'OrderController',
            'action' => 'create',
            'message' => '创建订单成功',
            'data' => [
                'user_id' => $userId,
                'product_id' => $productId,
                'amount' => $amount,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * 获取订单详情
     */
    public function show($id)
    {
        return [
            'module' => 'ModuleC',
            'controller' => 'OrderController',
            'action' => 'show',
            'message' => '订单详情',
            'data' => [
                'id' => $id,
                'user_id' => 1,
                'product_id' => 1,
                'amount' => 5999,
                'status' => 'paid'
            ]
        ];
    }

    /**
     * 支付订单
     */
    public function pay($id)
    {
        return [
            'module' => 'ModuleC',
            'controller' => 'OrderController',
            'action' => 'pay',
            'message' => '订单支付成功',
            'data' => [
                'id' => $id,
                'status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * 取消订单
     */
    public function cancel($id)
    {
        return [
            'module' => 'ModuleC',
            'controller' => 'OrderController',
            'action' => 'cancel',
            'message' => '订单取消成功',
            'data' => [
                'id' => $id,
                'status' => 'cancelled',
                'cancelled_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
