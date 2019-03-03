# LnapiRoute - LanNiu requset router for we7 addons

## Install

To install with composer:

    composer require lanniu/lnapi-route
    
## Usage 

    require './../vendor/autoload.php';
    
    require './class/bootstrap.php';
    LnapiRoute\Routing(function ($r) {
        $r->get('/users', 'VoteMoel\Topic@List');
    });