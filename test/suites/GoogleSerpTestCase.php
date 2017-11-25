<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\SearchEngine\Google;

use Serps\Core\Media\File;
use Serps\Core\Serp\ResultDataInterface;
use Serps\Core\Serp\ResultSetInterface;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\Core\Media\MediaInterface;

class GoogleSerpTestCase extends \PHPUnit_Framework_TestCase
{

    public function assertResultHasTypes(array $types, ResultDataInterface $result, $file, $index)
    {

        if (is_int($index)) {
            $index = $index + 1;
        }

        $this->assertCount(
            count($types),
            $result->getTypes(),
            'Type count does not match. '
            . 'Expects that '
            . 'item[' . implode(', ', $result->getTypes()) . '] '
            . 'has types:[' . implode(', ', $types) . '] '
            . 'Using file ' . $file . ' result #' . $index
        );



        foreach ($types as $type) {
            $typeValue = constant(NaturalResultType::class . '::' . $type);
            $this->assertTrue(
                $result->is($typeValue),
                'Expects that '
                . 'item[' . implode(', ', $result->getTypes()) . '] has the type: ' . $typeValue
                . '. Using file ' . $file . ' result #' . $index
            );
        }
    }

    public function assertResultDoesNotHaveTypes(array $types, ResultDataInterface $result)
    {
        foreach ($types as $type) {
            $typeValue = constant(NaturalResultType::class . '::' . $type);
            $this->assertFalse($result->is($typeValue), 'Expects that item[' . implode(', ', $result->getTypes()) . '] does NOT have the type: ' . $typeValue);
        }
    }

    public function assertResultHasData(array $dataArray, $result, $currentPath = null)
    {
        if (null == $currentPath) {
            $currentPath = '/';
        }

        foreach ($dataArray as $k => $data) {
            $currentPathForItem = $currentPath . $k . '/';

            if ($k === 'types()') {
                if (is_object($result) && $result instanceof ResultDataInterface) {
                    $this->assertResultHasTypes($data, $result, $currentPathForItem, $k);
                } else {
                    $this->fail('Asserting that data is instance of ResultDataInterface (evaluating types()). Path: "' . $currentPathForItem . '"');
                }
            } else {
                if (is_array($data)) {
                    if (is_object($result)) {
                        if ($result instanceof ResultSetInterface) {
                            $this->assertResultHasData($data, $result[$k], $currentPathForItem);
                        } else {
                            $this->assertResultHasData($data, $result->$k, $currentPathForItem);
                        }
                    } elseif (is_array($result)) {
                        $this->assertResultHasData($data, $result[$k], $currentPathForItem);
                    } else {
                        $this->fail('Asserting that data has key "' . $k . '"". Path: "' . $currentPathForItem . '"');
                    }
                } else {
                    if (!is_object($result)) {
                        $this->fail('Data is not an object. Evaluating key "' . $k . '"". Path: "' . $currentPathForItem . '"');
                    }
                    $this->assertEquals($data, $result->$k, 'Checking key "' . $k . '" for equality. Path: "' . $currentPathForItem . '"');
                }
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

    public function assertResultHasDataMedia(array $dataArray, $result)
    {
        foreach ($dataArray as $k => $data) {
            if (is_array($data)) {
                if (is_object($result)) {
                    $this->assertResultHasDataMedia($data, $result->$k);
                } elseif (is_array($result)) {
                    $this->assertResultHasDataMedia($data, $result[$k]);
                } else {
                    $this->fail('Asserting that data has key ' . $k);
                }
            } else {
                $media = $result->$k;
                $this->assertInstanceOf(MediaInterface::class, $media);
                $expected = new File($data);
                $this->assertEquals($expected->asString(), $media->asString());
            }
        }
    }
}
