<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EmployeeInfosFixture
 */
class EmployeeInfosFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'employee_id' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員コード', 'precision' => null, 'fixed' => null],
        'employee_kbn' => ['type' => 'string', 'length' => 2, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '社員区分', 'precision' => null, 'fixed' => null],
        'employee_name1' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '氏名', 'precision' => null, 'fixed' => null],
        'employee_name2' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '所属', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 10, 'fixed' => true, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '暗証番号', 'precision' => null],
        'dining_license_flg' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'null' => true, 'default' => '0', 'collate' => 'utf8_general_ci', 'comment' => '社員食堂使用許可フラグ', 'precision' => null],
        'dining_licensed_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '社員食堂使用不可設定日', 'precision' => null],
        'ic_card_number' => ['type' => 'string', 'length' => 16, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ICカード番号', 'precision' => null, 'fixed' => null],
        'iccard_valid_s_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '有効期間（開始）', 'precision' => null],
        'iccard_valid_e_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '有効期間（終了）', 'precision' => null],
        'ic_card_number2' => ['type' => 'string', 'length' => 16, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ICカード番号２', 'precision' => null, 'fixed' => null],
        'iccard_valid_s_time2' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '有効期間（開始）２', 'precision' => null],
        'iccard_valid_e_time2' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '有効期間（終了）２', 'precision' => null],
        'memo' => ['type' => 'string', 'length' => 40, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '備考', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '登録日', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日', 'precision' => null],
        'delete_flg' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '削除フラグ', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'employee_id' => ['type' => 'unique', 'columns' => ['employee_id'], 'length' => []],
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
                'employee_name1' => 'Lorem ipsum dolor sit amet',
                'employee_name2' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ip',
                'dining_license_flg' => 'L',
                'dining_licensed_date' => '2020-11-09',
                'ic_card_number' => 'Lorem ipsum do',
                'iccard_valid_s_time' => '2020-11-09 14:41:05',
                'iccard_valid_e_time' => '2020-11-09 14:41:05',
                'ic_card_number2' => 'Lorem ipsum do',
                'iccard_valid_s_time2' => '2020-11-09 14:41:05',
                'iccard_valid_e_time2' => '2020-11-09 14:41:05',
                'memo' => 'Lorem ipsum dolor sit amet',
                'created' => '2020-11-09 14:41:05',
                'modified' => '2020-11-09 14:41:05',
                'delete_flg' => 1,
            ],
        ];
        parent::init();
    }
}
