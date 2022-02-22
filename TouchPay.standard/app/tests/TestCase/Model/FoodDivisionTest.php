<?php

namespace Test\TestCase\Model;

use App\Controller\AppController;
use App\Model\FoodDivision;
use Cake\TestSuite\TestCase;
class FoodDivisionTest extends TestCase {
    public $fixtures = array(
		'app.foodDivisions'
	);

    public function setup() {
        $this->FoodDivision = new FoodDivision;
    }
    
    public function testGetFoodCostt() {
        $res = $this->FoodDivision->getFoodCost('1');
        $this->assertEqual($res, 300);


        $res = $this->FoodDivision->getFoodCost('4');
        $this->assertEqual($res, 320);


        $res = $this->FoodDivision->getFoodCost('100');
        $this->assertEqual($res, null);
    }
}
