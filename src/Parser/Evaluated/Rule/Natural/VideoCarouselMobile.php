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

class VideoCarouselMobile implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $steps = ['version1', 'version2'];

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        if ($node->getChildren()->item(1)->getTagName()  == 'inline-video') {
            return self::RULE_MATCH_MATCHED;
        }

        $tagName = '';

        try {
            $tagName = $node->firstChild->tagName;
        } catch (Exception $e) {
            return self::RULE_MATCH_NOMATCH;
        }
        
        if ($tagName == 'video-voyager') {
            return self::RULE_MATCH_MATCHED;
        }
        
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        foreach ($this->steps as $functionName) {
            call_user_func_array([$this, $functionName], [$googleDOM, $node, $resultSet, $isMobile]);
        }
    }

    public function version1(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $aHrefs = $googleDOM->getXpath()->query('descendant::a[@class="ygih0"]', $node);
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

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL_MOBILE, $items));
    }

    public function version2(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $url = ((($node->getChildren()->item(1))->getChildren()->item(0))->getChildren()->item(0))->getChildren()->item(0)->getAttribute('data-id');

        if (empty($url)) {
            return;
        }

        $items[] = [
            'url' => $url,
            'height' => '',
        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL_MOBILE, $items));
    }

}
