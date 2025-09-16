# Hyperf 模块化开发架构

## 项目概述

本项目基于 Hyperf 框架实现了模块化开发架构，支持多程序员并行开发，通过 Git 子模块实现代码隔离，通过透传服务实现数据访问。

## 架构特点

- **代码隔离**：每个模块独立开发，通过 Git 子模块管理
- **数据透传**：主项目提供透传服务，统一管理数据访问
- **Mock/真实数据切换**：通过环境变量控制数据源
- **接口标准化**：统一的接口定义，确保数据结构一致性

## 目录结构

```
hyperf-skeleton/
├── app/                          # 主项目应用代码
│   ├── Controller/               # 主项目控制器
│   │   ├── IndexController.php   # 首页控制器
│   │   └── ModuleManagerController.php # 模块管理控制器
│   └── Service/                  # 主项目服务
│       ├── PTUserDataService.php     # 用户数据透传服务
│       └── PTProductDataService.php  # 商品数据透传服务
├── interfaces/                   # 接口定义
│   ├── UserDataInterface.php     # 用户数据接口
│   └── ProductDataInterface.php  # 商品数据接口
├── data/mock/                    # Mock数据
│   ├── users.json               # 用户Mock数据
│   └── products.json            # 商品Mock数据
├── modules/                      # 子模块目录
│   ├── module-a/                # 用户管理模块
│   ├── module-b/                # 商品管理模块
│   └── module-c/                # 订单管理模块
└── config/autoload/
    ├── mock.php                 # Mock配置
    └── dependencies.php         # 依赖注入配置
```

## 主项目抽离公共Service流程

### 1. 定义接口

在 `interfaces/` 目录下定义标准接口：

```php
// interfaces/UserDataInterface.php
<?php
declare(strict_types=1);

namespace App\Interfaces;

interface UserDataInterface
{
    public function getUser(int $userId): ?array;
    public function getUsers(array $filters = []): array;
    public function searchUsers(string $keyword): array;
    public function userExists(int $userId): bool;
}
```

### 2. 创建透传服务

在 `app/Service/` 目录下创建透传服务：

```php
// app/Service/PTUserDataService.php
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
        // 读取配置
        $config = require BASE_PATH . '/config/autoload/mock.php';
        $this->isMockMode = $config['is_mock'];
        $this->loadMockData();
    }
    
    public function getUser(int $userId): ?array
    {
        if ($this->isMockMode) {
            return $this->getMockUser($userId);
        }
        return $this->getRealUser($userId);
    }
    
    // Mock数据方法
    private function getMockUser(int $userId): ?array
    {
        foreach ($this->mockData as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }
        return null;
    }
    
    // 真实数据方法（调用子模块服务）
    private function getRealUser(int $userId): ?array
    {
        if (class_exists('App\\Modules\\ModuleA\\Services\\UserDataService')) {
            $userService = new \App\Modules\ModuleA\Services\UserDataService();
            return $userService->getUser($userId);
        }
        return null;
    }
}
```

### 3. 配置Mock数据

在 `data/mock/` 目录下创建JSON数据文件：

```json
// data/mock/users.json
{
    "users": [
        {
            "id": 1,
            "name": "张三",
            "email": "zhangsan@example.com",
            "phone": "13800138001",
            "status": "active",
            "created_at": "2024-01-01 00:00:00",
            "updated_at": "2024-01-01 00:00:00"
        }
    ]
}
```

### 4. 配置依赖注入

在 `config/autoload/dependencies.php` 中注册服务：

```php
<?php
declare(strict_types=1);

use App\Service\PTUserDataService;
use App\Service\PTProductDataService;

return [
    // 用户数据透传服务
    PTUserDataService::class => PTUserDataService::class,
    
    // 商品数据透传服务
    PTProductDataService::class => PTProductDataService::class,
];
```

### 5. 配置环境变量

创建 `.env` 文件：

```env
# 数据模式配置
# true: 使用Mock数据 (开发环境)
# false: 使用真实数据 (生产环境)
IS_MOCK=true
```

### 6. 配置Mock开关

在 `config/autoload/mock.php` 中配置：

```php
<?php
declare(strict_types=1);

use function Hyperf\Support\env;

return [
    // 数据模式配置
    // true: 使用Mock数据 (开发环境)
    // false: 使用真实数据 (生产环境)
    'is_mock' => (bool)env('IS_MOCK', true),
];
```

### 7. 在子模块中使用

在子模块控制器中注入透传服务：

```php
// modules/module-c/Controllers/OrderWithDataController.php
<?php
declare(strict_types=1);

namespace App\Modules\ModuleC\Controllers;

use App\Service\PTUserDataService;
use App\Service\PTProductDataService;

class OrderWithDataController
{
    private PTUserDataService $userService;
    private PTProductDataService $productService;
    
    public function __construct(PTUserDataService $userService, PTProductDataService $productService)
    {
        $this->userService = $userService;
        $this->productService = $productService;
    }
    
    public function createOrder(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        // 验证用户
        $user = $this->userService->getUser($userId);
        if (!$user) {
            return $response->json(['success' => false, 'message' => '用户不存在']);
        }
        
        // 验证商品
        $product = $this->productService->getProduct($productId);
        if (!$product) {
            return $response->json(['success' => false, 'message' => '商品不存在']);
        }
        
        // 创建订单逻辑...
    }
}
```

## 开发流程

### 1. 新增公共Service流程

1. **定义接口**：在 `interfaces/` 目录下定义标准接口
2. **创建透传服务**：在 `app/Service/` 目录下创建 `PT*Service.php`
3. **创建Mock数据**：在 `data/mock/` 目录下创建对应的JSON文件
4. **配置依赖注入**：在 `dependencies.php` 中注册服务
5. **子模块实现**：在对应子模块中实现真实数据服务
6. **测试验证**：确保Mock和真实数据都能正常工作

### 2. 命名规范

- **透传服务**：`PT{ServiceName}Service.php`（PT = Pass Through）
- **接口文件**：`{ServiceName}Interface.php`
- **Mock数据**：`{service_name}.json`
- **子模块服务**：`{ServiceName}Service.php`

### 3. 数据一致性要求

- **Mock数据结构**：必须与真实数据结构完全一致
- **接口返回**：所有方法返回类型必须一致
- **错误处理**：统一的错误处理机制

## 环境配置

### 开发环境
```env
IS_MOCK=true
```

### 生产环境
```env
IS_MOCK=false
```

## 测试

运行测试脚本验证功能：

```bash
./test-simplified-architecture.sh
```

## 注意事项

1. **不要使用默认值**：配置必须明确设置，避免兜底方案
2. **遵循命名规范**：保持代码结构清晰
3. **数据一致性**：Mock数据与真实数据结构必须一致
4. **接口标准化**：所有服务必须实现标准接口
5. **环境变量管理**：通过 `.env` 文件管理配置

## 总结

这个架构实现了：
- ✅ **代码隔离**：通过Git子模块实现
- ✅ **数据透传**：主项目统一管理数据访问
- ✅ **环境切换**：Mock/真实数据自动切换
- ✅ **接口标准化**：统一的数据访问接口
- ✅ **简单易用**：清晰的开发流程和命名规范