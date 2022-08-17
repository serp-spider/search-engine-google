<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class MapMobile implements ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;
    
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('img.wfAGXd', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $item = [
            'localPack' => function () use ($node, $dom) {
                $localPackNodes = $dom->cssQuery('.PX16ld', $node);
                $data = [];
                foreach ($localPackNodes as $localPack) {
                    $data[] = new BaseResult(NaturalResultType::MAP_PLACE, $this->parseItem($localPack, $dom));
                }
                return $data;
            },
            'mapUrl'    => function () use ($node, $dom) {
                return null;
            }

        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $item, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }

    private function parseItem($localPack, GoogleDom $dom)
    {

        return [
            'title' => function () use ($localPack, $dom) {
                return $dom->cssQuery('.kR1eme', $localPack)->getNodeAt(0)->getNodeValue();
            },
            'url' => function () use ($localPack, $dom) {
                $nodes = $dom->cssQuery('a', $localPack);

                $href = $nodes
                    ->getNodeAt(0)
                    ->getAttribute('href');

                if ($href) {
                    return $dom->getUrl()->resolveAsString($href);
                }
            },
            'street' => function () use ($localPack, $dom) {
                // TODO
                return null;
            },
            'stars' => function () use ($localPack, $dom) {
                $rating = $dom->cssQuery('.BTtC6e', $localPack)->getNodeAt(0)->getNodeValue();
                // transforms "4,4" to 4.4
                return $rating ? (float)str_replace(',', '.', $rating) : null;
            },

            'review' => function () use ($localPack, $dom) {
                $review = $dom->cssQuery(
                    '.BTtC6e',
                    $localPack
                )->getNodeAt(0);

                if ($review instanceof DomElement) {
                    $value = $review->parentNode->getNodeValue();
                } else {
                    return null;
                }

                if ($value && preg_match('/(\([0-9 ,\.]+\))/', $value, $matches)) {
                    // transform '(1 000)' or '(1,000)', etc... to 1000
                    return (int) preg_replace('/[^0-9]/', '', $matches[1]);
                }

                return null;
            },

            'phone' => function () use ($localPack, $dom) {
                return null;
            },
        ];
    }
}
