#!/bin/bash

# 开发者环境设置脚本
# 用法: ./setup-developer-env.sh [module-a|module-b|module-c]

MODULE_NAME=$1

if [ -z "$MODULE_NAME" ]; then
    echo "用法: $0 [module-a|module-b|module-c]"
    echo "示例: $0 module-a"
    exit 1
fi

echo "🚀 为程序员设置 $MODULE_NAME 开发环境..."

# 检查模块目录是否存在
if [ ! -d "modules/$MODULE_NAME" ]; then
    echo "❌ 错误: 模块 $MODULE_NAME 不存在"
    exit 1
fi

# 进入模块目录
cd "modules/$MODULE_NAME"

echo "📁 当前工作目录: $(pwd)"
echo "📋 模块内容:"
ls -la

echo ""
echo "🔍 Git状态:"
git status

echo ""
echo "📝 Git历史:"
git log --oneline

echo ""
echo "✅ 开发环境设置完成！"
echo ""
echo "🎯 程序员可以在这里进行开发:"
echo "   - 修改 Controllers/ 目录下的控制器"
echo "   - 修改 Services/ 目录下的服务"
echo "   - 修改 routes/routes.php 路由配置"
echo "   - 修改 config/config.php 模块配置"
echo "   - 修改 mock/ 目录下的Mock数据"
echo ""
echo "💡 常用命令:"
echo "   git add .                    # 添加所有修改"
echo "   git commit -m '描述'         # 提交修改"
echo "   git log --oneline            # 查看提交历史"
echo "   git status                   # 查看当前状态"
echo ""
echo "🔒 代码隔离: 程序员只能看到和修改当前模块的代码"
