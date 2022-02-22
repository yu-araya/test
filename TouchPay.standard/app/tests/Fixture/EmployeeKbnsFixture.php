<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EmployeeKbnsFixture
 */
class EmployeeKbnsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'employee_kbn' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員区分', 'precision' => null, 'fixed' => null],
        'employee_kbn_name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_bin', 'comment' => '社員区分名', 'precision' => null, 'fixed' => null],
        'food_allowance_flg' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'null' => false, 'default' => '0', 'collate' => 'utf8_general_ci', 'comment' => '食事手当フラグ', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '登録日', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日', 'precision' => null],
        'delete_flg' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '削除フラグ', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'employee_kbn' => ['type' => 'unique', 'columns' => ['employee_kbn'], 'length' => []],
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
                'employee_kbn' => 'Lo',
                'employee_kbn_name' => 'Lorem ipsum dolor sit amet',
                'food_allowance_flg' => 'L',
                'created' => '2020-11-09 14:41:05',
                'modified' => '2020-11-09 14:41:05',
                'delete_flg' => 1,
            ],
        ];
        parent::init();
    }
}
