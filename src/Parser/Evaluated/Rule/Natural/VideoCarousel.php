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

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        $tagName = '';
        $firstChild = $node->firstChild;

        if (empty($firstChild)) {
            return self::RULE_MATCH_NOMATCH;
        }

        $tagName = $firstChild->tagName;

        if ($tagName == 'video-voyager') {
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
            $theUrl = $url->getAttribute('href');
            if (empty($theUrl)) {
                continue;
            }
            $items[] = [
                'url'    => $theUrl,
                'height' => '',
            ];
        }
        if (!empty($items)) {
            $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }
    }
}
