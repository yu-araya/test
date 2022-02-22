<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FoodHistoryReservationsFixture
 */
class FoodHistoryReservationsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'employee_id' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'employee_kbn' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'food_division' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'target_date' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'reason' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'data_type' => ['type' => 'string', 'length' => 1, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'food_cost' => ['type' => 'decimal', 'length' => 7, 'precision' => 0, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => ''],
        '_options' => [
            'engine' => null,
            'collation' => null
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'employee_id' => 'Lorem ip',
                'employee_kbn' => 'Lo',
                'food_division' => 1,
                'target_date' => '2020-11-09 14:41:06',
                'reason' => 'Lorem ipsum dolor sit amet',
                'data_type' => 'L',
                'food_cost' => 1.5,
            ],
        ];
        parent::init();
    }
}
