# Swoole 扩展安装指南

## 错误信息
```
Fatal error: Uncaught Hyperf\Engine\Exception\RuntimeException: The ext-swoole is required.
```

## 解决方案

### 方法1：使用 PECL 安装（推荐）

```bash
# 安装 Swoole 扩展
pecl install swoole

# 如果遇到权限问题，使用 sudo
sudo pecl install swoole
```

### 方法2：使用 Homebrew 安装（macOS）

```bash
# 安装 Homebrew（如果还没有）
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 安装 PHP 和 Swoole
brew install php
brew install swoole

# 或者使用 pecl
brew install php
pecl install swoole
```

### 方法3：使用 Docker（最简单）

```bash
# 使用官方 Hyperf Docker 镜像
docker run -it --rm -v $(pwd):/opt/www -p 9501:9501 hyperf/hyperf:8.1-alpine-v3.16-swoole

# 或者在项目根目录创建 docker-compose.yml
```

### 方法4：手动编译安装

```bash
# 下载 Swoole 源码
wget https://github.com/swoole/swoole-src/archive/v5.1.0.tar.gz
tar -xzf v5.1.0.tar.gz
cd swoole-src-5.1.0

# 编译安装
phpize
./configure
make && make install

# 添加到 php.ini
echo "extension=swoole.so" >> $(php --ini | grep "Loaded Configuration File" | cut -d: -f2)
```

## 验证安装

```bash
# 检查 Swoole 是否安装成功
php -m | grep swoole

# 查看 Swoole 版本
php --ri swoole
```

## 配置 PHP.ini

找到 PHP 配置文件并添加：
```ini
extension=swoole.so
```

## 重启服务

```bash
# 重启 PHP-FPM（如果使用）
sudo service php-fpm restart

# 或者重启 Web 服务器
sudo service nginx restart
sudo service apache2 restart
```

## 常见问题

### 1. 权限问题
```bash
# 使用 sudo 安装
sudo pecl install swoole
```

### 2. 依赖缺失
```bash
# Ubuntu/Debian
sudo apt-get install php-dev

# CentOS/RHEL
sudo yum install php-devel

# macOS
xcode-select --install
```

### 3. 版本兼容性
确保 PHP 版本 >= 8.1，Swoole 版本 >= 5.0

## 快速测试

安装完成后，运行：
```bash
php bin/hyperf.php start
```

如果看到类似输出，说明安装成功：
```
[INFO] HTTP Server listening at 0.0.0.0:9501
```

## Docker 方案（推荐用于开发）

如果安装遇到困难，建议使用 Docker：

```yaml
# docker-compose.yml
version: '3.8'
services:
  hyperf:
    image: hyperf/hyperf:8.1-alpine-v3.16-swoole
    container_name: hyperf-skeleton
    ports:
      - "9501:9501"
    volumes:
      - .:/opt/www
    working_dir: /opt/www
    command: php bin/hyperf.php start
```

然后运行：
```bash
docker-compose up -d
```
