<?php

declare(strict_types=1);
/**
 * 商品管理模块 - 程序员B负责
 * 
 * @author 程序员B
 * @date 2024
 */

namespace App\Controller\ModuleB;

use App\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * 获取商品列表
     */
    public function index()
    {
        return [
            'module' => 'ModuleB',
            'controller' => 'ProductController',
            'action' => 'index',
            'message' => '商品列表 - 由程序员B开发',
            'data' => [
                ['id' => 1, 'name' => 'iPhone 15', 'price' => 5999, 'stock' => 100],
                ['id' => 2, 'name' => 'MacBook Pro', 'price' => 12999, 'stock' => 50],
            ]
        ];
    }

    /**
     * 创建商品
     */
    public function create()
    {
        $name = $this->request->input('name', '');
        $price = $this->request->input('price', 0);
        $stock = $this->request->input('stock', 0);

        return [
            'module' => 'ModuleB',
            'controller' => 'ProductController',
            'action' => 'create',
            'message' => '创建商品成功',
            'data' => [
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * 获取商品详情
     */
    public function show($id)
    {
        return [
            'module' => 'ModuleB',
            'controller' => 'ProductController',
            'action' => 'show',
            'message' => '商品详情',
            'data' => [
                'id' => $id,
                'name' => '商品' . $id,
                'price' => 999,
                'stock' => 10
            ]
        ];
    }

    /**
     * 更新商品库存
     */
    public function updateStock($id)
    {
        $stock = $this->request->input('stock', 0);

        return [
            'module' => 'ModuleB',
            'controller' => 'ProductController',
            'action' => 'updateStock',
            'message' => '更新库存成功',
            'data' => [
                'id' => $id,
                'stock' => $stock,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
