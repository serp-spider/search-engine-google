<?php

namespace Serps\SearchEngine\Google\Parser;

use Serps\SearchEngine\Google\NaturalResultType;

/**
 * Class TranslateService
 *
 * @package Serps\SearchEngine\Google\Parser
 */
class TranslateService
{
    protected $siteHost = null,
        $mobile = false,
        $crawlSubdomains = false,
        $urlAlias = null,
        $response = [];

    const DEFAULT_POSITION = 666;

    /**
     * TranslateService constructor.
     *
     * @param $siteHost
     * @param bool $crawlSubdomains
     * @param null $urlAlias
     */
    public function __construct($siteHost, $crawlSubdomains = false, $urlAlias = null)
    {
        $this->siteHost        = $siteHost;
        $this->crawlSubdomains = $crawlSubdomains;
        $this->urlAlias        = $urlAlias;
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function extractDomain($url)
    {
        $url = strtolower(trim($url));
        $url = str_replace(['http://', 'https://'], ['', ''], $url);

        $url = preg_replace('/^www[0-9]*\./', '', $url);

        if (strpos($url, '/') != false) {
            $url = substr($url, 0, strpos($url, '/'));
        }

        $url = ltrim($url, '.');
        $url = ltrim($url, '/');

        return $url;
    }

    /**
     * @param $item
     *
     * @return array
     */
    protected function matchSubdomainsOrUrlAlias($item)
    {
        $matchedSubdomains = [];

        if ($this->crawlSubdomains || $this->mobile || $this->urlAlias) {
            if ($this->crawlSubdomains === false) {
                preg_match('/m\.' . str_replace('.', '\.', $this->siteHost) . '/', $item->url, $matchedSubdomains);

                if (empty($matchedSubdomains[0]) && $this->urlAlias) {
                    preg_match('/m\.' . str_replace('.', '\.', $this->urlAlias) . '/', $item->url, $matchedSubdomains);
                }

            } else {
                preg_match('/.*\.' . str_replace('.', '\.', $this->siteHost) . '/', $item->url, $matchedSubdomains);

                if (empty($matchedSubdomains[0]) && $this->urlAlias) {
                    preg_match('/.*\.' . str_replace('.', '\.', $this->urlAlias) . '/', $item->url,
                        $matchedSubdomains);
                }
            }
        }

        return $matchedSubdomains;
    }

    /**
     * @param $item
     * @param $rank
     */
    protected function processClassicalResult($item, &$rank)
    {
        $rank++;

        $matchedSubdomains = $this->matchSubdomainsOrUrlAlias($item);

        $title       = $item->title;
        $description = $item->description;
        $domainName  = $this->extractDomain($item->url);


        if (
            $this->response['position'] == self::DEFAULT_POSITION &&
            ( $domainName === $this->siteHost || $domainName === $this->urlAlias || !empty($matchedSubdomains[0]))
        ) {
            $this->response['position']     = $rank;
            $this->response['landing_page'] = $item->url;
        }

        if (empty($this->response[0][$domainName])) {
            $this->response['list_of_urls'][0][$domainName] = $rank;
        }

        $this->response['competition'][$rank] = [
            "url"               => $domainName,
            "full_landing_page" => $item->url,
            "height"            => "0",
            "title"             => $title,
            "description"       => $description,
            "video"             => "",
            "amp"               => "",
        ];
    }

    /**
     * @param $item
     */
    protected function processSerpFeatures($item)
    {
        if ($item->is(NaturalResultType::APP_PACK)) {
            $this->response[NaturalResultType::APP_PACK] = true;
        }

        if ($item->is(NaturalResultType::AdsTOP) || $item->is(NaturalResultType::AdsTOP_MOBILE)) {
            $this->response[NaturalResultType::AdsTOP] = $item->getData();
        }

        if ($item->is(NaturalResultType::AdsDOWN) || $item->is(NaturalResultType::AdsDOWN_MOBILE)) {
            $this->response[NaturalResultType::AdsDOWN] = $item->getData();
        }

        if ($item->is(NaturalResultType::APP_PACK_MOBILE)) {
            $this->response[NaturalResultType::APP_PACK_MOBILE] = true;
        }

        if ($item->is(NaturalResultType::MISSPELLING) || $item->is(NaturalResultType::MISSPELING_MOBILE)) {
            $this->response[NaturalResultType::MISSPELLING_OLD_VERSION] = true;
        }

        if ($item->is(NaturalResultType::MAP) || $item->is(NaturalResultType::MAP_MOBILE)) {
            $this->response[NaturalResultType::MAP]              = true;
            $this->response[NaturalResultType::MAPS_OLD_VERSION] = true;

            foreach ($item->getData()['title'] as $title) {
                $this->response[NaturalResultType::MAPS_LINKS][] = ['title' => $title, 'url' => ''];
            }
        }

        if ($item->is(NaturalResultType::VIDEOS) || $item->is(NaturalResultType::VIDEOS_MOBILE)) {
            $this->response[NaturalResultType::VIDEOS] = $item->getData();
        }

        if ($item->is(NaturalResultType::KNOWLEDGE_GRAPH) || $item->is(NaturalResultType::KNOWLEDGE_GRAPH_MOBILE)) {
            $this->response[NaturalResultType::KNOWLEDGE_GRAPH] = $item->getData();
        }

        if ($item->is(NaturalResultType::RECIPES_GROUP)) {
            $this->response[NaturalResultType::RECIPES_GROUP] = true;
            $this->response[NaturalResultType::RECIPES_LINKS] = $item->getData()['recipes_links'];
        }

        if ($item->is(NaturalResultType::FEATURED_SNIPPED) || $item->is(NaturalResultType::FEATURED_SNIPPED_MOBILE)) {
            $this->response[NaturalResultType::FEATURED_SNIPPED] = $item->getData();
        }

        if ($item->is(NaturalResultType::PRODUCT_LISTING) || $item->is(NaturalResultType::PRODUCT_LISTING)) {
            $this->response[NaturalResultType::PRODUCT_LISTING] = $item->getData();
        }

        if ($item->is(NaturalResultType::QUESTIONS) || $item->is(NaturalResultType::QUESTIONS_MOBILE)) {
            $this->response[NaturalResultType::QUESTIONS][] = $item->getData();
        }

        if ($item->is(NaturalResultType::FLIGHTS)) {
            $this->response[NaturalResultType::FLIGHTS] = true;
        }

        if ($item->is(NaturalResultType::DEFINITIONS) || $item->is(NaturalResultType::DEFINITIONS_MOBILE)) {
            $this->response[NaturalResultType::DEFINITIONS] = true;
        }

        if ($item->is(NaturalResultType::JOBS) || $item->is(NaturalResultType::JOBS_MOBILE)) {
            $this->response[NaturalResultType::JOBS] = true;
        }

        $this->response[NaturalResultType::SITE_LINKS] = 0;

        if ($item->is(NaturalResultType::SITE_LINKS_BIG) || $item->is(NaturalResultType::SITE_LINKS_BIG_MOBILE)) {
            $this->response[NaturalResultType::SITE_LINKS] = 2;
        }

        if ($item->is(NaturalResultType::SITE_LINKS_SMALL) || $item->is(NaturalResultType::SITE_LINKS)) {
            $this->response[NaturalResultType::SITE_LINKS] = 1;
        }

        if ($item->is(NaturalResultType::DIRECTIONS) || $item->is(NaturalResultType::DIRECTIONS_MOBILE)) {
            $this->response[NaturalResultType::DIRECTIONS] = true;
        }

        if ($item->is(NaturalResultType::RESULTS_NO)) {
            $this->response[NaturalResultType::RESULTS_NO] = $item->getData()[0];
        }
    }

    /**
     * @param \Serps\Core\Serp\IndexedResultSet $results
     *
     * @return $this
     */
    public function intoOldResponse(\Serps\Core\Serp\IndexedResultSet $results)
    {
        if (empty($results->getItems())) {
            $this->response = NaturalResultType::SERP_FEATURES_OLD_RESPONSE_TEMPLATE;

            return $this;
        }

        if ($results->hasType([NaturalResultType::CLASSICAL_MOBILE])) {
            $this->mobile = true;
        }

        $rank = 0;
        $this->initSerpFeaturesDefaultResponse();
        $this->response['position'] = self::DEFAULT_POSITION;

        foreach ($results->getItems() as $item) {
            if ($item->is(NaturalResultType::CLASSICAL) || $item->is(NaturalResultType::CLASSICAL_MOBILE)) {
                $this->processClassicalResult($item, $rank);

                continue;
            }

            $this->processSerpFeatures($item);
        }

        $this->response['list_of_urls'][0] = !empty($this->response['list_of_urls'][0]) ? array_reverse($this->response['list_of_urls'][0]):[];

        return $this;
    }

    /**
     *
     */
    protected function initSerpFeaturesDefaultResponse()
    {
        foreach (NaturalResultType::SERP_FEATURES_OLD_RESPONSE_TEMPLATE as $typeSerp => $value) {
            $this->response[$typeSerp] = $value;
        }
    }

    /**
     * @return array|bool|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
