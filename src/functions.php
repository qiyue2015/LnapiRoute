<?php
/**
 * Created by PhpStorm.
 * User: fengqiyue
 * Date: 2019-02-27
 * Time: 00:57
 */

namespace LnapiRoute;

use FastRoute;
use LnapiRoute\Result;

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
            Result::send_error(404, 'Not Found 没找到对应的方法');
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed  方法不允许
            Result::send_error(405, $allowedMethods . ' Method Not Allowed 方法不允许');
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

            if ($options['API_PATH']) {
                $filePath = $options['API_PATH'];
            } else {
                $filePath = str_replace("\\", '/', dirname(dirname(__FILE__)));
            }
            $fileName = $filePath . '/api/' . $parts[0] . '/' . $segments[0] . '.php';
            if (!file_exists($fileName)) {
                Result::send_error(404, 'Not Found 没找到对应的方法文件：' . $segments[0]);
            } else {
                require_once $fileName;
            }

            // Instanitate controller
            $controller = new $segments[0]();
            $ret = call_user_func_array(array($controller, $segments[1]), array($vars));
            if (is_error($ret)) {
                Result::send_error($ret['errno'], $ret['message']);
            } elseif (array_key_exists('errno', $ret)) {
                Result::send_result($ret['message']);
            } else {
                Result::send_result($ret);
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