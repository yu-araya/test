<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DayOffCalendarsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DayOffCalendarsTable Test Case
 */
class DayOffCalendarsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DayOffCalendarsTable
     */
    public $DayOffCalendars;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DayOffCalendars',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DayOffCalendars') ? [] : ['className' => DayOffCalendarsTable::class];
        $this->DayOffCalendars = TableRegistry::getTableLocator()->get('DayOffCalendars', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DayOffCalendars);

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
