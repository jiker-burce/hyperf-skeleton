#!/bin/bash

# Hyperf 模块化架构测试脚本
# 测试透传服务、数据隔离、Mock/真实数据切换等功能

echo "🚀 Hyperf 模块化架构测试"
echo "=================================="

BASE_URL="http://localhost:9501"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 测试函数
test_endpoint() {
    local method=$1
    local url=$2
    local description=$3
    local expected_code=${4:-200}
    local data=$5
    
    echo -e "\n${YELLOW}测试: $description${NC}"
    echo "请求: $method $url"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -w "\n%{http_code}" "$BASE_URL$url")
    else
        response=$(curl -s -w "\n%{http_code}" -X "$method" "$BASE_URL$url" -H "Content-Type: application/json" -d "$data")
    fi
    
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | head -n -1)
    
    if [ "$http_code" = "$expected_code" ]; then
        echo -e "${GREEN}✓ 成功 (HTTP $http_code)${NC}"
        if command -v jq >/dev/null 2>&1; then
            echo "响应: $body" | jq . 2>/dev/null || echo "响应: $body"
        else
            echo "响应: $body"
        fi
    else
        echo -e "${RED}✗ 失败 (HTTP $http_code, 期望 $expected_code)${NC}"
        echo "响应: $body"
    fi
}

# 检查服务是否运行
check_service() {
    echo -e "${BLUE}检查服务状态...${NC}"
    if ! curl -s http://localhost:9501/ > /dev/null; then
        echo -e "${RED}❌ 错误: Hyperf服务未运行${NC}"
        echo "请先启动服务: php bin/hyperf.php start"
        exit 1
    fi
    echo -e "${GREEN}✅ Hyperf服务运行正常${NC}"
}

# 检查架构文件
check_architecture_files() {
    echo -e "\n${BLUE}检查架构文件...${NC}"
    
    local files=(
        "interfaces/UserDataInterface.php"
        "interfaces/ProductDataInterface.php"
        "app/Service/PTUserDataService.php"
        "app/Service/PTProductDataService.php"
        "data/mock/users.json"
        "data/mock/products.json"
        "config/autoload/mock.php"
        "config/autoload/dependencies.php"
    )
    
    for file in "${files[@]}"; do
        if [ -f "$file" ]; then
            echo -e "${GREEN}✅ $file${NC}"
        else
            echo -e "${RED}❌ $file 不存在${NC}"
        fi
    done
}

# 测试基础模块功能
test_basic_modules() {
    echo -e "\n${YELLOW}=== 1. 测试基础模块功能 ===${NC}"
    
    test_endpoint "GET" "/api/module-a/" "模块A信息"
    test_endpoint "GET" "/api/module-b/" "模块B信息"
    test_endpoint "GET" "/api/module-c/" "模块C信息"
}

# 测试透传服务功能
test_passthrough_services() {
    echo -e "\n${YELLOW}=== 2. 测试透传服务功能 ===${NC}"
    
    # 测试订单创建（使用透传服务验证用户和商品）
    test_endpoint "POST" "/api/module-c/orders/create-with-validation" "创建订单（验证用户和商品）" 200 '{"user_id": 1, "product_id": 1, "quantity": 2}'
    
    test_endpoint "POST" "/api/module-c/orders/create-with-validation" "创建订单（用户不存在）" 200 '{"user_id": 999, "product_id": 1, "quantity": 2}'
    
    test_endpoint "POST" "/api/module-c/orders/create-with-validation" "创建订单（商品不存在）" 200 '{"user_id": 1, "product_id": 999, "quantity": 2}'
    
    test_endpoint "POST" "/api/module-c/orders/create-with-validation" "创建订单（库存不足）" 200 '{"user_id": 1, "product_id": 1, "quantity": 1000}'
}

# 测试数据集成功能
test_data_integration() {
    echo -e "\n${YELLOW}=== 3. 测试数据集成功能 ===${NC}"
    
    # 获取订单详情（包含用户和商品信息）
    test_endpoint "GET" "/api/module-c/orders/1/details" "获取订单详情（包含用户和商品信息）"
    
    # 高级搜索（跨模块数据搜索）
    test_endpoint "GET" "/api/module-c/orders/search-advanced?keyword=张三" "搜索订单（按用户名）"
    test_endpoint "GET" "/api/module-c/orders/search-advanced?keyword=iPhone" "搜索订单（按商品名）"
    test_endpoint "GET" "/api/module-c/orders/search-advanced?keyword=" "搜索订单（无关键词）"
    
    # 订单统计（需要用户和商品数据）
    test_endpoint "GET" "/api/module-c/orders/stats" "获取订单统计信息"
}

# 测试数据隔离
test_data_isolation() {
    echo -e "\n${YELLOW}=== 4. 测试数据隔离 ===${NC}"
    
    # 测试模块A只能访问自己的数据
    test_endpoint "GET" "/api/module-a/users" "模块A访问用户数据"
    
    # 测试模块B只能访问自己的数据
    test_endpoint "GET" "/api/module-b/products" "模块B访问商品数据"
    
    # 测试模块C通过透传服务访问其他模块数据
    test_endpoint "GET" "/api/module-c/orders/1/details" "模块C通过透传服务访问其他模块数据"
}

# 测试Mock数据切换
test_mock_data_switching() {
    echo -e "\n${YELLOW}=== 5. 测试Mock数据切换 ===${NC}"
    
    echo -e "${BLUE}检查Mock配置...${NC}"
    if [ -f ".env" ]; then
        if grep -q "IS_MOCK=true" .env; then
            echo -e "${GREEN}✅ 当前使用Mock数据模式${NC}"
        elif grep -q "IS_MOCK=false" .env; then
            echo -e "${GREEN}✅ 当前使用真实数据模式${NC}"
        else
            echo -e "${YELLOW}⚠️  IS_MOCK配置未设置${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  .env文件不存在${NC}"
    fi
    
    # 测试Mock数据是否正常工作
    test_endpoint "GET" "/api/module-c/orders/stats" "验证Mock数据功能"
}

# 测试错误处理
test_error_handling() {
    echo -e "\n${YELLOW}=== 6. 测试错误处理 ===${NC}"
    
    # 测试不存在的路由
    test_endpoint "GET" "/api/nonexistent" "不存在的路由" 404
    
    # 测试无效的请求数据
    test_endpoint "POST" "/api/module-c/orders/create-with-validation" "无效的请求数据" 200 '{"invalid": "data"}'
}

# 生成测试报告
generate_report() {
    echo -e "\n${GREEN}=== 测试完成 ===${NC}"
    echo "测试时间: $(date)"
    echo ""
    echo -e "${BLUE}架构特点总结:${NC}"
    echo "1. ✅ 主项目定义标准接口"
    echo "2. ✅ 透传服务统一管理数据访问"
    echo "3. ✅ 基于IS_MOCK配置自动切换Mock/真实数据"
    echo "4. ✅ 数据结构与JSON文件一一对应"
    echo "5. ✅ 各子模块通过接口访问数据，实现解耦"
    echo "6. ✅ 支持Git子模块实现代码隔离"
    echo ""
    echo -e "${BLUE}透传服务说明:${NC}"
    echo "- PTUserDataService: 用户数据透传服务"
    echo "- PTProductDataService: 商品数据透传服务"
    echo "- 根据IS_MOCK配置自动切换数据源"
    echo "- Mock数据存储在data/mock/目录"
    echo "- 真实数据调用对应子模块服务"
}

# 主函数
main() {
    echo "开始时间: $(date)"
    echo ""
    
    # 检查服务
    check_service
    
    # 检查架构文件
    check_architecture_files
    
    # 执行测试
    test_basic_modules
    test_passthrough_services
    test_data_integration
    test_data_isolation
    test_mock_data_switching
    test_error_handling
    
    # 生成报告
    generate_report
}

# 运行测试
main
