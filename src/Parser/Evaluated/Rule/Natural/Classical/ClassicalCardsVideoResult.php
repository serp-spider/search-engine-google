<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;

class ClassicalCardsVideoResult extends ClassicalCardsResult
{

    public function match(GoogleDom $dom, DomElement $node)
    {
        $match = parent::match($dom, $node);
        if ($match === self::RULE_MATCH_MATCHED) {
            if ($dom->cssQuery('.A995L', $node)->length) {
                return self::RULE_MATCH_MATCHED;
            }
            return self::RULE_MATCH_NOMATCH;
        } else {
            return $match;
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $resultTypes = [NaturalResultType::CLASSICAL, NaturalResultType::CLASSICAL_VIDEO];

        $item = new BaseResult($resultTypes, [
            'title' => function () use ($dom, $node) {
                return $dom->cssQuery('h3 a', $node)->getNodeAt(0)->getNodeValue();
            },
            'url' => function () use ($dom, $node) {
                return $dom->getUrl()->resolveAsString(
                    $dom->cssQuery('h3 a', $node)->getNodeAt(0)->getAttribute('href')
                );
            },
            'destination' => function () use ($dom, $node) {
                return $dom->cssQuery('.RXIhdf', $node)->getNodeAt(0)->getNodeValue();
            },
            'description' => '',
            'isAmp' => false,
            'videoLarge' => false,
            'videoCover' => function () use ($dom, $node) {
                return MediaFactory::createMediaFromSrc(
                    $dom->cssQuery('.A995L a img', $node)->getNodeAt(0)->getAttribute('src')
                );
            }
        ]);
        $resultSet->addItem($item);
    }
}
