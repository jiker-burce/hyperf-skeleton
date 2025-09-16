# 模块目录

这个目录用于存放各个模块的代码。

## 使用方法

### 拉取模块
```bash
# 拉取用户管理模块
git clone <module-a仓库地址> modules/module-a

# 拉取商品管理模块
git clone <module-b仓库地址> modules/module-b

# 拉取订单管理模块
git clone <module-c仓库地址> modules/module-c
```

### 启动服务
```bash
php bin/hyperf.php start
```

## 模块说明

- **module-a**: 用户管理模块 (程序员A负责)
- **module-b**: 商品管理模块 (程序员B负责)
- **module-c**: 订单管理模块 (程序员C负责)

每个模块都是独立的Git仓库，包含完整的代码和配置。
