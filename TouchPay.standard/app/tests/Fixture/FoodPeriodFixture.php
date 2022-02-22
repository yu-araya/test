<?php
namespace Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class FoodPeriodFixture extends TestFixture {
    public $import = 'FoodPeriod';
    public $records = [
        [
            'id' => 1,
            'food_division' => 1,
            'start_date' => '2020-02-12',
            'food_period_name' => '適用されない',
            'food_price' => 100,
            'created' => '2019-04-16 07:23:27',
            'modified' => '2019-04-16 07:23:27',
            'delete_flg' => '0'
        ],
        [
            'id' => 2,
            'food_division' => 1,
            'start_date' => '2020-02-25',
            'food_period_name' => 'カレー',
            'food_price' => 1000,
            'created' => '2019-04-16 07:23:27',
            'modified' => '2019-04-16 07:23:27',
            'delete_flg' => '0'
        ],
        [
            'id' => 3,
            'food_division' => 1,
            'start_date' => '2020-02-28',
            'food_period_name' => 'こいつも適用されない',
            'food_price' => 200,
            'created' => '2019-04-16 07:23:27',
            'modified' => '2019-04-16 07:23:27',
            'delete_flg' => '0'
        ],
    ];
}
