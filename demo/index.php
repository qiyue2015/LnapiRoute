<?php
/**
 * Created by PhpStorm.
 * User: fengqiyue
 * Date: 2019-03-04
 * Time: 05:37
 */

require './../vendor/autoload.php';

require './class/bootstrap.php';
LnapiRoute\Routing(function ($r) {
    $r->get('/users', 'VoteMoel\Topic@List');
});