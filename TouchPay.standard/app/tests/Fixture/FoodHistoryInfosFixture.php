<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FoodHistoryInfosFixture
 */
class FoodHistoryInfosFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '食事履歴番号', 'autoIncrement' => true, 'precision' => null],
        'employee_id' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員コード', 'precision' => null, 'fixed' => null],
        'employee_kbn' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員区分', 'precision' => null, 'fixed' => null],
        'ic_card_number' => ['type' => 'string', 'length' => 16, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ICカード番号', 'precision' => null, 'fixed' => null],
        'instrument_division' => ['type' => 'integer', 'length' => 5, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '機器区分', 'precision' => null, 'autoIncrement' => null],
        'food_division' => ['type' => 'integer', 'length' => 5, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '食事区分', 'precision' => null, 'autoIncrement' => null],
        'reason' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '理由', 'precision' => null, 'fixed' => null],
        'card_recept_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'カード受付時間', 'precision' => null],
        'state_flg' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'null' => false, 'default' => '0', 'collate' => 'utf8_general_ci', 'comment' => '状態フラグ', 'precision' => null],
        'food_cost' => ['type' => 'decimal', 'length' => 7, 'precision' => 0, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '金額'],
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
                'employee_id' => 'Lorem ip',
                'employee_kbn' => 'Lo',
                'ic_card_number' => 'Lorem ipsum do',
                'instrument_division' => 1,
                'food_division' => 1,
                'reason' => 'Lorem ipsum dolor sit amet',
                'card_recept_time' => '2020-11-09 14:41:06',
                'state_flg' => 'L',
                'food_cost' => 1.5,
                'created' => '2020-11-09 14:41:06',
                'modified' => '2020-11-09 14:41:06',
                'delete_flg' => 1,
            ],
        ];
        parent::init();
    }
}
