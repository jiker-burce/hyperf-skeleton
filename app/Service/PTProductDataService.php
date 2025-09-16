<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\ProductDataInterface;
use Hyperf\Config\Annotation\Value;

/**
 * 商品数据透传服务
 * 根据配置自动切换Mock/真实数据，透传调用子模块服务
 */
class PTProductDataService implements ProductDataInterface
{
    private array $mockData = [];
    
    #[Value("mock.is_mock")]
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
        $mockFile = defined('BASE_PATH') ? BASE_PATH . '/data/mock/products.json' : __DIR__ . '/../../data/mock/products.json';
        if (file_exists($mockFile)) {
            $data = json_decode(file_get_contents($mockFile), true);
            $this->mockData = $data['products'] ?? [];
        }
    }
    
    /**
     * 根据ID获取商品
     */
    public function getProduct(int $productId): ?array
    {
        if ($this->isMockMode) {
            return $this->getMockProduct($productId);
        }
        
        // 真实数据获取逻辑
        return $this->getRealProduct($productId);
    }
    
    /**
     * 获取商品列表
     */
    public function getProducts(array $filters = []): array
    {
        if ($this->isMockMode) {
            return $this->getMockProducts($filters);
        }
        
        // 真实数据获取逻辑
        return $this->getRealProducts($filters);
    }
    
    /**
     * 搜索商品
     */
    public function searchProducts(string $keyword): array
    {
        if ($this->isMockMode) {
            return $this->searchMockProducts($keyword);
        }
        
        // 真实数据搜索逻辑
        return $this->searchRealProducts($keyword);
    }
    
    /**
     * 检查商品是否存在
     */
    public function productExists(int $productId): bool
    {
        return $this->getProduct($productId) !== null;
    }
    
    /**
     * 检查库存
     */
    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->getProduct($productId);
        return $product && ($product['stock'] >= $quantity);
    }
    
    // Mock数据方法
    private function getMockProduct(int $productId): ?array
    {
        foreach ($this->mockData as $product) {
            if ($product['id'] == $productId) {
                return $product;
            }
        }
        return null;
    }
    
    private function getMockProducts(array $filters = []): array
    {
        $products = $this->mockData;
        
        // 应用过滤器
        if (isset($filters['category'])) {
            $products = array_filter($products, fn($product) => $product['category'] === $filters['category']);
        }
        
        if (isset($filters['status'])) {
            $products = array_filter($products, fn($product) => $product['status'] === $filters['status']);
        }
        
        return array_values($products);
    }
    
    private function searchMockProducts(string $keyword): array
    {
        if (empty($keyword)) {
            return $this->mockData;
        }
        
        return array_filter($this->mockData, function($product) use ($keyword) {
            return stripos($product['name'], $keyword) !== false || 
                   stripos($product['brand'], $keyword) !== false;
        });
    }
    
    // 真实数据方法（调用模块B的服务）
    private function getRealProduct(int $productId): ?array
    {
        // 直接调用模块B的服务
        if (class_exists('App\\Modules\\ModuleB\\Services\\ProductDataService')) {
            $productService = new \App\Modules\ModuleB\Services\ProductDataService();
            return $productService->getProduct($productId);
        }
        
        return null;
    }
    
    private function getRealProducts(array $filters = []): array
    {
        // 直接调用模块B的服务
        if (class_exists('App\\Modules\\ModuleB\\Services\\ProductDataService')) {
            $productService = new \App\Modules\ModuleB\Services\ProductDataService();
            return $productService->getProducts($filters);
        }
        
        return [];
    }
    
    private function searchRealProducts(string $keyword): array
    {
        // 直接调用模块B的服务
        if (class_exists('App\\Modules\\ModuleB\\Services\\ProductDataService')) {
            $productService = new \App\Modules\ModuleB\Services\ProductDataService();
            return $productService->searchProducts($keyword);
        }
        
        return [];
    }
}
