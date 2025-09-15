#!/bin/bash

echo "🚀 启动 Hyperf 代码隔离项目..."

# 检查是否安装了 Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker 未安装，请先安装 Docker"
    echo "   访问: https://docs.docker.com/get-docker/"
    exit 1
fi

# 检查是否安装了 docker-compose
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose 未安装，请先安装 docker-compose"
    exit 1
fi

# 停止现有容器
echo "🛑 停止现有容器..."
docker-compose down

# 启动服务
echo "🚀 启动服务..."
docker-compose up -d

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 5

# 检查服务状态
if curl -s http://localhost:9501/ > /dev/null; then
    echo "✅ 服务启动成功！"
    echo ""
    echo "📋 可用的 API 端点："
    echo "   - 首页: http://localhost:9501/"
    echo "   - 所有模块: http://localhost:9501/api/modules"
    echo "   - 模块A (用户): http://localhost:9501/api/module-a/users"
    echo "   - 模块B (商品): http://localhost:9501/api/module-b/products"
    echo "   - 模块C (订单): http://localhost:9501/api/module-c/orders"
    echo ""
    echo "🔧 管理命令："
    echo "   - 查看日志: docker-compose logs -f"
    echo "   - 停止服务: docker-compose down"
    echo "   - 重启服务: docker-compose restart"
    echo ""
    echo "📖 代码隔离说明："
    echo "   - 每个程序员只能访问自己的模块代码"
    echo "   - 通过 Git 子模块实现代码隔离"
    echo "   - 使用模拟数据进行开发测试"
else
    echo "❌ 服务启动失败，请检查日志："
    docker-compose logs
fi
