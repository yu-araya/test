<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LoginHistorysTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LoginHistorysTable Test Case
 */
class LoginHistorysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LoginHistorysTable
     */
    public $LoginHistorys;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.LoginHistorys',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('LoginHistorys') ? [] : ['className' => LoginHistorysTable::class];
        $this->LoginHistorys = TableRegistry::getTableLocator()->get('LoginHistorys', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LoginHistorys);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
