<?php

declare(strict_types=1);
/**
 * 模块启动服务
 * 在应用启动时自动加载所有模块
 */

namespace App\Service;

use Hyperf\Di\Annotation\Inject;

class ModuleBootstrapService
{
    #[Inject]
    protected ModuleRouteService $moduleRouteService;

    /**
     * 启动所有模块
     */
    public function bootstrap(): void
    {
        echo "🚀 开始启动自治模块系统...\n";
        
        // 加载所有模块路由
        $this->moduleRouteService->loadAllModuleRoutes();
        
        $loadedModules = $this->moduleRouteService->getLoadedModules();
        
        echo "✅ 模块启动完成！已加载 " . count($loadedModules) . " 个模块:\n";
        
        foreach ($loadedModules as $module) {
            $moduleInfo = $this->moduleRouteService->getModuleInfo($module);
            if ($moduleInfo) {
                $config = $moduleInfo['config'];
                echo "   - {$module}: {$config['name']} (v{$config['version']}) - {$config['developer']}\n";
            }
        }
        
        echo "\n";
    }

    /**
     * 获取模块状态
     */
    public function getModuleStatus(): array
    {
        $allModules = $this->moduleRouteService->getAllModulesInfo();
        $loadedModules = $this->moduleRouteService->getLoadedModules();
        
        $status = [
            'total_modules' => count($allModules),
            'loaded_modules' => count($loadedModules),
            'modules' => []
        ];
        
        foreach ($allModules as $name => $info) {
            $status['modules'][$name] = [
                'name' => $info['config']['name'],
                'version' => $info['config']['version'],
                'developer' => $info['config']['developer'],
                'enabled' => $info['config']['enabled'],
                'loaded' => $info['loaded'],
                'route_prefix' => $info['config']['route_prefix'],
                'mock_enabled' => $info['config']['mock']['enabled'] ?? false,
            ];
        }
        
        return $status;
    }
}
