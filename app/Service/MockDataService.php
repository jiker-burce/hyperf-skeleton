<?php

declare(strict_types=1);
/**
 * 模拟数据服务
 * 根据环境配置提供模拟数据或真实数据
 */

namespace App\Service;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Config\Annotation\Value;

class MockDataService
{
    #[Value('development.mock_mode')]
    protected bool $mockMode;

    #[Value('development.modules')]
    protected array $modules;

    #[Value('development.mock')]
    protected array $mockConfig;

    /**
     * 获取模块数据
     * 
     * @param string $module 模块名称
     * @param string $dataType 数据类型
     * @param array $filters 过滤条件
     * @return array
     */
    public function getModuleData(string $module, string $dataType = '', array $filters = []): array
    {
        if (!$this->mockMode) {
            return $this->getRealData($module, $dataType, $filters);
        }

        return $this->getMockData($module, $dataType, $filters);
    }

    /**
     * 获取模拟数据
     * 
     * @param string $module 模块名称
     * @param string $dataType 数据类型
     * @param array $filters 过滤条件
     * @return array
     */
    protected function getMockData(string $module, string $dataType = '', array $filters = []): array
    {
        if (!isset($this->modules[$module])) {
            return [];
        }

        $moduleConfig = $this->modules[$module];
        $mockDataFile = BASE_PATH . '/' . $moduleConfig['mock_data'];

        if (!file_exists($mockDataFile)) {
            return [];
        }

        $data = json_decode(file_get_contents($mockDataFile), true);
        
        if (empty($dataType)) {
            return $data;
        }

        if (!isset($data[$dataType])) {
            return [];
        }

        $result = $data[$dataType];

        // 应用过滤条件
        if (!empty($filters)) {
            $result = $this->applyFilters($result, $filters);
        }

        return $result;
    }

    /**
     * 获取真实数据（占位方法）
     * 
     * @param string $module 模块名称
     * @param string $dataType 数据类型
     * @param array $filters 过滤条件
     * @return array
     */
    protected function getRealData(string $module, string $dataType = '', array $filters = []): array
    {
        // 这里应该调用真实的数据库或API
        // 为了演示，返回空数组
        return [];
    }

    /**
     * 应用过滤条件
     * 
     * @param array $data 数据
     * @param array $filters 过滤条件
     * @return array
     */
    protected function applyFilters(array $data, array $filters): array
    {
        if (empty($filters)) {
            return $data;
        }

        return array_filter($data, function ($item) use ($filters) {
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    // 支持范围查询
                    if (isset($value['min']) && $item[$key] < $value['min']) {
                        return false;
                    }
                    if (isset($value['max']) && $item[$key] > $value['max']) {
                        return false;
                    }
                    if (isset($value['in']) && !in_array($item[$key], $value['in'])) {
                        return false;
                    }
                } else {
                    // 精确匹配
                    if (!isset($item[$key]) || $item[$key] != $value) {
                        return false;
                    }
                }
            }
            return true;
        });
    }

    /**
     * 检查模块是否启用
     * 
     * @param string $module 模块名称
     * @return bool
     */
    public function isModuleEnabled(string $module): bool
    {
        return isset($this->modules[$module]) && $this->modules[$module]['enabled'];
    }

    /**
     * 获取模块配置
     * 
     * @param string $module 模块名称
     * @return array
     */
    public function getModuleConfig(string $module): array
    {
        return $this->modules[$module] ?? [];
    }

    /**
     * 获取所有模块配置
     * 
     * @return array
     */
    public function getAllModules(): array
    {
        return $this->modules;
    }

    /**
     * 切换模拟数据模式
     * 
     * @param bool $enabled 是否启用模拟数据
     * @return void
     */
    public function setMockMode(bool $enabled): void
    {
        $this->mockMode = $enabled;
    }

    /**
     * 检查是否使用模拟数据
     * 
     * @return bool
     */
    public function isMockMode(): bool
    {
        return $this->mockMode;
    }
}
