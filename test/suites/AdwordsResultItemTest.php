<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google;

use Serps\Core\Serp\BaseResult;
use Serps\SearchEngine\Google\AdwordsResultItem;

/**
 * @covers Serps\SearchEngine\Google\AdwordsResultItem
 */
class AdwordsResultItemTest extends \PHPUnit_Framework_TestCase
{

    public function testIs()
    {
        $item = new AdwordsResultItem('top', new BaseResult('foo', []));
        $this->assertTrue($item->is('top'));
        $this->assertTrue($item->is('foo'));
        $this->assertTrue($item->is('foo', 'top'));
        $this->assertTrue($item->is('foo', 'fake'));
        $this->assertFalse($item->is('fake'));
    }

    public function testGetTypes()
    {
        $item = new AdwordsResultItem('top', new BaseResult('foo', []));
        $this->assertEquals(['top', 'foo'], $item->getTypes(), '', 0.0, 10, true);
    }
}
