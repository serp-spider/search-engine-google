<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

class AdwordsItemMobile implements ParsingRuleInterface
{

    /**
     * @inheritdoc
     */
    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->hasClass('ads-fr')) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    /**
     * @inheritdoc
     */
    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'title' => function () use ($googleDOM, $node) {
                $aTag = $googleDOM->cssQuery('a .MUxGbd.v0nnCb', $node)->item(0);
                if (!$aTag) {
                    throw new InvalidDOMException('Cannot find title for mobile adwords.');
                }
                return $aTag->nodeValue;
            },
            'url' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->cssQuery('a', $node)->item(0);
                if (!$aTag) {
                    throw new InvalidDOMException('Cannot find ads anchor');
                }
                return $googleDOM->getUrl()->resolveAsString($aTag->getAttribute('href'));
            },
            'visurl' => function () use ($node, $googleDOM) {
                return $googleDOM->cssQuery('.qzEoUe', $node)->getNodeAt(0)->getNodeValue();
            },
            'description' => function () use ($node, $googleDOM) {
                return $googleDOM->cssQuery('div.BmP5tf>div.MUxGbd', $node)
                    ->getNodeAt(0)
                    ->getNodeValue();
            },
        ];

        $resultSet->addItem(new BaseResult(AdwordsResultType::AD, $item));
    }
}
