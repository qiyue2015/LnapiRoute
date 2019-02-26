# LnapiRoute - LanNiu requset router for we7 addons

## Install

To install with composer:

    composer require lanniu/lnapi-route
    
## Usage 

    require './vendor/autoload.php';
    
    LnapiRoute\Routing(function ($r) {
        $r->get('/users', 'web/user@userList');
        $r->get('/user/{uid:\d+}', 'web/user@userinfo');
    });