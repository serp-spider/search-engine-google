<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Css;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;

class Shopping implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        $class = $node->getAttribute('class');

        if (strpos(' ' . $class . ' ', ' _oc ')) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }
    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'title' => function () use ($googleDOM, $node) {
                $aTag = $googleDOM->getXpath()->query('descendant::h3/a[2]', $node)->item(0);
                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
            'url' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->getXpath()->query('descendant::h3/a[2]', $node)->item(0);
                if (!$aTag) {
                    return $googleDOM->getUrl()->resolve('/');
                }

                return $googleDOM->getUrl()->resolve($aTag->getAttribute('href'));
            },
            'visurl' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->getXpath()->query(
                    Css::toXPath('div.ads-visurl>cite'),
                    $node
                )->item(0);

                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;

            },
            'description' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->getXpath()->query(
                    Css::toXPath('div.ads-creative'),
                    $node
                )->item(0);

                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;

            },
        ];

//        $xpathCards = "descendant::ul[@class='rg_ul']/div[@class='_ZGc bili uh_r rg_el ivg-i']//a";
//        $imageNodes = $googleDOM->getXpath()->query($xpathCards, $node);
//        foreach ($imageNodes as $imgNode) {
//            $item['images'][] = $this->parseItem($googleDOM, $imgNode);
//
//        }
        $resultSet->addItem(new BaseResult(AdwordsResultType::SHOPPING_GROUP, $item));
    }
}
