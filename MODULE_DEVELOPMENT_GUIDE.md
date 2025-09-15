# 模块化开发指南

- 每个人一个开发分支
- 每个模块内部一个模拟数据文件，存放需要的数据结构数据（根据条件有不同的数据）；给定一个开关，如果是开发环节，就不访问其他必要数据模块，集成测试，线上测试则进行集中

## 目录结构

```
app/Controller/
├── AbstractController.php     # 基础控制器
├── IndexController.php        # 首页控制器
├── ModuleA/                   # 用户管理模块 (程序员A)
│   └── UserController.php
├── ModuleB/                   # 商品管理模块 (程序员B)
│   └── ProductController.php
├── ModuleC/                   # 订单管理模块 (程序员C)
│   └── OrderController.php
└── Common/                    # 共享控制器
    └── BaseController.php
```

## API 路由结构

### 模块A - 用户管理
```
GET    /api/module-a/users          # 获取用户列表
POST   /api/module-a/users          # 创建用户
GET    /api/module-a/users/{id}     # 获取用户详情
```

### 模块B - 商品管理
```
GET    /api/module-b/products              # 获取商品列表
POST   /api/module-b/products              # 创建商品
GET    /api/module-b/products/{id}         # 获取商品详情
PUT    /api/module-b/products/{id}/stock   # 更新库存
```

### 模块C - 订单管理
```
GET    /api/module-c/orders                # 获取订单列表
POST   /api/module-c/orders                # 创建订单
GET    /api/module-c/orders/{id}           # 获取订单详情
POST   /api/module-c/orders/{id}/pay       # 支付订单
POST   /api/module-c/orders/{id}/cancel    # 取消订单
```

## 开发规范

### 1. 命名空间规范
```php
namespace App\Controller\ModuleA;  // 用户模块
namespace App\Controller\ModuleB;  // 商品模块
namespace App\Controller\ModuleC;  // 订单模块
```

### 2. 控制器规范
```php
<?php

declare(strict_types=1);
/**
 * 模块描述 - 负责人
 * 
 * @author 程序员X
 * @date 2024
 */

namespace App\Controller\ModuleX;

use App\Controller\AbstractController;

class XxxController extends AbstractController
{
    /**
     * 方法描述
     */
    public function methodName()
    {
        // 实现逻辑
        return [
            'module' => 'ModuleX',
            'controller' => 'XxxController',
            'action' => 'methodName',
            'message' => '操作成功',
            'data' => []
        ];
    }
}
```

### 3. 响应格式规范
```php
// 成功响应
return [
    'module' => 'ModuleX',
    'controller' => 'XxxController',
    'action' => 'methodName',
    'message' => '操作成功',
    'data' => [
        // 具体数据
    ]
];

// 错误响应
return [
    'module' => 'ModuleX',
    'controller' => 'XxxController',
    'action' => 'methodName',
    'error' => true,
    'message' => '错误信息',
    'code' => 400
];
```

## 测试 API

### 启动服务器
```bash
php bin/hyperf.php start
```

### 测试用户模块
```bash
# 获取用户列表
curl http://localhost:9501/api/module-a/users

# 创建用户
curl -X POST http://localhost:9501/api/module-a/users \
  -H "Content-Type: application/json" \
  -d '{"name":"张三","email":"zhangsan@example.com"}'

# 获取用户详情
curl http://localhost:9501/api/module-a/users/1
```

### 测试商品模块
```bash
# 获取商品列表
curl http://localhost:9501/api/module-b/products

# 创建商品
curl -X POST http://localhost:9501/api/module-b/products \
  -H "Content-Type: application/json" \
  -d '{"name":"iPhone 15","price":5999,"stock":100}'

# 更新库存
curl -X PUT http://localhost:9501/api/module-b/products/1/stock \
  -H "Content-Type: application/json" \
  -d '{"stock":50}'
```

### 测试订单模块
```bash
# 获取订单列表
curl http://localhost:9501/api/module-c/orders

# 创建订单
curl -X POST http://localhost:9501/api/module-c/orders \
  -H "Content-Type: application/json" \
  -d '{"user_id":1,"product_id":1,"amount":5999}'

# 支付订单
curl -X POST http://localhost:9501/api/module-c/orders/1/pay
```

## 开发注意事项

### 1. 模块隔离
- 每个模块独立开发，避免相互依赖
- 使用不同的命名空间和路由前缀
- 模块间通信通过服务层或事件机制

### 2. 代码质量
- 遵循 PSR-12 编码规范
- 添加适当的注释和文档
- 编写单元测试

### 3. 性能考虑
- 合理使用缓存
- 避免 N+1 查询问题
- 优化数据库查询

### 4. 安全考虑
- 输入验证和过滤
- 防止 SQL 注入
- 适当的权限控制

## 扩展建议

### 1. 添加中间件
```php
// config/autoload/middlewares.php
return [
    'http' => [
        // 全局中间件
        \App\Middleware\CorsMiddleware::class,
        
        // 模块特定中间件
        'api/module-a' => [
            \App\Middleware\AuthMiddleware::class,
        ],
    ],
];
```

### 2. 添加服务层
```php
// app/Service/ModuleA/UserService.php
namespace App\Service\ModuleA;

class UserService
{
    public function createUser(array $data): array
    {
        // 业务逻辑
    }
}
```

### 3. 添加数据模型
```php
// app/Model/ModuleA/User.php
namespace App\Model\ModuleA;

use Hyperf\DbConnection\Model\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
}
```

### 4. 添加事件监听
```php
// app/Listener/ModuleA/UserCreatedListener.php
namespace App\Listener\ModuleA;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class UserCreatedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UserCreatedEvent::class,
        ];
    }

    public function process(object $event): void
    {
        // 处理用户创建事件
    }
}
```
