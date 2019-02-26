<?php
/**
 * Created by PhpStorm.
 * User: fengqiyue
 * Date: 2019-02-27
 * Time: 01:02
 */
require './vendor/autoload.php';

LnapiRoute\Routing(function ($r) {
    $r->get('/users', 'web/user@userList');
    $r->get('/user/{uid:\d+}', 'web/user@userinfo');
});
