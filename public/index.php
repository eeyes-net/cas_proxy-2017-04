<?php

use PhpSimpleCas\PhpCasProxy;

require '../vendor/autoload.php';

/**
 * 检查域名是否在允许域名列表之中
 * 
 * @param string $service 被代理服务链接
 *
 * @return bool
 */
function is_available_host($service)
{
    $available_hosts = include '../config/hosts.php';
    $host = parse_url($service, PHP_URL_HOST);
    foreach ($available_hosts as $available_host) {
        if (ends_with($host, $available_host)) {
            return true;
        }
    }
    return false;
}

/**
 * 判断$haystack是否以$needle结尾
 *
 * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php#7168986
 *
 * @param string $haystack 在什么字符串中查找
 * @param string $needle 要找到的子字符串
 *
 * @return bool
 */
function ends_with($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

// 主程序
$cas_config = include '../config/cas.php';
$phpCasProxy = new PhpCasProxy($cas_config['server'], 'is_available_host', $cas_config['my_service'], $cas_config['my_cas_context']);
$phpCasProxy->proxy();

// 之后是CAS未跳转提示
readfile('index.html');
