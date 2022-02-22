<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FoodHistoryInfosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FoodHistoryInfosTable Test Case
 */
class FoodHistoryInfosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FoodHistoryInfosTable
     */
    public $FoodHistoryInfos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FoodHistoryInfos',
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
        $config = TableRegistry::getTableLocator()->exists('FoodHistoryInfos') ? [] : ['className' => FoodHistoryInfosTable::class];
        $this->FoodHistoryInfos = TableRegistry::getTableLocator()->get('FoodHistoryInfos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FoodHistoryInfos);

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
