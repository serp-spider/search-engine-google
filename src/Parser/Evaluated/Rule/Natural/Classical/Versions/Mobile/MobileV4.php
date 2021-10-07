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
        $aTag = $dom->xpathQuery("descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' C8nzq BmP5tf ')]", $organicResult);

        if (empty($aTag)) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        $titleTag = $aTag->item(0)->lastChild;

        if($organicResultObject->getLink() === null) {
            $organicResultObject->setLink($dom->getUrl()->resolveAsString($aTag->item(0)->getAttribute('href')));
        }

        if (!$titleTag instanceof DomElement) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        if($organicResultObject->getTitle() === null) {
            $organicResultObject->setTitle($titleTag->textContent);
        }

        $descriptionNodes = $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' MUxGbd yDYNvb ')]",
            $organicResult);

        $descriptionTag = null;

        if ($descriptionNodes->length > 0) {
            $organicResultObject->setDescription($descriptionNodes->item(0)->textContent);
        }
    }

}
