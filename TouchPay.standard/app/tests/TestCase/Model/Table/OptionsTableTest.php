<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OptionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OptionsTable Test Case
 */
class OptionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OptionsTable
     */
    public $Options;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Options',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Options') ? [] : ['className' => OptionsTable::class];
        $this->Options = TableRegistry::getTableLocator()->get('Options', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Options);

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
