<?php

declare(strict_types=1);
/**
 * 模块管理控制器
 * 提供模块信息查询和管理功能
 */

namespace App\Controller;

use App\Service\ModuleBootstrapService;
use Hyperf\Di\Annotation\Inject;

class ModuleManagerController extends AbstractController
{
    #[Inject]
    protected ModuleBootstrapService $moduleBootstrapService;

    /**
     * 获取所有模块信息
     */
    public function index()
    {
        $status = $this->moduleBootstrapService->getModuleStatus();
        
        return $this->success('获取模块信息成功', $status);
    }

    /**
     * 获取特定模块信息
     */
    public function show(string $moduleName)
    {
        $status = $this->moduleBootstrapService->getModuleStatus();
        
        if (!isset($status['modules'][$moduleName])) {
            return $this->error('模块不存在');
        }
        
        return $this->success('获取模块信息成功', [
            'module' => $status['modules'][$moduleName]
        ]);
    }

    /**
     * 重新加载所有模块
     */
    public function reload()
    {
        $this->moduleBootstrapService->bootstrap();
        
        return $this->success('模块重新加载成功');
    }

    /**
     * 成功响应
     */
    protected function success(string $message, $data = null)
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ];
    }

    /**
     * 错误响应
     */
    protected function error(string $message, int $code = 400)
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'timestamp' => time()
        ];
    }
}
