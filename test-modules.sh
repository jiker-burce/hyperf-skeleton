#!/bin/bash

# 模块路由测试脚本
# 测试所有模块的API接口是否正常工作

echo "🚀 开始测试Hyperf自治模块系统..."
echo "=================================="

# 检查服务是否运行
check_service() {
    if ! curl -s http://localhost:9501/ > /dev/null; then
        echo "❌ 错误: Hyperf服务未运行"
        echo "请先启动服务: php bin/hyperf.php start"
        exit 1
    fi
    echo "✅ Hyperf服务运行正常"
}

# 测试模块A (用户管理模块)
test_module_a() {
    echo ""
    echo "📋 测试模块A (用户管理模块)"
    echo "--------------------------------"
    
    # 模块信息
    echo "🔍 测试模块信息..."
    response=$(curl -s http://localhost:9501/api/module-a/)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 模块信息API正常"
    else
        echo "❌ 模块信息API异常: $response"
    fi
    
    # 用户列表
    echo "🔍 测试用户列表..."
    response=$(curl -s http://localhost:9501/api/module-a/users)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 用户列表API正常"
    else
        echo "❌ 用户列表API异常: $response"
    fi
    
    # 用户详情
    echo "🔍 测试用户详情..."
    response=$(curl -s http://localhost:9501/api/module-a/users/1)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 用户详情API正常"
    else
        echo "❌ 用户详情API异常: $response"
    fi
    
    # 用户搜索
    echo "🔍 测试用户搜索..."
    response=$(curl -s http://localhost:9501/api/module-a/users/search)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 用户搜索API正常"
    else
        echo "❌ 用户搜索API异常: $response"
    fi
    
    # 创建用户
    echo "🔍 测试创建用户..."
    response=$(curl -s -X POST http://localhost:9501/api/module-a/users \
        -H "Content-Type: application/json" \
        -d '{"name":"测试用户","email":"test@example.com"}')
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 创建用户API正常"
    else
        echo "❌ 创建用户API异常: $response"
    fi
}

# 测试模块B (商品管理模块)
test_module_b() {
    echo ""
    echo "📋 测试模块B (商品管理模块)"
    echo "--------------------------------"
    
    # 模块信息
    echo "🔍 测试模块信息..."
    response=$(curl -s http://localhost:9501/api/module-b/)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 模块信息API正常"
    else
        echo "❌ 模块信息API异常: $response"
    fi
    
    # 商品列表
    echo "🔍 测试商品列表..."
    response=$(curl -s http://localhost:9501/api/module-b/products)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 商品列表API正常"
    else
        echo "❌ 商品列表API异常: $response"
    fi
    
    # 商品详情
    echo "🔍 测试商品详情..."
    response=$(curl -s http://localhost:9501/api/module-b/products/1)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 商品详情API正常"
    else
        echo "❌ 商品详情API异常: $response"
    fi
    
    # 商品搜索
    echo "🔍 测试商品搜索..."
    response=$(curl -s http://localhost:9501/api/module-b/products/search)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 商品搜索API正常"
    else
        echo "❌ 商品搜索API异常: $response"
    fi
    
    # 创建商品
    echo "🔍 测试创建商品..."
    response=$(curl -s -X POST http://localhost:9501/api/module-b/products \
        -H "Content-Type: application/json" \
        -d '{"name":"测试商品","price":999,"stock":100}')
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 创建商品API正常"
    else
        echo "❌ 创建商品API异常: $response"
    fi
}

# 测试模块C (订单管理模块)
test_module_c() {
    echo ""
    echo "📋 测试模块C (订单管理模块)"
    echo "--------------------------------"
    
    # 模块信息
    echo "🔍 测试模块信息..."
    response=$(curl -s http://localhost:9501/api/module-c/)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 模块信息API正常"
    else
        echo "❌ 模块信息API异常: $response"
    fi
    
    # 订单列表
    echo "🔍 测试订单列表..."
    response=$(curl -s http://localhost:9501/api/module-c/orders)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 订单列表API正常"
    else
        echo "❌ 订单列表API异常: $response"
    fi
    
    # 订单详情
    echo "🔍 测试订单详情..."
    response=$(curl -s http://localhost:9501/api/module-c/orders/1)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 订单详情API正常"
    else
        echo "❌ 订单详情API异常: $response"
    fi
    
    # 订单搜索
    echo "🔍 测试订单搜索..."
    response=$(curl -s http://localhost:9501/api/module-c/orders/search)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 订单搜索API正常"
    else
        echo "❌ 订单搜索API异常: $response"
    fi
    
    # 创建订单
    echo "🔍 测试创建订单..."
    response=$(curl -s -X POST http://localhost:9501/api/module-c/orders \
        -H "Content-Type: application/json" \
        -d '{"user_id":1,"product_id":1,"amount":999}')
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 创建订单API正常"
    else
        echo "❌ 创建订单API异常: $response"
    fi
    
    # 支付订单
    echo "🔍 测试支付订单..."
    response=$(curl -s -X POST http://localhost:9501/api/module-c/orders/1/pay)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 支付订单API正常"
    else
        echo "❌ 支付订单API异常: $response"
    fi
}

# 测试模块管理功能
test_module_management() {
    echo ""
    echo "📋 测试模块管理功能"
    echo "--------------------------------"
    
    # 模块列表
    echo "🔍 测试模块列表..."
    response=$(curl -s http://localhost:9501/api/modules/)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 模块列表API正常"
    else
        echo "❌ 模块列表API异常: $response"
    fi
    
    # 单个模块信息
    echo "🔍 测试单个模块信息..."
    response=$(curl -s http://localhost:9501/api/modules/module-a)
    if echo "$response" | grep -q "success.*true"; then
        echo "✅ 单个模块信息API正常"
    else
        echo "❌ 单个模块信息API异常: $response"
    fi
}

# 测试首页
test_homepage() {
    echo ""
    echo "📋 测试首页"
    echo "--------------------------------"
    
    echo "🔍 测试首页..."
    response=$(curl -s http://localhost:9501/)
    if echo "$response" | grep -q "Hello Hyperf"; then
        echo "✅ 首页正常"
    else
        echo "❌ 首页异常: $response"
    fi
}

# 主函数
main() {
    echo "开始时间: $(date)"
    echo ""
    
    # 检查服务
    check_service
    
    # 测试各个模块
    test_homepage
    test_module_a
    test_module_b
    test_module_c
    test_module_management
    
    echo ""
    echo "=================================="
    echo "🎉 测试完成！"
    echo "结束时间: $(date)"
    echo ""
    echo "💡 提示:"
    echo "- 如果看到 ❌ 错误，请检查对应的模块是否正确配置"
    echo "- 如果看到 ✅ 成功，说明模块工作正常"
    echo "- 可以查看详细的API响应来调试问题"
}

# 运行测试
main
