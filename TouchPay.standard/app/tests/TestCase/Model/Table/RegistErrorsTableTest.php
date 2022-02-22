<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RegistErrorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RegistErrorsTable Test Case
 */
class RegistErrorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RegistErrorsTable
     */
    public $RegistErrors;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.RegistErrors',
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
        $config = TableRegistry::getTableLocator()->exists('RegistErrors') ? [] : ['className' => RegistErrorsTable::class];
        $this->RegistErrors = TableRegistry::getTableLocator()->get('RegistErrors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RegistErrors);

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
