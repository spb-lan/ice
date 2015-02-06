<?php

namespace Ice\Core;

use Ice\Helper\Console;
use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Test::dropTable();
        Test::createTable();
    }

    public function testActiveRecordCrud()
    {
//        foreach (Data_Source::getConfig()->gets('default') as $dataSourceClass => $scheme) {
//            $dataSourceKey = $dataSourceClass . '/default.' . $scheme;

//        $dataSourceKey = 'Ice:Mongodb/default.test';
        $dataSourceKey = 'Ice:Mysqli/default.test';

            $user1 = Test::create([
                '/name' => 'name',
                'name2' => 'test'
            ])->save([], $dataSourceKey);

            $user1->save(['/name' => 'test name'], $dataSourceKey);

            $this->assertNotNull($user1);
            $this->assertTrue($user1 instanceof Test);

            $user2 = Test::create(['/name' => 'test name'])
                ->find(['/name', 'name2'], $dataSourceKey);

            $user4 = Test::getModelBy(['/name' => 'test name'], ['/name', 'name2'], $dataSourceKey);

            $this->assertEquals($user2->get('/name'), 'test name');

            $this->assertNotNull($user2);
            $this->assertTrue($user2 instanceof Test);
            $this->assertEquals($user1, $user2);
            $this->assertEquals($user2->test_name, $user4->test_name);

            $user2->remove($dataSourceKey);

            $user3 = Test::getModel(1, '/pk', $dataSourceKey);

            $this->assertNull($user3);
        }
//    }
}
