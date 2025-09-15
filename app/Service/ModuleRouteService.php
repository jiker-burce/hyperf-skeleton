<?php

declare(strict_types=1);
/**
 * 模块路由自动注入服务
 * 自动扫描并注册所有模块的路由
 */

namespace App\Service;

class ModuleRouteService
{
    protected string $modulesPath;
    protected array $loadedModules = [];

    public function __construct()
    {
        $this->modulesPath = BASE_PATH . '/modules';
    }

    /**
     * 自动加载所有模块路由
     */
    public function loadAllModuleRoutes(): void
    {
        $modules = $this->scanModules();
        
        foreach ($modules as $module) {
            $this->loadModuleRoutes($module);
        }
    }

    /**
     * 扫描所有模块
     */
    protected function scanModules(): array
    {
        $modules = [];
        
        if (!is_dir($this->modulesPath)) {
            return $modules;
        }
        
        $dirs = scandir($this->modulesPath);
        
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }
            
            $modulePath = $this->modulesPath . '/' . $dir;
            
            if (is_dir($modulePath)) {
                $configFile = $modulePath . '/config/config.php';
                
                if (file_exists($configFile)) {
                    $config = require $configFile;
                    
                    if ($config['enabled'] ?? false) {
                        $modules[] = [
                            'name' => $dir,
                            'path' => $modulePath,
                            'config' => $config
                        ];
                    }
                }
            }
        }
        
        return $modules;
    }

    /**
     * 加载单个模块的路由
     */
    protected function loadModuleRoutes(array $module): void
    {
        $routeFile = $module['path'] . '/routes/routes.php';
        
        if (file_exists($routeFile)) {
            // 记录已加载的模块
            $this->loadedModules[] = $module['name'];
            
            // 加载路由文件
            require $routeFile;
            
            echo "✅ 已加载模块路由: {$module['name']}\n";
        }
    }

    /**
     * 获取已加载的模块列表
     */
    public function getLoadedModules(): array
    {
        return $this->loadedModules;
    }

    /**
     * 获取模块信息
     */
    public function getModuleInfo(string $moduleName): ?array
    {
        $modulePath = $this->modulesPath . '/' . $moduleName;
        $configFile = $modulePath . '/config/config.php';
        
        if (!file_exists($configFile)) {
            return null;
        }
        
        $config = require $configFile;
        
        return [
            'name' => $moduleName,
            'config' => $config,
            'loaded' => in_array($moduleName, $this->loadedModules)
        ];
    }

    /**
     * 获取所有模块信息
     */
    public function getAllModulesInfo(): array
    {
        $modules = [];
        $scannedModules = $this->scanModules();
        
        foreach ($scannedModules as $module) {
            $modules[$module['name']] = [
                'name' => $module['name'],
                'config' => $module['config'],
                'loaded' => in_array($module['name'], $this->loadedModules)
            ];
        }
        
        return $modules;
    }

    /**
     * 重新加载所有模块路由
     */
    public function reloadAllRoutes(): void
    {
        $this->loadedModules = [];
        $this->loadAllModuleRoutes();
    }
}
