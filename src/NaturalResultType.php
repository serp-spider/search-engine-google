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
    const JOBS_MINE                    = 'jobs_mine';
    const JOBS_MINE_MOBILE             = 'jobs_mine_mobile';
    
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
    const VIDEOS_LIST        = 'videos_list';
    const VIDEOS_MOBILE = 'videos_mobile';
    const VIDEO_CAROUSEL = 'video_carousel';
    const VIDEO_CAROUSEL_MOBILE = 'video_carousel_mobile';

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
    const FLIGHTS_MOBILE                = 'flights_mobile';
    const FLIGHTS_MINE                = 'flights_mine';
    const FLIGHTS_MINE_MOBILE                = 'flights_mine_mobile';
    
    const KNOWLEDGE_GRAPH        = 'knowledge_graph';
    const KNOWLEDGE_GRAPH_MOBILE = 'knowledge_graph_mobile';

    const ANSWER_BOX = 'answer_box';

    const HOTELS        = 'hotels';
    const EXCEPTIONS        = 'exceptions';
    const HOTELS_NAMES  = 'hotels_names';
    const HOTELS_MOBILE = 'hotels_mobile';

    const DEFINITIONS        = 'definition';
    const DEFINITIONS_MOBILE = 'definition_mobile';
    const DEFINITIONS_MINE        = 'definition_mine';
    const DEFINITIONS_MINE_MOBILE = 'definition_mine_mobile';

    const MISSPELLING             = 'misspelling';
    const MISSPELLING_MINE             = 'misspelling_mine';
    const MISSPELLING_OLD_VERSION = 'spell';
    const MISSPELLING_OLD_VERSION_MINE = 'spell_mine';
    const MISSPELLING_OLD_VERSION_MOBILE = 'spell_mobile';
    const MISSPELLING_OLD_VERSION_MINE_MOBILE = 'spell_mine_mobile';
    const MISSPELING_MOBILE       = 'misspelling_mobile';
    const MISSPELING_MINE_MOBILE       = 'misspelling_mine_mobile';

    const RESULTS_NO = 'no_results';
    const DIRECTIONS = 'directions';
    const DIRECTIONS_MOBILE = 'directions_mobile';
    const DIRECTIONS_MINE = 'directions_mine';
    const DIRECTIONS__MINE_MOBILE = 'directions_mine_mobile';

    const KNOWLEDGE_GRAPH_MINE        = 'knowledge_graph_mine';
    const KNOWLEDGE_GRAPH_MINE_MOBILE = 'knowledge_graph_mine_mobile';

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

    const SERP_FEATURES_TYPE_TO_OLD_RESPONSE_FOR_POSITIONS = [
        self::APP_PACK => self::APP_PACK,
        self::APP_PACK_MOBILE => self::APP_PACK,
        self::AdsTOP => self::AdsTOP,
        self::AdsTOP_MOBILE => self::AdsTOP,
        self::AdsDOWN => self::AdsDOWN,
        self::AdsDOWN_MOBILE => self::AdsDOWN,
        self::MISSPELLING => self::MISSPELLING_OLD_VERSION,
        self::MISSPELING_MOBILE => self::MISSPELLING_OLD_VERSION,
        self::HOTELS => self::HOTELS,
        self::HOTELS_MOBILE => self::HOTELS,
        self::KNOWLEDGE_GRAPH => self::KNOWLEDGE_GRAPH_MINE,
        self::KNOWLEDGE_GRAPH_MOBILE => self::KNOWLEDGE_GRAPH_MINE_MOBILE,
        self::FEATURED_SNIPPED => self::FEATURED_SNIPPED,
        self::FEATURED_SNIPPED_MOBILE => self::FEATURED_SNIPPED,
        self::RECIPES_GROUP => self::RECIPES_GROUP,
        self::RECIPES_LINKS => self::RECIPES_LINKS,
        self::PRODUCT_LISTING => self::PRODUCT_LISTING,
        self::PRODUCT_LISTING_MOBILE => self::PRODUCT_LISTING,
        self::QUESTIONS => self::QUESTIONS,
        self::QUESTIONS_MOBILE => self::QUESTIONS,
        self::FLIGHTS => self::FLIGHTS,
        self::FLIGHTS_MOBILE => self::FLIGHTS,
        self::DEFINITIONS => self::DEFINITIONS,
        self::DEFINITIONS_MOBILE => self::DEFINITIONS,
        self::JOBS => self::JOBS,
        self::JOBS_MOBILE => self::JOBS,
        self::DIRECTIONS => self::DIRECTIONS,
        self::DIRECTIONS_MOBILE => self::DIRECTIONS,
        self::IMAGE_GROUP => self::IMAGE_GROUP,
        self::IMAGE_GROUP_MOBILE => self::IMAGE_GROUP,
        self::TOP_STORIES => self::TOP_STORIES_OLD_VERSION,
        self::TOP_STORIES_MOBILE => self::TOP_STORIES_OLD_VERSION,
        self::MAPS_LINKS => self::MAPS_LINKS,
        self::VIDEO_CAROUSEL => self::VIDEOS,
        self::VIDEO_CAROUSEL_MOBILE => self::VIDEOS,
        self::MAP => self::MAP,
        self::MAP_MOBILE => self::MAP,
    ];

}
