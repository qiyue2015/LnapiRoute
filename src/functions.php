<?php
/**
 * Created by PhpStorm.
 * User: fengqiyue
 * Date: 2019-02-27
 * Time: 00:57
 */

namespace LnapiRoute;

use FastRoute;

function Routing($routeDefinitionCallback, array $options = [])
{

    // 获取请求的方法和 URI
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_GET['uri'] ? $_GET['uri'] : $_GET['REQUEST_URI'];

    // 去除查询字符串( ? 后面的内容) 和 解码 URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);

    $dispatcher = FastRoute\simpleDispatcher($routeDefinitionCallback, $options);
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found 没找到对应的方法
            send_error(404, 'Not Found 没找到对应的方法');
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed  方法不允许
            send_error(405, 'Method Not Allowed  方法不允许');
            break;
        case FastRoute\Dispatcher::FOUND: // 找到对应的方法

            $handler = $routeInfo[1];
            $vars = $routeInfo[2]; // 获取请求参数

            // Grab all parts based on a / separator
            $parts = explode('/', $handler);

            // Collect the last index of the array
            $last = end($parts);

            // Grab the controller name and method call
            $segments = explode('@', $last);

            $filePath = str_replace("\\", '/', dirname(dirname(__FILE__)));
            $fileName = $filePath . '/api/' . $parts[0] . '/' . $segments[0] . '.php';
            if (!file_exists($fileName)) {
                send_error(404, 'Not Found 没找到对应的方法文件：' . $segments[0]);
            } else {
                require_once $fileName;
            }
            // Instanitate controller
            $controller = new $segments[0]();
            $ret = call_user_func_array(array($controller, $segments[1]), array($vars));
            if (is_error($ret)) {
                send_error($ret['errno'], $ret['message']);
            } else {
                send_result($ret);
            }

            break;
    }
}

function is_error($data)
{
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}

function send_error($number, $msg)
{
    global $_GPC;
    $obj = array();
    $obj['err_code'] = intval($number);
    $obj['err_msg'] = $msg;
    header('Content - type: application / json');
    $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($_GPC['jsonpCallback']) {
        $obj = $_GPC['jsonpCallback'] . '(' . $obj . ')';
    }
    die($obj);
}

function send_result($data = array())
{
    global $_GPC;
    $obj = array();
    $obj['err_code'] = 0;
    $obj['err_msg'] = 'success';
    $obj['data'] = $data ? $data : (Object)array();
    header('Content - type: application / json');
    $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($_GPC['jsonpCallback']) {
        $obj = $_GPC['jsonpCallback'] . '(' . $obj . ')';
    }
    die($obj);
}
