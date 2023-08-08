<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Monolog\Logger;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class ClassicalResultEngine
{
    use \Serps\SearchEngine\Google\Parser\Helper\Log;

    protected $resultType = NaturalResultType::CLASSICAL;

    /**
     * @param Logger|null $logger Monolog log channel dependency
     */
    public function __construct(Logger $logger = null)
    {
        $this->initLogger($logger);
    }

    protected function parseNode(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k) {}

    protected function parseNodeWithRules(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {
        $organicResultObject = new OrganicResultObject();

        /** @var ParsingRuleByVersionInterface $versionRule */
        foreach ($this->getRules() as $versionRule) {

            try {
                $versionRule->parseNode($dom, $organicResult, $organicResultObject);
            } catch (\Throwable $exception) {
                continue;
            }
        }

        if ($organicResultObject->getLink() === null || $organicResultObject->getTitle() === null) {

            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, [], $organicResult));
            //$this->monolog->error('Cannot identify natural result', ['class' => self::class]);

            return;
        }

        if (strpos($organicResultObject->getLink(), 'google.') !== false && strpos($organicResultObject->getLink(), '/search') !== false ) {
            return;
        }
        $imbricatorParent = $dom->xpathQuery("ancestor::*[@class='FxLDp']", $organicResult);

        $reviewsAndPricingNodes = $dom->xpathQuery("descendant::*[@class='fG8Fp uo4vr']", $organicResult);
        $hasPricing = false;
        $reviewsAndPricing = false;
        if ($reviewsAndPricingNodes->length > 0) {
            $reviewsAndPricing = $reviewsAndPricingNodes->getNodeAt(0)->textContent;
            preg_match('([0-9,]+(\xC2\xA0)[A-Z]{0,3})',$reviewsAndPricingNodes->getNodeAt(0)->textContent, $priceMatches);
            if (!empty($priceMatches)) {
                $hasPricing = true;
            }
        }
        $hasArticleNodes = $dom->xpathQuery("descendant::*[@class='MUxGbd wuQ4Ob WZ8Tjf']", $organicResult);
        $hasArticleDate = false;
        if ($hasArticleNodes->length > 0) {
            $hasArticleDate = $hasArticleNodes->getNodeAt(0)->textContent;
        }
        $resultSet->addItem(new BaseResult(
            [$this->resultType],
            [
                'title'       => $organicResultObject->getTitle(),
                'url'         => $organicResultObject->getLink(),
                'description' => $organicResultObject->getDescription(),
                'imbricated'  => ($imbricatorParent->length > 0),
                'reviewsAndPricing' => $reviewsAndPricing,
                'hasPricing' => $hasPricing,
                'articleDate' => $hasArticleDate
            ],
            $organicResult
        ));
    }
}
