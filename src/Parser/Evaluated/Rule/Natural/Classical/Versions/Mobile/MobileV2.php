<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Mobile;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\OrganicResultObject;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class MobileV2 implements ParsingRuleByVersionInterface
{
    public function parseNode(GoogleDom $dom, \DomElement $organicResult, OrganicResultObject $organicResultObject)
    {
        /* @var $aTag \DOMElement */
        $aTag = $dom->xpathQuery("descendant::*[
            contains(concat(' ', normalize-space(@class), ' '), ' d5oMvf KJDcUb ') or
            contains(concat(' ', normalize-space(@class), ' '), ' tKdlvb KJDcUb ') or
             @class='KJDcUb'
         ]/a", $organicResult);

        if (empty($aTag) && $organicResultObject->getLink() === null) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        $aTagNode = $aTag->item(0);

        if(!empty($aTagNode)) {
            $titleTag = $aTagNode->lastChild;
        }


        if($organicResultObject->getLink() === null) {
            $organicResultObject->setLink($dom->getUrl()->resolveAsString($aTag->item(0)->getAttribute('href')));
        }

        if (!$titleTag instanceof DomElement) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if($organicResultObject->getTitle() === null) {
            $organicResultObject->setTitle($titleTag->textContent);
        }

        $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' yDYNvb ')]",
            $organicResult);

        $descriptionTag = null;

        if ($descriptionNodes->length > 0) {
            $organicResultObject->setDescription($descriptionNodes->item(0)->textContent);
        }
    }

}
