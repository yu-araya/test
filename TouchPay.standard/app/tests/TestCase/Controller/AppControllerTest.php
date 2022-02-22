<?php

namespace Test\TestCase\Controller;

use App\Controller\AppController;
use Cake\TestSuite\TestCase;
class AppControllerTest extends TestCase {
    public function setUp() {
        $this->AppController = new AppController();
    }

    public function testIsDate1() {
        $res1 = $this->AppController->isDate('2020-02-04');
        $this->assertTrue($res1);
    }
    public function testIsDate2() {
        $res2 = $this->AppController->isDate('2020/02/04');
        $this->assertTrue($res2);
    }
    public function testIsDate3() {
        $res3 = $this->AppController->isDate('2020-02-30');
        $this->assertFalse($res3);
    }
    public function testIsDate4() {
        $res4 = $this->AppController->isDate('asdasdad');
        $this->assertEquals($res4, null);
    }
    public function testIsDate5() {
        $res5 = $this->AppController->isDate('202022');
        $this->assertFalse($res5);
    }
}
