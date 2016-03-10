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

class ClassicalResult implements ParsingRuleInterace
{
    public function match(\DOMElement $node)
    {
        return ParsingRuleInterace::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        // find the tilte/url
        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        if (!$aTag) {
            return;
        }

        $url=$aTag->getAttribute('href');

         //if no protocole it means the result is a an relative path to google
        if ((!strpos($url, '://'))>0) {
            // todo URLArchive::copy
            $url = GoogleUrlArchive::fromString($dom->getEffectiveUrl()->resolve($url));
            if ($url->getResultType() == GoogleUrl::RESULT_TYPE_IMAGES) {
                $data = [
                    'snippet' => $node->C14N(),
                    'title'   => $aTag->nodeValue,
                    'url'     => $url
                    // TODO: image list
                ];
                $resultType = 'imageGroup';
            } else {
                return;
            }
        } else {
            $data = [
                'snippet' => $node->C14N(),
                'title'   => $aTag->nodeValue,
                'url'     => $url,
            ];

            $resultType = 'classical';
        }

        $item = new BaseResult($resultType, $data);
        $resultSet->addItem($item);
    }
}
