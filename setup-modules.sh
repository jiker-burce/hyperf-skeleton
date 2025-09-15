#!/bin/bash

# 代码隔离模块设置脚本
# 用于设置Git子模块实现代码隔离

echo "🚀 开始设置代码隔离模块..."

# 创建模块目录
echo "📁 创建模块目录..."
mkdir -p modules/{module-a,module-b,module-c}

# 初始化Git子模块（需要先创建对应的仓库）
echo "🔧 设置Git子模块..."

# 注意：这些仓库URL需要替换为实际的私有仓库地址
# 每个程序员应该有自己的私有仓库

echo "⚠️  请按照以下步骤完成设置："
echo ""
echo "1. 为每个程序员创建独立的私有仓库："
echo "   - 程序员A: hyperf-module-a (用户管理模块)"
echo "   - 程序员B: hyperf-module-b (商品管理模块)"
echo "   - 程序员C: hyperf-module-c (订单管理模块)"
echo ""
echo "2. 设置仓库权限："
echo "   - 程序员A只能访问 hyperf-module-a"
echo "   - 程序员B只能访问 hyperf-module-b"
echo "   - 程序员C只能访问 hyperf-module-c"
echo "   - 主项目仓库只有项目负责人可以访问"
echo ""
echo "3. 添加子模块到主项目："
echo "   git submodule add <module-a-repo-url> modules/module-a"
echo "   git submodule add <module-b-repo-url> modules/module-b"
echo "   git submodule add <module-c-repo-url> modules/module-c"
echo ""
echo "4. 提交子模块配置："
echo "   git add .gitmodules modules/"
echo "   git commit -m 'feat: 添加模块子模块配置'"
echo "   git push origin main"
echo ""
echo "5. 各程序员克隆自己的模块："
echo "   # 程序员A"
echo "   git clone <module-a-repo-url> modules/module-a"
echo "   # 程序员B"
echo "   git clone <module-b-repo-url> modules/module-b"
echo "   # 程序员C"
echo "   git clone <module-c-repo-url> modules/module-c"
echo ""
echo "6. 设置环境变量："
echo "   echo 'MOCK_MODE=true' >> .env"
echo "   echo 'APP_ENV=development' >> .env"
echo ""
echo "✅ 设置完成！现在每个程序员只能看到自己的模块代码。"

# 创建示例的.gitmodules文件
cat > .gitmodules.example << 'EOF'
[submodule "modules/module-a"]
	path = modules/module-a
	url = https://github.com/your-org/hyperf-module-a.git
[submodule "modules/module-b"]
	path = modules/module-b
	url = https://github.com/your-org/hyperf-module-b.git
[submodule "modules/module-c"]
	path = modules/module-c
	url = https://github.com/your-org/hyperf-module-c.git
EOF

echo ""
echo "📝 已创建 .gitmodules.example 文件作为参考"
echo "   请根据实际情况修改仓库URL后重命名为 .gitmodules"
