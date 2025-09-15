# 自治模块系统指南

## 🎯 设计理念

每个模块都是完全自治的，拥有自己的：
- **配置文件** (`config/config.php`)
- **路由定义** (`routes/routes.php`)
- **控制器** (`controllers/`)
- **服务层** (`services/`)
- **中间件** (`middlewares/`)
- **模拟数据** (`mock/data.json`)

## 🏗️ 模块结构

```
modules/
├── module-a/                    # 用户管理模块 (程序员A)
│   ├── config/
│   │   └── config.php          # 模块配置
│   ├── routes/
│   │   └── routes.php          # 模块路由
│   ├── controllers/
│   │   ├── UserController.php  # 用户控制器
│   │   └── ModuleController.php # 模块信息控制器
│   ├── services/
│   │   └── UserService.php     # 用户服务
│   ├── middlewares/            # 模块中间件
│   └── mock/
│       └── data.json           # 模拟数据
├── module-b/                    # 商品管理模块 (程序员B)
│   └── ... (相同结构)
└── module-c/                    # 订单管理模块 (程序员C)
    └── ... (相同结构)
```

## 🚀 核心特性

### 1. 完全自治
- 每个程序员只需要关心自己的模块
- 独立的配置文件、路由、控制器
- 自己的业务逻辑和数据处理

### 2. 自动路由注入
- 系统启动时自动扫描所有模块
- 自动加载每个模块的路由配置
- 无需手动注册路由

### 3. 模拟数据支持
- 开发环境使用模拟数据
- 不依赖其他模块的真实数据
- 可以独立开发和测试

### 4. 模块间通信
- 通过服务接口进行模块间通信
- 支持依赖注入
- 可以调用其他模块的服务

## 📋 模块配置

### config/config.php
```php
return [
    'name' => '用户管理模块',
    'version' => '1.0.0',
    'developer' => '程序员A',
    'enabled' => true,
    'route_prefix' => '/api/module-a',
    'namespace' => 'App\\Modules\\ModuleA',
    'mock' => [
        'enabled' => true,
        'data_file' => __DIR__ . '/../mock/data.json',
    ],
    'dependencies' => [
        // 依赖其他模块的服务
    ],
    'services' => [
        'user_service' => 'App\\Modules\\ModuleA\\Services\\UserService',
    ],
];
```

### routes/routes.php
```php
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/api/module-a', function () {
    Router::get('/users', 'App\\Modules\\ModuleA\\Controllers\\UserController@index');
    Router::post('/users', 'App\\Modules\\ModuleA\\Controllers\\UserController@create');
    // ... 更多路由
});
```

## 🔧 开发流程

### 1. 程序员A开发用户模块
```bash
# 在 modules/module-a/ 目录下工作
cd modules/module-a/

# 修改配置文件
vim config/config.php

# 添加路由
vim routes/routes.php

# 实现控制器
vim controllers/UserController.php

# 实现服务
vim services/UserService.php

# 更新模拟数据
vim mock/data.json
```

### 2. 程序员B开发商品模块
```bash
# 在 modules/module-b/ 目录下工作
cd modules/module-b/

# 同样的开发流程
# 完全独立，不需要关心其他模块
```

### 3. 程序员C开发订单模块
```bash
# 在 modules/module-c/ 目录下工作
cd modules/module-c/

# 可以依赖其他模块的服务
# 在 config.php 中声明依赖
'dependencies' => [
    'module-a' => ['user_service'],
    'module-b' => ['product_service'],
],
```

## 🧪 测试API

### 模块管理
```bash
# 获取所有模块信息
curl http://localhost:9501/api/modules

# 获取特定模块信息
curl http://localhost:9501/api/modules/module-a

# 重新加载所有模块
curl -X POST http://localhost:9501/api/modules/reload
```

### 模块A - 用户管理
```bash
# 获取用户列表
curl http://localhost:9501/api/module-a/users

# 创建用户
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"新用户","email":"new@example.com"}' \
  http://localhost:9501/api/module-a/users

# 获取用户详情
curl http://localhost:9501/api/module-a/users/1

# 搜索用户
curl http://localhost:9501/api/module-a/users/search?keyword=张三

# 更新用户状态
curl -X PATCH -H "Content-Type: application/json" \
  -d '{"status":"inactive"}' \
  http://localhost:9501/api/module-a/users/1/status
```

### 模块B - 商品管理
```bash
# 获取商品列表
curl http://localhost:9501/api/module-b/products

# 创建商品
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"新商品","price":999,"stock":100}' \
  http://localhost:9501/api/module-b/products

# 更新库存
curl -X PUT -H "Content-Type: application/json" \
  -d '{"stock":150}' \
  http://localhost:9501/api/module-b/products/1/stock
```

### 模块C - 订单管理
```bash
# 获取订单列表
curl http://localhost:9501/api/module-c/orders

# 创建订单
curl -X POST -H "Content-Type: application/json" \
  -d '{"user_id":1,"product_id":1,"amount":999}' \
  http://localhost:9501/api/module-c/orders

# 支付订单
curl -X POST http://localhost:9501/api/module-c/orders/1/pay

# 取消订单
curl -X POST http://localhost:9501/api/module-c/orders/1/cancel
```

## 🔐 代码隔离

### Git子模块设置
```bash
# 为每个模块创建独立仓库
git submodule add <module-a-repo> modules/module-a
git submodule add <module-b-repo> modules/module-b
git submodule add <module-c-repo> modules/module-c

# 设置权限
# 程序员A只能访问 module-a 仓库
# 程序员B只能访问 module-b 仓库
# 程序员C只能访问 module-c 仓库
```

### 开发环境
- 每个程序员在自己的模块目录下工作
- 使用模拟数据进行开发
- 不依赖其他模块的真实数据

### 集成测试
- 所有模块统一部署
- 使用真实数据进行测试
- 模块间正常通信

## 🎉 优势

1. **完全自治** - 每个程序员独立开发，互不干扰
2. **自动注入** - 路由自动加载，无需手动配置
3. **模拟数据** - 开发环境独立，不依赖其他模块
4. **模块通信** - 支持模块间服务调用
5. **易于扩展** - 新增模块只需创建目录结构
6. **版本控制** - 每个模块独立的Git仓库
7. **权限隔离** - 精确的代码访问控制

## 📝 最佳实践

1. **模块设计** - 保持模块功能单一，职责清晰
2. **接口定义** - 模块间通信通过服务接口
3. **数据隔离** - 使用模拟数据进行开发
4. **版本管理** - 每个模块独立版本控制
5. **文档维护** - 及时更新模块文档和API说明

这个自治模块系统真正实现了"每个程序员只需要关心自己的模块"的目标！🎯
