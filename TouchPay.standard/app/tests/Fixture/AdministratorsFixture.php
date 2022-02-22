<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AdministratorsFixture
 */
class AdministratorsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'login_name' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '管理者ID', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'precision' => null, 'fixed' => null],
        'role' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ロール', 'precision' => null],
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
                'login_name' => 'Lorem ip',
                'password' => 'Lorem ip',
                'role' => 'L',
                'created' => '2020-11-09 14:40:27',
                'modified' => '2020-11-09 14:40:27',
                'delete_flg' => 1,
            ],
        ];
        parent::init();
    }
}
