<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google;

use Serps\Core\Serp\BaseResult;
use Serps\SearchEngine\Google\AdwordsResultItem;
use Serps\SearchEngine\Google\AdwordsSectionResultSet;

/**
 * @covers Serps\SearchEngine\Google\AdwordsSectionResultSet
 */
class AdwordsSectionResultSetTest extends \PHPUnit_Framework_TestCase
{

    public function testAddItem()
    {
        $resultSet = new AdwordsSectionResultSet('top');
        $result = new BaseResult('foo', []);

        $resultSet->addItem($result);

        $testResult = $resultSet->getItems()[0];

        $this->assertTrue($testResult instanceof AdwordsResultItem);
        $this->assertTrue($testResult->is('top'));
    }
}
