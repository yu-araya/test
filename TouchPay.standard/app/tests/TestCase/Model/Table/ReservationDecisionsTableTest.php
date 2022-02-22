<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReservationDecisionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReservationDecisionsTable Test Case
 */
class ReservationDecisionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReservationDecisionsTable
     */
    public $ReservationDecisions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ReservationDecisions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ReservationDecisions') ? [] : ['className' => ReservationDecisionsTable::class];
        $this->ReservationDecisions = TableRegistry::getTableLocator()->get('ReservationDecisions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ReservationDecisions);

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
