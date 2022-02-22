<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FoodPeriodsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FoodPeriodsTable Test Case
 */
class FoodPeriodsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FoodPeriodsTable
     */
    public $FoodPeriods;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FoodPeriods',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('FoodPeriods') ? [] : ['className' => FoodPeriodsTable::class];
        $this->FoodPeriods = TableRegistry::getTableLocator()->get('FoodPeriods', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FoodPeriods);

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
