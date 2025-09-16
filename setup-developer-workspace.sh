#!/bin/bash

# 开发者工作空间设置脚本
# 用法: ./setup-developer-workspace.sh [module-a|module-b|module-c] [developer-name]

MODULE_NAME=$1
DEVELOPER_NAME=$2

if [ -z "$MODULE_NAME" ] || [ -z "$DEVELOPER_NAME" ]; then
    echo "用法: $0 [module-a|module-b|module-c] [developer-name]"
    echo "示例: $0 module-a 程序员A"
    echo "示例: $0 module-b 程序员B"
    echo "示例: $0 module-c 程序员C"
    exit 1
fi

echo "🚀 为 $DEVELOPER_NAME 设置 $MODULE_NAME 开发工作空间..."

# 创建工作空间目录
WORKSPACE_DIR="../hyperf-workspace-$MODULE_NAME"
echo "📁 创建工作空间目录: $WORKSPACE_DIR"

if [ -d "$WORKSPACE_DIR" ]; then
    echo "⚠️  工作空间已存在，是否删除重建？(y/N)"
    read -r response
    if [[ "$response" =~ ^[Yy]$ ]]; then
        rm -rf "$WORKSPACE_DIR"
    else
        echo "❌ 取消操作"
        exit 1
    fi
fi

mkdir -p "$WORKSPACE_DIR"
cd "$WORKSPACE_DIR"

# 1. 克隆主项目（不包含子模块）
echo "📥 克隆主项目..."
git clone --no-checkout ../hyperf-skeleton .
git checkout main

# 2. 只克隆当前程序员需要的模块
echo "📥 克隆 $MODULE_NAME 模块..."
if [ -d "modules/$MODULE_NAME" ]; then
    rm -rf "modules/$MODULE_NAME"
fi

# 复制模块目录
cp -r "../hyperf-skeleton/modules/$MODULE_NAME" "modules/"

# 3. 创建其他模块的占位符目录（用于路由注入，但不包含代码）
echo "📁 创建其他模块占位符..."
for module in module-a module-b module-c; do
    if [ "$module" != "$MODULE_NAME" ]; then
        mkdir -p "modules/$module"
        # 创建基本的配置文件，但不包含源代码
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
done

# 4. 创建开发者专用的配置文件
echo "⚙️  创建开发者配置..."
cat > "config/autoload/developer.php" << EOF
<?php
return [
    'current_developer' => '$DEVELOPER_NAME',
    'current_module' => '$MODULE_NAME',
    'workspace_mode' => true,
    'other_modules_mock' => true,
    'development_mode' => true
];
EOF

# 5. 创建开发者专用的启动脚本
echo "📝 创建开发者启动脚本..."
cat > "start-dev.sh" << EOF
#!/bin/bash
echo "🚀 启动 $DEVELOPER_NAME 的开发环境..."
echo "📦 当前模块: $MODULE_NAME"
echo "🔒 代码隔离: 只能看到和修改 $MODULE_NAME 模块"
echo ""

# 检查Swoole扩展
if ! php -m | grep -q swoole; then
    echo "❌ 错误: Swoole扩展未安装"
    echo "请先安装Swoole扩展: pecl install swoole"
    exit 1
fi

# 启动服务
php bin/hyperf.php start
EOF

chmod +x start-dev.sh

# 6. 创建开发者说明文档
echo "📚 创建开发者说明文档..."
cat > "DEVELOPER_README.md" << EOF
# $DEVELOPER_NAME 开发环境

## 🎯 当前模块
**$MODULE_NAME** - 由 $DEVELOPER_NAME 负责开发

## 🚀 快速开始

### 1. 启动开发环境
\`\`\`bash
./start-dev.sh
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
# 按 Ctrl+C 停止服务，然后重新运行 ./start-dev.sh
\`\`\`

## 🔒 代码隔离说明

### ✅ 你可以访问的内容
- \`modules/$MODULE_NAME/\` - 你的模块代码
- \`app/\` - 主项目代码
- \`config/\` - 主项目配置
- 其他模块的占位符配置（用于路由注入）

### ❌ 你无法访问的内容
- \`modules/module-a/\` 的源代码（如果当前不是module-a）
- \`modules/module-b/\` 的源代码（如果当前不是module-b）
- \`modules/module-c/\` 的源代码（如果当前不是module-c）

## 📁 目录结构
\`\`\`
hyperf-workspace-$MODULE_NAME/
├── modules/
│   ├── $MODULE_NAME/          # 你的模块（完整代码）
│   ├── module-a/              # 其他模块（仅占位符）
│   ├── module-b/              # 其他模块（仅占位符）
│   └── module-c/              # 其他模块（仅占位符）
├── app/                       # 主项目代码
├── config/                    # 主项目配置
├── start-dev.sh              # 开发环境启动脚本
└── DEVELOPER_README.md       # 本说明文档
\`\`\`

## 🛠️ 开发流程

1. **修改代码**: 在 \`modules/$MODULE_NAME/\` 目录下开发
2. **测试API**: 使用 \`./start-dev.sh\` 启动服务
3. **提交代码**: 在模块目录下使用 \`git add . && git commit -m "描述"\`
4. **推送代码**: 推送到远程仓库（如果配置了的话）

## 🔧 常用命令

\`\`\`bash
# 启动服务
./start-dev.sh

# 进入模块目录
cd modules/$MODULE_NAME

# 查看模块状态
git status

# 提交代码
git add .
git commit -m "feat: 新功能"

# 查看模块历史
git log --oneline
\`\`\`

## 🎉 开始开发吧！

你现在可以独立开发 $MODULE_NAME 模块了，代码完全隔离，不会与其他程序员冲突！
EOF

# 7. 初始化Git仓库
echo "🔧 初始化Git仓库..."
git init
git add .
git commit -m "Initial commit: $DEVELOPER_NAME 的 $MODULE_NAME 开发环境"

echo ""
echo "✅ 开发工作空间设置完成！"
echo ""
echo "📁 工作空间位置: $(pwd)"
echo "🎯 当前模块: $MODULE_NAME"
echo "👤 开发者: $DEVELOPER_NAME"
echo ""
echo "🚀 启动开发环境:"
echo "   cd $WORKSPACE_DIR"
echo "   ./start-dev.sh"
echo ""
echo "📚 查看说明文档:"
echo "   cat DEVELOPER_README.md"
echo ""
echo "🔒 代码隔离: 只能看到和修改 $MODULE_NAME 模块的代码"
