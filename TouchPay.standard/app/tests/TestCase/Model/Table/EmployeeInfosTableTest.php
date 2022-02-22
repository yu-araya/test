<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EmployeeInfosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EmployeeInfosTable Test Case
 */
class EmployeeInfosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EmployeeInfosTable
     */
    public $EmployeeInfos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.EmployeeInfos',
        'app.Employees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EmployeeInfos') ? [] : ['className' => EmployeeInfosTable::class];
        $this->EmployeeInfos = TableRegistry::getTableLocator()->get('EmployeeInfos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EmployeeInfos);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
