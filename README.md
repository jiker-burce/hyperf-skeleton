# Hyperf 自治模块系统

## 🎯 项目概述

这是一个基于Hyperf框架的自治模块系统，支持多个程序员并行开发不同模块，代码完全隔离。

## 🏗️ 系统架构

### 主项目结构
```
hyperf-skeleton/
├── app/                    # 主项目代码
├── config/                 # 主项目配置
├── bin/                    # 启动脚本
├── modules/                # 模块目录（按需拉取）
│   ├── module-a/          # 用户管理模块
│   ├── module-b/          # 商品管理模块
│   └── module-c/          # 订单管理模块
└── README.md              # 项目说明
```

### 模块结构
```
module-x/
├── Controllers/            # 控制器
├── Services/               # 服务层
├── config/                 # 模块配置
├── routes/                 # 路由定义
├── mock/                   # Mock数据
└── README.md              # 模块说明
```

## 🚀 快速开始

### 1. 克隆主项目
```bash
git clone <主项目仓库地址> hyperf-skeleton
cd hyperf-skeleton
```

### 2. 按需拉取模块
```bash
# 拉取用户管理模块
git clone <module-a仓库地址> modules/module-a

# 拉取商品管理模块
git clone <module-b仓库地址> modules/module-b

# 拉取订单管理模块
git clone <module-c仓库地址> modules/module-c
```

### 3. 启动服务
```bash
php bin/hyperf.php start
```

### 4. 访问API
- 模块A: http://localhost:9501/api/module-a/
- 模块B: http://localhost:9501/api/module-b/
- 模块C: http://localhost:9501/api/module-c/

## 👥 开发者工作流程

### 程序员A (用户管理模块)
```bash
# 1. 克隆主项目
git clone <主项目仓库地址> hyperf-skeleton
cd hyperf-skeleton

# 2. 克隆自己的模块
git clone <module-a仓库地址> modules/module-a

# 3. 启动开发环境
php bin/hyperf.php start

# 4. 开发代码
cd modules/module-a
# 修改代码...
git add .
git commit -m "feat: 新功能"
git push origin main
```

### 程序员B (商品管理模块)
```bash
# 1. 克隆主项目
git clone <主项目仓库地址> hyperf-skeleton
cd hyperf-skeleton

# 2. 克隆自己的模块
git clone <module-b仓库地址> modules/module-b

# 3. 启动开发环境
php bin/hyperf.php start

# 4. 开发代码
cd modules/module-b
# 修改代码...
git add .
git commit -m "feat: 新功能"
git push origin main
```

### 程序员C (订单管理模块)
```bash
# 1. 克隆主项目
git clone <主项目仓库地址> hyperf-skeleton
cd hyperf-skeleton

# 2. 克隆自己的模块
git clone <module-c仓库地址> modules/module-c

# 3. 启动开发环境
php bin/hyperf.php start

# 4. 开发代码
cd modules/module-c
# 修改代码...
git add .
git commit -m "feat: 新功能"
git push origin main
```

## 🔒 代码隔离

- ✅ 每个程序员只能看到自己的模块代码
- ✅ 无法访问其他模块的源代码
- ✅ 独立的版本管理
- ✅ 并行开发，无冲突

## 📋 管理命令

### 查看所有模块状态
```bash
curl http://localhost:9501/api/modules/
```

### 查看特定模块信息
```bash
curl http://localhost:9501/api/modules/module-a
```

### 重新加载模块配置
```bash
curl -X POST http://localhost:9501/api/modules/reload
```

## 🎯 核心特性

1. **完全代码隔离**: 每个程序员只能看到自己的模块
2. **自动路由注入**: 模块路由自动注册，无需手动配置
3. **Mock数据支持**: 支持跨模块依赖的Mock数据
4. **灵活部署**: 主项目可以灵活组合不同版本的模块

## 📚 相关文档

- `最终开发方案总结.md` - 详细的开发方案说明
- `setup-simple-workspace.sh` - 开发环境设置脚本

## 🎉 开始开发

选择你的模块，克隆对应的仓库，开始开发吧！

```bash
# 示例：程序员A开始开发
git clone <主项目仓库地址> hyperf-skeleton
cd hyperf-skeleton
git clone <module-a仓库地址> modules/module-a
php bin/hyperf.php start
```