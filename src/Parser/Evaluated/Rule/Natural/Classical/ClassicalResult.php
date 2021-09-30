<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinks;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBig;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksSmall;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResult implements ParsingRuleInterface
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
        $naturalResults = $dom->xpathQuery("descendant::div[@class='g']", $node);

        if ($naturalResults->length == 0) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        foreach ($naturalResults as $organicResult) {

            /* @var $aTag \DOMElement */
            $aTag = $dom->xpathQuery("descendant::*[(self::div)]/a", $organicResult)->item(0);

            if (!$aTag) {
                throw new InvalidDOMException('Cannot parse a classical result.');
            }

            $h3Tag = $dom->xpathQuery('descendant::h3', $organicResult)->item(0);

            if (!$h3Tag) {
                throw new InvalidDOMException('Cannot parse a classical result.');
            }

            $descriptionTag = $dom->xpathQuery("descendant::div[@class='IsZvec']", $organicResult)->item(0);

            $result = [
                'title'       => $h3Tag->textContent,
                'url'         => $dom->getUrl()->resolveAsString($aTag->getAttribute('href')),
                'description' => $descriptionTag ? $descriptionTag->textContent : null,
            ];

            $resultTypes = [NaturalResultType::CLASSICAL];

            $resultSet->addItem(new BaseResult($resultTypes, $result));

            if( $dom->xpathQuery("descendant::table[@class='jmjoTe']", $organicResult)->length >0) {
                (new SiteLinksBig())->parse($dom,$organicResult, $resultSet, false);
            }

            if( $dom->xpathQuery("descendant::div[@class='HiHjCd']", $organicResult)->length >0) {
                (new SiteLinksSmall())->parse($dom,$organicResult, $resultSet, false);
            }
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $this->parseNode($dom, $node, $resultSet);
    }
}
