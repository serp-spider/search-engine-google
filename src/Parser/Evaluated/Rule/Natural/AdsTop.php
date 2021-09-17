<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class AdsTop implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if($node->getTagName() !='div') {
            return self::RULE_MATCH_NOMATCH;
        }

        if ($node->parentNode->getAttribute('id') == 'tads') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $link = $googleDOM->getXpath()->query('descendant::a', $node)->item(0)->getAttribute('href');

        if($resultSet->hasType('image_group')) {

        } else {
            $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, [$link]));
        }
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DOMElement $imgNode
     * @return array
     */
    private function parseItem(GoogleDom $googleDOM, \DOMElement $imgNode)
    {
        $data =  $imgNode->getAttribute('data-lpage');


        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, [$data]);
    }
}
