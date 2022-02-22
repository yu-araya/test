<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FoodDivisionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FoodDivisionsTable Test Case
 */
class FoodDivisionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FoodDivisionsTable
     */
    public $FoodDivisions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FoodDivisions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('FoodDivisions') ? [] : ['className' => FoodDivisionsTable::class];
        $this->FoodDivisions = TableRegistry::getTableLocator()->get('FoodDivisions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FoodDivisions);

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
