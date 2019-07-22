<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Exception;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

/**
 * A slightly different implementation of the twitter carousel that is found on mobile results under the element ._Z1m
 */
class TweetsCarouselZ1m implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        if ($node->childNodes->length == 1) {
            $childNode = $node->getChildren()->getNodeAt(0);

            $subChildNode = $childNode->getChildren()->getNodeAt(0);

            if ($childNode->hasClass('_Z1m') && $subChildNode->hasClass('_ujp')) {
                return self::RULE_MATCH_MATCHED;
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $item = new BaseResult(NaturalResultType::TWEETS_CAROUSEL, [

            'url' => function () use ($dom, $node) {
                $res = $dom->cssQuery('._Z1m>._ujp>a', $node);

                if ($res->length == 1) {
                    return $res->item(0)->getAttribute('href');
                } else {
                    throw new InvalidDOMException('Cannot parse url for twitter carousel.');
                }
            },

            'title' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('._ees', $node)
                    ->item(0)
                    ->nodeValue;
            },

            'destination' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('span._Clt', $node)
                    ->item(0)
                    ->nodeValue;
            },

            'user' => function (BaseResult $result) {
                $url = $result->getDataValue('url');

                $match = preg_match('~twitter.com/([^/?#]+)~', $url, $matches);

                if ($match) {
                    return '@' . $matches[1];
                }

                return null;
            }
        ]);
        $resultSet->addItem($item);
    }
}
