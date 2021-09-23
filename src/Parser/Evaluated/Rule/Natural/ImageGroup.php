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

class ImageGroup implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('id') == 'iur' &&
            (   // Mobile
                $node->parentNode->hasAttribute('jsmodel') ||
                // Desktop
                $node->parentNode->parentNode->hasAttribute('jsmodel')
            )
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $images = $googleDOM->getXpath()->query('descendant::div[@data-lpage]', $node->lastChild);
        $item = [];

        if($images->length> 0) {
            foreach ($images as $imageNode) {
                $item['images'][] = $this->parseItem($googleDOM, $imageNode);
            }
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $item));
    }

    /**
     * @param GoogleDom $googleDOM
     * @param \DOMElement $imgNode
     *
     * @return BaseResult
     */
    private function parseItem(GoogleDom $googleDOM, \DOMElement $imgNode)
    {
        $data =  $imgNode->getAttribute('data-lpage');

        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, [$data]);
    }
}
