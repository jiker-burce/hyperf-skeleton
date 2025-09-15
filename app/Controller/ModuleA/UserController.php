<?php

declare(strict_types=1);
/**
 * 用户管理模块 - 程序员A负责
 * 
 * @author 程序员A
 * @date 2024
 */

namespace App\Controller\ModuleA;

use App\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * 获取用户列表
     */
    public function index()
    {
        return [
            'module' => 'ModuleA',
            'controller' => 'UserController',
            'action' => 'index',
            'message' => '用户列表 - 由程序员A开发',
            'data' => [
                ['id' => 1, 'name' => '张三', 'email' => 'zhangsan@example.com'],
                ['id' => 2, 'name' => '李四', 'email' => 'lisi@example.com'],
            ]
        ];
    }

    /**
     * 创建用户
     */
    public function create()
    {
        $name = $this->request->input('name', '');
        $email = $this->request->input('email', '');

        return [
            'module' => 'ModuleA',
            'controller' => 'UserController',
            'action' => 'create',
            'message' => '创建用户成功',
            'data' => [
                'name' => $name,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * 获取用户详情
     */
    public function show($id)
    {
        return [
            'module' => 'ModuleA',
            'controller' => 'UserController',
            'action' => 'show',
            'message' => '用户详情',
            'data' => [
                'id' => $id,
                'name' => '用户' . $id,
                'email' => 'user' . $id . '@example.com'
            ]
        ];
    }
}
