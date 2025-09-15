# Git 模块化开发工作流程

## 分支策略

### 主分支
- `main` - 生产环境分支，只接受经过测试的代码
- `develop` - 开发环境分支，用于集成各模块的代码

### 功能分支
- `feature/module-a-*` - 程序员A的用户管理模块功能分支
- `feature/module-b-*` - 程序员B的商品管理模块功能分支  
- `feature/module-c-*` - 程序员C的订单管理模块功能分支

### 示例分支命名
```
feature/module-a-user-crud          # 用户CRUD功能
feature/module-a-user-auth          # 用户认证功能
feature/module-b-product-catalog    # 商品目录功能
feature/module-b-inventory          # 库存管理功能
feature/module-c-order-processing   # 订单处理功能
feature/module-c-payment            # 支付功能
```

## 开发流程

### 1. 初始化项目
```bash
# 克隆项目
git clone <repository-url>
cd hyperf-skeleton

# 创建开发分支
git checkout -b develop
git push -u origin develop
```

### 2. 程序员A开发用户模块
```bash
# 从develop分支创建功能分支
git checkout develop
git pull origin develop
git checkout -b feature/module-a-user-crud

# 开发完成后提交
git add app/Controller/ModuleA/
git commit -m "feat(module-a): 添加用户CRUD功能

- 实现用户列表查询
- 实现用户创建功能
- 实现用户详情查看
- 添加用户管理相关路由"

# 推送到远程
git push -u origin feature/module-a-user-crud
```

### 3. 程序员B开发商品模块
```bash
# 从develop分支创建功能分支
git checkout develop
git pull origin develop
git checkout -b feature/module-b-product-catalog

# 开发完成后提交
git add app/Controller/ModuleB/
git commit -m "feat(module-b): 添加商品管理功能

- 实现商品列表查询
- 实现商品创建功能
- 实现商品详情查看
- 实现库存更新功能
- 添加商品管理相关路由"

# 推送到远程
git push -u origin feature/module-b-product-catalog
```

### 4. 程序员C开发订单模块
```bash
# 从develop分支创建功能分支
git checkout develop
git pull origin develop
git checkout -b feature/module-c-order-processing

# 开发完成后提交
git add app/Controller/ModuleC/
git commit -m "feat(module-c): 添加订单管理功能

- 实现订单列表查询
- 实现订单创建功能
- 实现订单详情查看
- 实现订单支付功能
- 实现订单取消功能
- 添加订单管理相关路由"

# 推送到远程
git push -u origin feature/module-c-order-processing
```

## 代码合并流程

### 1. 创建Pull Request
每个功能分支完成后，创建PR到develop分支：
- 标题格式：`feat(module-x): 功能描述`
- 详细描述功能变更
- 添加相关测试

### 2. 代码审查
- 指定相关模块负责人进行代码审查
- 确保代码符合项目规范
- 检查是否有冲突

### 3. 合并到develop
```bash
# 审查通过后合并
git checkout develop
git pull origin develop
git merge feature/module-a-user-crud
git push origin develop
```

### 4. 发布到生产环境
```bash
# 从develop合并到main
git checkout main
git pull origin main
git merge develop
git tag v1.0.0
git push origin main --tags
```

## 冲突解决

### 1. 路由冲突
如果多个模块修改了`config/routes.php`：
```bash
# 拉取最新代码
git checkout develop
git pull origin develop

# 重新基于develop创建分支
git checkout feature/module-a-user-crud
git rebase develop

# 解决冲突后继续
git add config/routes.php
git rebase --continue
```

### 2. 配置文件冲突
如果修改了共享配置文件：
- 优先沟通协调
- 使用Git的合并工具解决
- 必要时创建临时分支进行测试

## 最佳实践

### 1. 提交规范
```bash
# 功能提交
git commit -m "feat(module-a): 添加用户认证功能"

# 修复提交  
git commit -m "fix(module-b): 修复商品价格计算错误"

# 文档提交
git commit -m "docs(module-c): 更新订单API文档"

# 重构提交
git commit -m "refactor(module-a): 重构用户服务类"
```

### 2. 分支管理
- 功能完成后及时删除本地分支
- 定期清理远程分支
- 保持分支名称清晰明确

### 3. 代码同步
- 每天开始工作前拉取最新代码
- 定期将develop分支合并到功能分支
- 避免长时间不合并导致的大冲突

## 模块隔离策略

### 1. 目录隔离
```
app/Controller/
├── ModuleA/          # 程序员A负责
├── ModuleB/          # 程序员B负责  
├── ModuleC/          # 程序员C负责
└── Common/           # 共享控制器
```

### 2. 命名空间隔离
```php
namespace App\Controller\ModuleA;  // 用户模块
namespace App\Controller\ModuleB;  // 商品模块
namespace App\Controller\ModuleC;  // 订单模块
```

### 3. 路由隔离
```php
// 每个模块使用独立的路由组
Router::addGroup('/api/module-a', function () { ... });
Router::addGroup('/api/module-b', function () { ... });
Router::addGroup('/api/module-c', function () { ... });
```

## 团队协作建议

1. **明确分工**：每个程序员负责特定模块
2. **及时沟通**：涉及共享资源时提前沟通
3. **代码审查**：相互审查代码，提高代码质量
4. **文档维护**：及时更新API文档和开发文档
5. **测试覆盖**：为每个模块编写单元测试
