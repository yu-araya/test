<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReservationInfosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReservationInfosTable Test Case
 */
class ReservationInfosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReservationInfosTable
     */
    public $ReservationInfos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ReservationInfos',
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
        $config = TableRegistry::getTableLocator()->exists('ReservationInfos') ? [] : ['className' => ReservationInfosTable::class];
        $this->ReservationInfos = TableRegistry::getTableLocator()->get('ReservationInfos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ReservationInfos);

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
