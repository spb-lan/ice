<?php

namespace Ice\Core;

use Ice;
use Ice\Data\Provider\Router;
use PHPUnit_Framework_TestCase;

class ActionTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $router = Router::getInstance();
        $route = Route::getInstance($router->get('routeName'));
        $method = $route->gets('request/' . $router->get('method'));

        /** @var Action $actionClass */
        list($actionClass, $input) = each($method);

        $this->assertEquals($actionClass::call($input)->getContent(), 'Layout Test

inputTestPhp1
testPhpOk
inputTestSmarty

testSmartyOk

inputTestTwig

testTwigOk

inputTestPhp2
testPhpOk
test');
    }
}
