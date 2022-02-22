<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FoodPeriodsFixture
 */
class FoodPeriodsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'food_division' => ['type' => 'integer', 'length' => 5, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '食事区分', 'precision' => null, 'autoIncrement' => null],
        'start_date' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '開始日', 'precision' => null],
        'food_period_name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_bin', 'comment' => '食事名', 'precision' => null, 'fixed' => null],
        'food_price' => ['type' => 'decimal', 'length' => 7, 'precision' => 0, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '価格'],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '登録日', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日', 'precision' => null],
        'delete_flg' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '削除フラグ', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
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
                'id' => 1,
                'food_division' => 1,
                'start_date' => '2020-11-09',
                'food_period_name' => 'Lorem ipsum dolor sit amet',
                'food_price' => 1.5,
                'created' => '2020-11-09 14:41:06',
                'modified' => '2020-11-09 14:41:06',
                'delete_flg' => 1,
            ],
        ];
        parent::init();
    }
}
