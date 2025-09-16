#!/bin/bash

# 简化开发环境设置脚本
# 用法: ./setup-simple-workspace.sh [module-a|module-b|module-c] [module-repo-url]

MODULE_NAME=$1
MODULE_REPO_URL=$2

if [ -z "$MODULE_NAME" ] || [ -z "$MODULE_REPO_URL" ]; then
    echo "用法: $0 [module-a|module-b|module-c] [module-repo-url]"
    echo "示例: $0 module-a https://github.com/your-org/module-a.git"
    echo "示例: $0 module-b https://github.com/your-org/module-b.git"
    echo "示例: $0 module-c https://github.com/your-org/module-c.git"
    exit 1
fi

echo "🚀 设置 $MODULE_NAME 开发环境..."

# 检查模块目录是否存在
if [ -d "modules/$MODULE_NAME" ]; then
    echo "⚠️  模块目录已存在，是否删除重建？(y/N)"
    read -r response
    if [[ "$response" =~ ^[Yy]$ ]]; then
        rm -rf "modules/$MODULE_NAME"
    else
        echo "❌ 取消操作"
        exit 1
    fi
fi

# 克隆模块仓库
echo "📥 克隆 $MODULE_NAME 模块仓库..."
git clone "$MODULE_REPO_URL" "modules/$MODULE_NAME"

if [ $? -ne 0 ]; then
    echo "❌ 克隆模块仓库失败"
    exit 1
fi

# 创建其他模块的占位符
echo "📁 创建其他模块占位符..."
for module in module-a module-b module-c; do
    if [ "$module" != "$MODULE_NAME" ]; then
        if [ ! -d "modules/$module" ]; then
            mkdir -p "modules/$module"
            # 创建基本的配置文件
            cat > "modules/$module/config/config.php" << EOF
<?php
return [
    'name' => '${module^} 模块',
    'version' => '1.0.0',
    'developer' => '其他程序员',
    'description' => '此模块由其他程序员开发',
    'enabled' => false,
    'route_prefix' => '/api/$module',
    'namespace' => 'App\\\\Modules\\\\${module^}',
    'mock_enabled' => true,
    'dependencies' => [],
    'services' => [],
    'endpoints' => []
];
EOF
            cat > "modules/$module/routes/routes.php" << EOF
<?php
// 此模块由其他程序员开发
// 当前开发者无法看到此模块的代码
use Hyperf\\HttpServer\\Router\\Router;

Router::addGroup('/api/$module', function () {
    Router::get('/', function() {
        return [
            'success' => false,
            'message' => '此模块由其他程序员开发，当前不可用',
            'module' => '$module',
            'timestamp' => time()
        ];
    });
});
EOF
        fi
    fi
done

# 创建开发者说明
echo "📝 创建开发者说明..."
cat > "DEVELOPER_README.md" << EOF
# $MODULE_NAME 开发环境

## 🎯 当前模块
**$MODULE_NAME** - 由当前程序员负责开发

## 🚀 快速开始

### 1. 启动开发环境
\`\`\`bash
php bin/hyperf.php start
\`\`\`

### 2. 访问API
- 模块信息: http://localhost:9501/api/$MODULE_NAME/
- 其他API请查看模块路由配置

### 3. 开发代码
\`\`\`bash
# 进入模块目录
cd modules/$MODULE_NAME

# 查看模块结构
ls -la

# 修改代码后重启服务
# 按 Ctrl+C 停止服务，然后重新运行 php bin/hyperf.php start
\`\`\`

## 🔒 代码隔离说明

### ✅ 你可以访问的内容
- \`modules/$MODULE_NAME/\` - 你的模块代码（完整）
- \`app/\` - 主项目代码
- \`config/\` - 主项目配置
- 其他模块的占位符配置（用于路由注入）

### ❌ 你无法访问的内容
- 其他模块的源代码（只有占位符）

## 📁 目录结构
\`\`\`
hyperf-skeleton/
├── modules/
│   ├── $MODULE_NAME/          # 你的模块（完整代码）
│   ├── module-a/              # 其他模块（仅占位符）
│   ├── module-b/              # 其他模块（仅占位符）
│   └── module-c/              # 其他模块（仅占位符）
├── app/                       # 主项目代码
├── config/                    # 主项目配置
└── bin/                       # 启动脚本
\`\`\`

## 🛠️ 开发流程

1. **修改代码**: 在 \`modules/$MODULE_NAME/\` 目录下开发
2. **测试API**: 使用 \`php bin/hyperf.php start\` 启动服务
3. **提交代码**: 在模块目录下使用 \`git add . && git commit -m "描述"\`
4. **推送代码**: 推送到模块仓库 \`git push origin main\`

## 🔧 常用命令

\`\`\`bash
# 启动服务
php bin/hyperf.php start

# 进入模块目录
cd modules/$MODULE_NAME

# 查看模块状态
git status

# 提交代码
git add .
git commit -m "feat: 新功能"

# 推送代码
git push origin main

# 查看模块历史
git log --oneline
\`\`\`

## 🎉 开始开发吧！

你现在可以独立开发 $MODULE_NAME 模块了，代码完全隔离，不会与其他程序员冲突！
EOF

echo ""
echo "✅ 开发环境设置完成！"
echo ""
echo "🎯 当前模块: $MODULE_NAME"
echo "📁 模块位置: modules/$MODULE_NAME"
echo "🔗 模块仓库: $MODULE_REPO_URL"
echo ""
echo "🚀 启动开发环境:"
echo "   php bin/hyperf.php start"
echo ""
echo "📚 查看说明文档:"
echo "   cat DEVELOPER_README.md"
echo ""
echo "🔒 代码隔离: 只能看到和修改 $MODULE_NAME 模块的代码"
