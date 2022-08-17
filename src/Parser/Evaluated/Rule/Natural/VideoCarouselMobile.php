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
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        $children = $node->getChildren();

        if (empty($children)) {
            return self::RULE_MATCH_NOMATCH;
        }

        if (($node->getChildren()->item(1) && $node->getChildren()->item(1)->getTagName()  == 'inline-video')
            || ($node->getChildren()->item(0) && $node->getChildren()->item(0)->getTagName() == 'video-voyager')) {
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
            $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL_MOBILE, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }

    }

    public function version2(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $child = $googleDOM->getXpath()->query('descendant::div[@class="O6s9Nd"]', $node);

        if (empty($child) || empty($child->item(1))) {
            return;
        }

        $url = $child->item(1)->getAttribute('data-id');

        if (empty($url)) {
            return;
        }

        $items[] = [
            'url' => $url,
            'height' => '',
        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_CAROUSEL_MOBILE, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }

}
