<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

abstract class NaturalResultType
{

    const CLASSICAL = 'classical';
    const CLASSICAL_LARGE = 'classical_large';
    const CLASSICAL_VIDEO = 'classical_video';
    const CLASSICAL_SITELINK = 'classical_sitelink';
    const CLASSICAL_ILLUSTRATED = 'classical_illustrated';

    const IMAGE_GROUP = 'image_group';
    const IMAGE_GROUP_IMAGE = 'image_group_image';

    const IN_THE_NEWS = 'in_the_news';
    const TOP_STORIES = 'top_stories';
    const TWEETS_CAROUSEL= 'tweets_carousel';

    const MAP = 'map';
    const MAP_PLACE = 'map_place';

    const FLIGHTS = 'flights';

    const ANSWER_BOX = 'answer_box';
}
