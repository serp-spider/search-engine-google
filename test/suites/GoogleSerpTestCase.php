<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\SearchEngine\Google;

use Serps\Core\Serp\ResultDataInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class GoogleSerpTestCase extends \PHPUnit_Framework_TestCase
{

    public function assertResultHasTypes(array $types, ResultDataInterface $result)
    {
        foreach ($types as $type) {
            $typeValue = constant(NaturalResultType::class . '::' . $type);
            $this->assertTrue($result->is($typeValue), 'Expects that item[' . implode(', ', $result->getTypes()) . '] has the type: ' . $typeValue);
        }
    }

    public function assertResultHasData(array $dataArray, $result)
    {
        foreach ($dataArray as $k => $data) {
            if (is_array($data)) {
                if (is_object($result)) {
                    $this->assertResultHasData($data, $result->$k);
                } elseif (is_array($result)) {
                    $this->assertResultHasData($data, $result[$k]);
                } else {
                    $this->fail('Asserting that data has key ' . $k);
                }
            } else {
                $this->assertEquals($data, $result->$k);
            }
        }
    }

    public function assertResultDataCount(array $dataArray, $result)
    {
        foreach ($dataArray as $k => $data) {
            if (is_array($data)) {
                if (is_object($result)) {
                    $this->assertResultHasData($data, $result->$k);
                } elseif (is_array($result)) {
                    $this->assertResultHasData($data, $result[$k]);
                } else {
                    $this->fail('Asserting that data has key ' . $k);
                }
            } else {
                $this->assertCount($data, $result->$k);
            }
        }
    }
}
