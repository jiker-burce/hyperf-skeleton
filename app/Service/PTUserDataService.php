<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\UserDataInterface;
use Hyperf\Config\Annotation\Value;

/**
 * 用户数据透传服务
 * 根据配置自动切换Mock/真实数据，透传调用子模块服务
 */
class PTUserDataService implements UserDataInterface
{
    private array $mockData = [];
    
    private bool $isMockMode;
    
    public function __construct()
    {
        // 直接读取配置，避免依赖注入时机问题
        $config = require BASE_PATH . '/config/autoload/mock.php';
        $this->isMockMode = $config['is_mock'];
        $this->loadMockData();
    }
    
    /**
     * 加载Mock数据
     */
    private function loadMockData(): void
    {
        $mockFile = defined('BASE_PATH') ? BASE_PATH . '/data/mock/users.json' : __DIR__ . '/../../data/mock/users.json';
        if (file_exists($mockFile)) {
            $data = json_decode(file_get_contents($mockFile), true);
            $this->mockData = $data['users'] ?? [];
        }
    }
    
    /**
     * 根据ID获取用户
     */
    public function getUser(int $userId): ?array
    {
        if ($this->isMockMode) {
            return $this->getMockUser($userId);
        }
        
        // 真实数据获取逻辑（这里可以调用数据库或其他服务）
        return $this->getRealUser($userId);
    }
    
    /**
     * 获取用户列表
     */
    public function getUsers(array $filters = []): array
    {
        if ($this->isMockMode) {
            return $this->getMockUsers($filters);
        }
        
        // 真实数据获取逻辑
        return $this->getRealUsers($filters);
    }
    
    /**
     * 搜索用户
     */
    public function searchUsers(string $keyword): array
    {
        if ($this->isMockMode) {
            return $this->searchMockUsers($keyword);
        }
        
        // 真实数据搜索逻辑
        return $this->searchRealUsers($keyword);
    }
    
    /**
     * 检查用户是否存在
     */
    public function userExists(int $userId): bool
    {
        return $this->getUser($userId) !== null;
    }
    
    // Mock数据方法
    private function getMockUser(int $userId): ?array
    {
        foreach ($this->mockData as $user) {
            if ($user['id'] == $userId) {
                return $user;
            }
        }
        return null;
    }
    
    private function getMockUsers(array $filters = []): array
    {
        $users = $this->mockData;
        
        // 应用过滤器
        if (isset($filters['status'])) {
            $users = array_filter($users, fn($user) => $user['status'] === $filters['status']);
        }
        
        return array_values($users);
    }
    
    private function searchMockUsers(string $keyword): array
    {
        if (empty($keyword)) {
            return $this->mockData;
        }
        
        return array_filter($this->mockData, function($user) use ($keyword) {
            return stripos($user['name'], $keyword) !== false || 
                   stripos($user['email'], $keyword) !== false;
        });
    }
    
    // 真实数据方法（调用模块A的服务）
    private function getRealUser(int $userId): ?array
    {
        // 直接调用模块A的服务
        if (class_exists('App\\Modules\\ModuleA\\Services\\UserDataService')) {
            $userService = new \App\Modules\ModuleA\Services\UserDataService();
            return $userService->getUser($userId);
        }
        
        return null;
    }
    
    private function getRealUsers(array $filters = []): array
    {
        // 直接调用模块A的服务
        if (class_exists('App\\Modules\\ModuleA\\Services\\UserDataService')) {
            $userService = new \App\Modules\ModuleA\Services\UserDataService();
            return $userService->getUsers($filters);
        }
        
        return [];
    }
    
    private function searchRealUsers(string $keyword): array
    {
        // 直接调用模块A的服务
        if (class_exists('App\\Modules\\ModuleA\\Services\\UserDataService')) {
            $userService = new \App\Modules\ModuleA\Services\UserDataService();
            return $userService->searchUsers($keyword);
        }
        
        return [];
    }
}
