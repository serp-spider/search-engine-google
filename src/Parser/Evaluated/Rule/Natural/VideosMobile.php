<?php
namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

class VideosMobile implements ParsingRuleInterface
{
    protected $steps = ['version1', 'version2', 'version3'];
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('cawG4b') && $node->hasClass('OvQkSb')) {
            return self::RULE_MATCH_MATCHED;
        }

        if ($node->hasClass('uVMCKf') && $node->hasClass('mnr-c')) {
            return self::RULE_MATCH_MATCHED;
        }

        if ($node->hasClass('HD8Pae') && $node->hasClass('mnr-c')) {
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

    protected function version1(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $data = [];

        if($node->parentNode->tagName !='a') {
            return;
        }

        $data[] = ['url' => $node->parentNode->getAttribute('href')];

        $resultSet->addItem(new BaseResult([NaturalResultType::VIDEOS_MOBILE], $data, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }

    protected function version2(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $videosContainer = $googleDOM->getXpath()->query("descendant::video-voyager", $node);

        if ($videosContainer->length ==0) {
            return;
        }

        $data = [];

        foreach ($videosContainer as $videoNode) {
            $url = $googleDOM->getXpath()->query("descendant::a", $videoNode)->item(0);

            $data[] = ['url'=>$url->getAttribute('href')];
        }

        $resultSet->addItem(new BaseResult([NaturalResultType::VIDEOS_MOBILE], $data, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }

    protected function version3(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $videosPlayerBtns = $googleDOM->getXpath()->query('descendant::span[@class="OPkOif"]', $node);

        if ($videosPlayerBtns->length ==0) {
            return;
        }

        $data = [];

        foreach ($videosPlayerBtns as $videoBtn) {
            $url = $videoBtn->parentNode->parentNode->parentNode->parentNode->parentNode;

            $data[] = ['url'=>$url->getAttribute('href')];
        }

        $resultSet->addItem(new BaseResult([NaturalResultType::VIDEOS_MOBILE], $data, $nod, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }
}
