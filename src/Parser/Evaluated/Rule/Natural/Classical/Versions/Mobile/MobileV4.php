<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Mobile;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\OrganicResultObject;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class MobileV4 implements ParsingRuleByVersionInterface
{
    public function parseNode(GoogleDom $dom, \DomElement $organicResult, OrganicResultObject $organicResultObject)
    {
        /* @var $aTag \DOMElement */
        $aTag = $dom->xpathQuery("descendant::*[
        contains(concat(' ', normalize-space(@class), ' '), ' C8nzq BmP5tf ') or
        @class='sXtWJb' or
        (contains(concat(' ', normalize-space(@class), ' '), ' BmP5tf ') and
         contains(concat(' ', normalize-space(@class), ' '), ' cz3goc '))
        ]", $organicResult);

        if (empty($aTag->length) && $organicResult->hasClass('zwqzjb')) {
            $aTag = $organicResult;
        }

        if (empty($aTag) && $organicResultObject->getLink() === null) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if(!$organicResult->hasClass('zwqzjb') && empty($aTag->item(0)) && $organicResultObject->getLink() === null) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        $titleTag = '';

        if(!($aTag instanceof DomElement) && !empty($aTag->item(0))) {
            $titleObj = $dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' MBeuO ')]", $aTag->item(0));
            if (!empty($titleObj->item(0))) {
                $titleTag =  $titleObj->item(0);
            } else {
                $titleTag = $aTag->item(0)->lastChild;
            }

        } else if ($aTag instanceof DomElement) {
            $titleObj =  $dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' pOOXW ')]", $aTag);
            if (!empty($titleObj->item(0))) {
                $titleTag =  $titleObj->item(0);
            } else {
                $titleTag = $aTag->item(0)->lastChild;
            }
        }



        if(!($aTag instanceof DomElement)  && $organicResultObject->getLink() === null) {
            $organicResultObject->setLink($dom->getUrl()->resolveAsString($aTag->item(0)->getAttribute('href')));
        } else if (($aTag instanceof DomElement)  && $organicResultObject->getLink() === null) {
            $organicResultObject->setLink($dom->getUrl()->resolveAsString($aTag->getAttribute('href')));
        }

        if (!$titleTag instanceof DomElement && !$titleTag instanceof \DOMText ) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if($organicResultObject->getTitle() === null) {
            $organicResultObject->setTitle($titleTag->textContent);
        }
        if (!($aTag instanceof DomElement) ) {
            $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' yDYNvb ')]",
                $organicResult);
        } else {
            $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' Kzca5 JQWoo ')]",
                $organicResult);
        }



        $descriptionTag = null;

        if ($descriptionNodes->length > 0) {
            $organicResultObject->setDescription($descriptionNodes->item(0)->textContent);
        }
    }

}
