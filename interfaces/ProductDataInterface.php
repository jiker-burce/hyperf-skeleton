<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * 商品数据接口
 * 主项目定义的标准商品数据访问接口
 */
interface ProductDataInterface
{
    /**
     * 根据ID获取商品
     */
    public function getProduct(int $productId): ?array;
    
    /**
     * 获取商品列表
     */
    public function getProducts(array $filters = []): array;
    
    /**
     * 搜索商品
     */
    public function searchProducts(string $keyword): array;
    
    /**
     * 检查商品是否存在
     */
    public function productExists(int $productId): bool;
    
    /**
     * 检查库存
     */
    public function checkStock(int $productId, int $quantity): bool;
}
