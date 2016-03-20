<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\ResultType;

class ImageGroup implements ParsingRuleInterace
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') !== 'g') {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        /* @var $aTag \DOMElement */
        $aTag=$dom->getXpath()
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        if ($aTag) {
            $url = $aTag->getAttribute('href');

            if (strpos($url, '/search') == 0) {

                // todo URLArchive::copy
                $url = GoogleUrlArchive::fromString($dom->getEffectiveUrl()->resolve($url));

                if ($url->getResultType() == GoogleUrl::RESULT_TYPE_IMAGES) {
                    return  ParsingRuleInterace::RULE_MATCH_MATCHED;
                }

            }
        }


        return ParsingRuleInterace::RULE_MATCH_NOMATCH;

    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        // find the tilte/url
        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        $url=$aTag->getAttribute('href');

        $data = [
            'snippet' => $node->C14N(),
            'title'   => $aTag->nodeValue,
            'url'     => $url
            // TODO: image list
        ];
        $resultType = ResultType::IMAGE_GROUP;

        $item = new BaseResult($resultType, $data);
        $resultSet->addItem($item);
    }
}
