<?php

namespace Serps\SearchEngine\Google;

abstract class NaturalResultType
{

    const CLASSICAL        = 'classical';
    const CLASSICAL_MOBILE = 'classical_mobile';

    const KNOWLEDGE       = 'knowledge';
    const AdsTOP          = 'ads_up';
    const ADS_OLD_VERSION = 'ads';
    const AdsTOP_MOBILE   = 'ads_top_mobile';
    const AdsDOWN         = 'ads_down';
    const AdsDOWN_MOBILE  = 'ads_down_mobile';

    const PEOPLE_ALSO_ASK = 'people_also_ask';
    const PAA_QUESTION    = 'paa_question';

    const IMAGE_GROUP             = 'images';
    const IMAGE_GROUP_MOBILE      = 'images_mobile';
    const FEATURED_SNIPPED        = 'pos_zero';
    const FEATURED_SNIPPED_MOBILE = 'pos_zero_mobile';
    const QUESTIONS               = 'questions';
    const QUESTIONS_MOBILE        = 'questions_mobile';
    const JOBS                    = 'jobs';
    const JOBS_MOBILE             = 'jobs_mobile';
    const APP_PACK                = 'app_pack';
    const WIKI                = 'has_wiki';
        const SITE_LINKS_BIG          = 'site_links_big';
    const SITE_LINKS              = 'site_links';
    const AMP                     = 'amp';
    const SITE_LINKS_BIG_MOBILE   = 'site_links_big_mobile';
    const SITE_LINKS_SMALL        = 'site_links_small';
    const APP_PACK_MOBILE         = 'app_pack_mobile';

    const PRODUCT_LISTING         = 'pla';
    const PRODUCT_LISTING_MOBILE  = 'pla_mobile';
    const RECIPES_GROUP           = 'recipes';
    const RECIPES_LINKS           = 'recipes_links';


    const VIDEOS        = 'videos';
    const VIDEOS_MOBILE = 'videos_mobile';

    const TOP_STORIES             = 'top_stories';
    const TOP_STORIES_OLD_VERSION = 'news';
    const TOP_STORIES_MOBILE      = 'top_stories_mobile';
    const TWEETS_CAROUSEL         = 'tweets_carousel';

    const MAP              = 'maps';
    const MAPS_OLD_VERSION = 'has_map';
    const MAPS_LINKS            = 'maps_links';
    const MAPS_COORDONATES      = 'maps_coords';
    const MAP_MOBILE       = 'maps_mobile';
    const MAPS_LATITUDE       = 'lat';
    const MAPS_LONGITUTDE       = 'long';

    const FLIGHTS                = 'flights';
    const KNOWLEDGE_GRAPH        = 'knowledge_graph';
    const KNOWLEDGE_GRAPH_MOBILE = 'knowledge_graph_mobile';

    const ANSWER_BOX = 'answer_box';

    const HOTELS        = 'hotels';
    const HOTELS_NAMES  = 'hotels_names';
    const HOTELS_MOBILE = 'hotels_mobile';

    const DEFINITIONS        = 'definition';
    const DEFINITIONS_MOBILE = 'definition_mobile';

    const MISSPELLING             = 'misspelling';
    const MISSPELLING_OLD_VERSION = 'spell';
    const MISSPELLING_OLD_VERSION_MOBILE = 'spell_mobile';
    const MISSPELING_MOBILE       = 'misspelling_mobile';

    const RESULTS_NO = 'no_results';
    const DIRECTIONS = 'directions';
    const DIRECTIONS_MOBILE = 'directions_mobile';

    const SERP_FEATURES_OLD_RESPONSE_TEMPLATE = [
        self::SITE_LINKS              => 0,
        self::MISSPELLING_OLD_VERSION => '',
        self::ADS_OLD_VERSION         => [],
        self::AdsDOWN                 => [],
        self::AdsTOP                  => [],
        self::IMAGE_GROUP             => [],
        self::TOP_STORIES_OLD_VERSION => [],
        self::VIDEOS                  => [],
        self::KNOWLEDGE_GRAPH         => '',
        self::MAPS_OLD_VERSION        => null,
        self::MAPS_LINKS              => null,
        self::MAPS_COORDONATES        => [],
        self::MAPS_LATITUDE           => false,
        self::MAPS_LONGITUTDE         => false,
        self::FEATURED_SNIPPED        => null,
        self::PRODUCT_LISTING         => [],
        self::QUESTIONS               => [],
        self::FLIGHTS                 => [],
        self::DEFINITIONS             => [],
        self::JOBS                    => [],
        self::APP_PACK                => null,
        self::HOTELS                  => null,
        self::HOTELS_NAMES            => [],
        self::RECIPES_GROUP           => null,
        self::RECIPES_LINKS           => null,
        self::DIRECTIONS              =>  [],
        self::RESULTS_NO              => null,
        self::WIKI              => 0,
    ];


}
