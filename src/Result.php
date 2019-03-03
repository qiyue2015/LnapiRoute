<?php
/**
 * Created by PhpStorm.
 * User: fengqiyue
 * Date: 2019-02-27
 * Time: 16:25
 */

namespace LnapiRoute;

class Result
{

    /**
     * 输出错误信息
     * @param $number
     * @param $msg
     */
    static function send_error($number, $msg)
    {
        $obj = array();
        $obj['err_code'] = intval($number);
        $obj['err_msg'] = $msg;
        header('Content-type: application/json');
        $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($_GET['jsonpCallback']) {
            $obj = $_GET['jsonpCallback'] . '(' . $obj . ')';
        }
        die($obj);
    }

    /**
     * 输出正确信息
     * @param array $data
     */
    static function send_result($data = array())
    {
        $obj = array();
        $obj['err_code'] = 0;
        $obj['err_msg'] = 'success';
        $obj['data'] = $data ? $data : (Object)array();
        header('Content-type: application/json');
        $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($_GET['jsonpCallback']) {
            $obj = $_GET['jsonpCallback'] . '(' . $obj . ')';
        }
        die($obj);
    }

}