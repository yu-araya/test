<?php

namespace Test\TestCase\Model;

use App\Controller\AppController;
use App\Model\FoodPeriod;
use Cake\TestSuite\TestCase;
class FoodPeriodTest extends TestCase {
    public $fixtures = array(
		'app.FoodPeriods'
	);

    public function setup() {
        $this->FoodPeriod = new FoodPeriod;
    }
    
    public function testGetFoodPrice() {
        $res = $this->FoodPeriod->getFoodPrice('1', '2020-02-26');
        $this->assertEqual($res, 1000);

        $res = $this->FoodPeriod->getFoodPrice('4', '2020-02-26');
        $this->assertEqual($res, null);
    }
}
