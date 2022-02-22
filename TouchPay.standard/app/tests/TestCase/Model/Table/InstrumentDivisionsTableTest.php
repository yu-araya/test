<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InstrumentDivisionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InstrumentDivisionsTable Test Case
 */
class InstrumentDivisionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\InstrumentDivisionsTable
     */
    public $InstrumentDivisions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InstrumentDivisions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('InstrumentDivisions') ? [] : ['className' => InstrumentDivisionsTable::class];
        $this->InstrumentDivisions = TableRegistry::getTableLocator()->get('InstrumentDivisions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InstrumentDivisions);

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
