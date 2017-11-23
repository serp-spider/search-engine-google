<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

class ComposedTopStories implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('._Fzo ._HSj', $node)->length == 1
            && $dom->cssQuery('.dbsr', $node)->length > 0
            // Dont use _yyh, _JTg or _bfj class because it's common to all carousel
        ) {
            return self::RULE_MATCH_MATCHED;
        }


        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = new BaseResult(
            [NaturalResultType::TOP_STORIES, NaturalResultType::TOP_STORIES_COMPOSED],
            $this->parseNode($dom, $node)
        );
        $resultSet->addItem($item);
    }

    private function parseNode(GoogleDom $dom, $node)
    {
        return [

            'isCarousel' => true,
            'isVertical' => true,

            'news' => function () use ($dom, $node) {
                $news =  $this->parseVerticalResults($dom, $node);
                $news = array_merge($news, $this->parseCarouselResults($dom, $node));

                $resultSet = new ResultSet();
                $resultSet->addItems($news);

                return $resultSet;
            },
        ];
    }

    private function parseVerticalResults(GoogleDom $dom, DomElement $node)
    {
        $news = [];
        $nodes = $dom->cssQuery('.dbsr', $node);

        foreach ($nodes as $newsNode) {
            $news[] = new BaseResult(NaturalResultType::TOP_STORIES_NEWS_VERTICAL, [
                'title' => function () use ($dom, $newsNode) {
                    $el = $dom->cssQuery('._eNq>span', $newsNode)->item(0);
                    return $el->nodeValue;
                },
                'url' => function () use ($dom, $newsNode) {
                    $el = $dom->cssQuery('._rNq>a', $newsNode)->item(0);
                    return $el->getAttribute('href');
                }
            ]);
        }

        return $news;
    }

    private function parseCarouselResults(GoogleDom $dom, DomElement $node)
    {
        $news = [];
        $nodes = $dom->cssQuery('._HSj ._ERj', $node);

        foreach ($nodes as $newsNode) {
            $news[] = new BaseResult(NaturalResultType::TOP_STORIES_NEWS_CAROUSEL, [
                'title' => function () use ($dom, $newsNode) {
                    $el = $dom->cssQuery('._IRj', $newsNode)->item(0);
                    return $el->nodeValue;
                },
                'url' => function () use ($dom, $newsNode) {
                    $el = $dom->cssQuery('g-inner-card._KBh>a', $newsNode)->item(0);
                    return $el->getAttribute('href');
                }
            ]);
        }

        return $news;
    }
}
