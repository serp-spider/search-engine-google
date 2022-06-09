<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\Exception;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class VideoCarousel implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        $tagName = '';
        
        try {
            $tagName = $node->firstChild->tagName;
        } catch (Exception $e) {
            return self::RULE_MATCH_NOMATCH;
        }
        
        if (
            $tagName == 'video-voyager'
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $aHrefs = $googleDOM->getXpath()->query('descendant::a[@class="X5OiLe"]', $node);

        if ($aHrefs->length == 0) {
            return;
        }

        $items = [];

        foreach ($aHrefs as $url) {
            $items[] = [
                'url'    => $url->getAttribute('href'),
                'height' => '',
            ];
        }
        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL, $items));
    }
}
