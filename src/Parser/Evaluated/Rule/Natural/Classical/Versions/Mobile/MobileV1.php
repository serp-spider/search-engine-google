<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Mobile;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\OrganicResultObject;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class MobileV1 implements ParsingRuleByVersionInterface
{
    public function parseNode(GoogleDom $dom, \DomElement $organicResult, OrganicResultObject $organicResultObject)
    {
        /* @var $aTag \DOMElement */
        //tKdlvb KJDcUb
        $aTag = $dom->xpathQuery("descendant::*[
            contains(concat(' ', normalize-space(@class), ' '), ' d5oMvf KJDcUb ') or
            contains(concat(' ', normalize-space(@class), ' '), ' tKdlvb KJDcUb ') or
             @class='KJDcUb'
         ]/a", $organicResult);

        $elemNode = $dom->xpathQuery("descendant::*[@class='pXvdUe']", $organicResult);

        if ($elemNode->length > 0) {
            $aTag = $dom->xpathQuery("descendant::a", $elemNode->item(0));

            if ($aTag->length > 0) {
                $aTag = $aTag->item(0);
            }
        }

        if (empty($aTag)) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if($organicResultObject->getLink() === null) {
            $organicResultObject->setLink($aTag->getAttribute('href'));
        }

        $titleTag = $aTag->lastChild;

        if (!$titleTag instanceof DomElement) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if($organicResultObject->getTitle() === null) {
            $organicResultObject->setTitle($titleTag->textContent);
        }

        $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' MUxGbd yDYNvb ')]", $organicResult);

        if ($descriptionNodes->length > 0) {
            $organicResultObject->setDescription($descriptionNodes->item(0)->textContent);
        }
    }
}
