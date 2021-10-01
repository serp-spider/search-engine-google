<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBigMobile;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResultMobile implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->getAttribute('id') == 'rso') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $naturalResults = $dom->xpathQuery("descendant::div[@class='mnr-c xpd O9g5cc uUPGi']", $node);

        if ($naturalResults->length == 0) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        foreach ($naturalResults as $organicResult) {

            /* @var $aTag \DOMElement */
            $aTag = $dom->xpathQuery("descendant::*[(self::div)]/a", $organicResult)->item(0);

            if (!$aTag) {
                throw new InvalidDOMException('Cannot parse a classical result.');
            }

            $titleTag = $aTag->lastChild;

            if (!$titleTag instanceof  DomElement) {
                throw new InvalidDOMException('Cannot parse a classical result.');
            }

            $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' MUxGbd yDYNvb ')]", $organicResult);

            $descriptionTag = null;

            if($descriptionNodes->length >0) {
                $descriptionTag = $descriptionNodes->item(0)->textContent;
            }

            $result = [
                'title'       => $titleTag->textContent,
                'url'         => $dom->getUrl()->resolveAsString($aTag->getAttribute('href')),
                'description' => $descriptionTag,
            ];

            $resultTypes = [NaturalResultType::CLASSICAL];

            $resultSet->addItem(new BaseResult($resultTypes, $result));

            if( $dom->xpathQuery("descendant::div[@class='MUxGbd v0nnCb lyLwlc']", $organicResult->parentNode->parentNode)->length >0) {
                (new SiteLinksBigMobile())->parse($dom,$organicResult->parentNode->parentNode, $resultSet, false);
            }
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $this->parseNode($dom, $node, $resultSet);
    }
}
