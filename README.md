# e瞳网西安交通大学CAS代理服务

* 2017-03-02 正式上线
* 2017-04-26 PhpSimpleCas\PhpCasProxy升级为2.0版本

## 部署

1. 解压代码
2. 修改`config`目录下的配置文件
3. 给`runtime`文件夹可读写权限
4. 将网站部署在`cas.eeyes.net`域名下，最好开启SSL
5. 将网站根目录设置在`/public/`文件夹

### Nginx配置文件参考

```nginx
fastcgi_intercept_errors  on;

upstream backend {
    server  127.0.0.1:9000;
    server  127.0.0.1:9001;
    server  127.0.0.1:9002;
}

server {
    listen       80;
    listen       443 ssl;
    server_name  cas.eeyes.net piao.eeyes.net;
    root         /srv/www/cas_proxy-2017-04/public;
    index        index.php;

    ssl_certificate      /srv/www/ssl/chained.pem;
    ssl_certificate_key  /srv/www/ssl/domain.key;

    location / {
        if (!-e $request_filename) {
            rewrite  ^(.*)$ /index.php$1 last;
        }
    }

    location ~ [^/]\.php(/|$) {
        try_files                $fastcgi_script_name =404;
    #   fastcgi_pass             127.0.0.1:9000;
        fastcgi_pass             backend; # 本地测试需要保证php-cgi至少2个线程
        fastcgi_index            index.php;
        fastcgi_split_path_info  ^(.+\.php)(/.*)$;
        set $path_info           $fastcgi_path_info;
        fastcgi_param            PATH_INFO $path_info;
        include                  fastcgi.conf;
    }
}
```

## 使用

将CAS的地址设为`cas.eeyes.net`即可

### jasig/phpcas

`jasig/phpcas` 部分兼容。只兼容 `logout`, `forceAuthentication`, `checkAuthentication`, `getUser` 四个函数。且CAS服务器必须支持HTTPS协议

```bash
composer require jasig/phpcas
```

```php
<?php
require './vendor/autoload.php';
phpCAS::client(CAS_VERSION_2_0, 'cas.eeyes.net', 443, '');
phpCAS::setNoCasServerValidation();
if (isset($_REQUEST['logout'])) {
    phpCAS::logout();
}
if (isset($_REQUEST['login'])) {
    phpCAS::forceAuthentication();
}
$auth = phpCAS::checkAuthentication();
if ($auth) {
    echo phpCAS::getUser();
} else {
    echo 'Guest mode';
}
```

### ganlvtech/php-simple-cas

```bash
composer require ganlvtech/php-simple-cas ^1.0
```

```php
<?php
use PhpSimpleCas\PhpCas;

$phpCas = new PhpCas('https://cas.eeyes.net/');
$net_id = $phpCas->getUserOrRedirect();

var_dump($net_id);
```

## 开发人员

Ganlv (@ganlvtech)

## LICENSE

    The MIT License (MIT)

    Copyright (c) 2017 eeyes.net

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
