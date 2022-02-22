<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RegistErrorsFixture
 */
class RegistErrorsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'occurrence_datetime' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '発生日時', 'precision' => null],
        'function_name' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '処理内容', 'precision' => null, 'fixed' => null],
        'error_level' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'エラーレベル', 'precision' => null, 'fixed' => null],
        'reason' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'エラー内容', 'precision' => null, 'fixed' => null],
        'employee_id' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員番号', 'precision' => null, 'fixed' => null],
        'ic_card_number' => ['type' => 'string', 'length' => 16, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ICカード番号', 'precision' => null, 'fixed' => null],
        'instrument_division' => ['type' => 'integer', 'length' => 5, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '機器番号', 'precision' => null, 'autoIncrement' => null],
        'food_division' => ['type' => 'integer', 'length' => 5, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'メニューID', 'precision' => null, 'autoIncrement' => null],
        'card_recept_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'カードタッチ日時', 'precision' => null],
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
                'occurrence_datetime' => '2020-11-09 14:41:07',
                'function_name' => 'Lorem ip',
                'error_level' => 'Lorem ipsum dolor sit amet',
                'reason' => 'Lorem ipsum dolor sit amet',
                'employee_id' => 'Lorem ip',
                'ic_card_number' => 'Lorem ipsum do',
                'instrument_division' => 1,
                'food_division' => 1,
                'card_recept_time' => '2020-11-09 14:41:07',
            ],
        ];
        parent::init();
    }
}
