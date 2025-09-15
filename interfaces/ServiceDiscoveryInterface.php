<?php

declare(strict_types=1);
/**
 * 服务发现接口
 * 用于模块间服务发现和通信
 */

namespace App\Interfaces;

interface ServiceDiscoveryInterface
{
    /**
     * 获取服务URL
     * 
     * @param string $module 模块名称
     * @return string 服务URL
     */
    public function getServiceUrl(string $module): string;

    /**
     * 注册服务
     * 
     * @param string $module 模块名称
     * @param string $url 服务URL
     * @return void
     */
    public function registerService(string $module, string $url): void;

    /**
     * 获取所有可用服务
     * 
     * @return array 服务列表
     */
    public function getAllServices(): array;

    /**
     * 检查服务是否可用
     * 
     * @param string $module 模块名称
     * @return bool 服务是否可用
     */
    public function isServiceAvailable(string $module): bool;
}
