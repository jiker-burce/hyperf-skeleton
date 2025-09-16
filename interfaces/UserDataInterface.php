<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * 用户数据接口
 * 主项目定义的标准用户数据访问接口
 */
interface UserDataInterface
{
    /**
     * 根据ID获取用户
     */
    public function getUser(int $userId): ?array;
    
    /**
     * 获取用户列表
     */
    public function getUsers(array $filters = []): array;
    
    /**
     * 搜索用户
     */
    public function searchUsers(string $keyword): array;
    
    /**
     * 检查用户是否存在
     */
    public function userExists(int $userId): bool;
}
