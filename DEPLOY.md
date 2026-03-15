# CentOS + PHP 8.2 部署指南

## 服务器环境要求

- CentOS 7/8
- PHP 8.2+
- PHP-FPM
- Nginx
- MySQL/MariaDB

## 安装步骤

### 1. 安装 PHP 8.2 和扩展

```bash
# 安装 Remi 仓库
yum install -y epel-release
yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm

# 启用 PHP 8.2
yum-config-manager --enable remi-php82

# 安装 PHP 及必要扩展
yum install -y php82 php82-php-fpm php82-php-mysqlnd php82-php-gd php82-php-mbstring php82-php-json php82-php-session php82-php-pdo

# 启动 PHP-FPM
systemctl start php82-php-fpm
systemctl enable php82-php-fpm
```

### 2. 配置 Nginx

编辑 `/etc/nginx/conf.d/网站地址.conf`：

```nginx
server {
    listen 80;
    server_name 网站地址;
    
    root /var/www/html/网站地址;
    index index.html index.php;

    # 前端路由支持
    location / {
        try_files $uri $uri/ /index.html;
    }

    # PHP 处理
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # CORS 支持
        add_header 'Access-Control-Allow-Origin' '*' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
        add_header 'Access-Control-Allow-Headers' 'Content-Type' always;
    }

    # 禁止访问隐藏文件
    location ~ /\. {
        deny all;
    }
}
```

### 3. 重启服务

```bash
nginx -t
systemctl reload nginx
```

### 4. 数据库配置

```bash
mysql -u root -p
```

执行 database.sql 中的 SQL 语句。

### 5. 目录权限

```bash
chmod -R 755 /var/www/html/网站地址
chmod -R 777 /var/www/html/网站地址/uploads
```

## 文件上传说明

需要上传的文件夹：

1. `api/` - PHP API 文件
2. `dist/` - Vue 构建后的前端文件
3. `uploads/` - 上传目录（需创建）

