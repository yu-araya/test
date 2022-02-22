<?php

namespace Test\TestCase\Model;

use App\Controller\AppController;
use App\Model\EmployeeInfo;
use Cake\TestSuite\TestCase;
class EmployeeInfoTest extends TestCase {
    public $fixtures = array(
		'app.employeeInfos'
	);
    public function setup() {
        $this->EmployeeInfo = new EmployeeInfo;
    }

    public function testGetCheckEmployeeId() {
        $res1 = $this->EmployeeInfo->getCheckEmployeeId('001');
        $this->assertEqual($res1, 1);

        $res2 = $this->EmployeeInfo->getCheckEmployeeId('999');
        $this->assertEqual($res2, 0);
    }
}
