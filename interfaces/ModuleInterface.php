<?php

declare(strict_types=1);
/**
 * 模块间通信接口
 * 定义所有模块必须实现的接口
 */

namespace App\Interfaces;

interface ModuleInterface
{
    /**
     * 获取模块数据
     * 
     * @param array $params 请求参数
     * @return array 返回数据
     */
    public function getData(array $params = []): array;

    /**
     * 处理模块数据
     * 
     * @param array $data 输入数据
     * @return array 处理后的数据
     */
    public function processData(array $data): array;

    /**
     * 获取模块信息
     * 
     * @return array 模块信息
     */
    public function getModuleInfo(): array;

    /**
     * 健康检查
     * 
     * @return bool 模块是否健康
     */
    public function healthCheck(): bool;
}
