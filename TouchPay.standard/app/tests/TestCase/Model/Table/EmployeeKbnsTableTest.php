<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EmployeeKbnsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EmployeeKbnsTable Test Case
 */
class EmployeeKbnsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EmployeeKbnsTable
     */
    public $EmployeeKbns;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.EmployeeKbns',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EmployeeKbns') ? [] : ['className' => EmployeeKbnsTable::class];
        $this->EmployeeKbns = TableRegistry::getTableLocator()->get('EmployeeKbns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EmployeeKbns);

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
