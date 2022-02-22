<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FoodHistoryReservationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FoodHistoryReservationsTable Test Case
 */
class FoodHistoryReservationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FoodHistoryReservationsTable
     */
    public $FoodHistoryReservations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FoodHistoryReservations',
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
        $config = TableRegistry::getTableLocator()->exists('FoodHistoryReservations') ? [] : ['className' => FoodHistoryReservationsTable::class];
        $this->FoodHistoryReservations = TableRegistry::getTableLocator()->get('FoodHistoryReservations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FoodHistoryReservations);

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
