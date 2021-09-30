<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

abstract class NaturalResultType
{

    const CLASSICAL             = 'classical';
    const CLASSICAL_LARGE       = 'classical_large';
    const CLASSICAL_VIDEO       = 'classical_video';
    const CLASSICAL_SITELINK    = 'classical_sitelink';
    const CLASSICAL_ILLUSTRATED = 'classical_illustrated';

    const KNOWLEDGE      = 'knowledge';
    const AdsTOP         = 'ads_top';
    const AdsTOP_MOBILE  = 'ads_top_mobile';
    const AdsDOWN        = 'ads_down';
    const AdsDOWN_MOBILE = 'ads_down_mobile';

    const PEOPLE_ALSO_ASK = 'people_also_ask';
    const PAA_QUESTION    = 'paa_question';

    const IMAGE_GROUP            = 'image_group';
    const IMAGE_GROUP_MOBILE     = 'image_group_mobile';
    const FEATURED_SNIPPED       = 'pos_zero';
    const FEATURED_SNIPPED_MOBILE       = 'pos_zero_mobile';
    const QUESTIONS              = 'questions';
    const QUESTIONS_MOBILE       = 'questions_mobile';
    const JOBS                   = 'jobs';
    const JOBS_MOBILE                   = 'jobs_mobile';
    const APP_PACK               = 'app_pack';
    const APP_PACK_MOBILE        = 'app_pack_mobile';
    const PRODUCT_LISTING        = 'pla';
    const PRODUCT_LISTING_MOBILE = 'pla_mobile';
    const RECIPES_GROUP          = 'recipes';

    const IMAGE_GROUP_IMAGE = 'image_group_image';

    const VIDEOS       = 'videos';
    const VIDEOS_MOBILE       = 'videos_mobile';

    const IN_THE_NEWS        = 'in_the_news';
    const TOP_STORIES        = 'top_stories';
    const TOP_STORIES_MOBILE = 'top_stories_mobile';
    const TWEETS_CAROUSEL    = 'tweets_carousel';

    const MAP        = 'maps';
    const MAP_MOBILE = 'maps_mobile';

    const FLIGHTS                = 'flights';
    const KNOWLEDGE_GRAPH        = 'knowledge_graph';
    const KNOWLEDGE_GRAPH_MOBILE = 'knowledge_graph_mobile';

    const ANSWER_BOX = 'answer_box';

    const HOTELS = 'hotels';
    const HOTELS_MOBILE = 'hotels_mobile';

    const DEFINITIONS = 'definitions';
    const DEFINITIONS_MOBILE = 'definitions_mobile';
    
    const MISSPELLING = 'misspelling';
    const MISSPELING_MOBILE = 'misspelling_mobile';

}
