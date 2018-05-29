# CHANGELOG


## 0.4.1

*2018-xx-xx* NOT RELEASED YET

## 0.4.0

*2018-05-29*

* Bug fix:
    * fixed the captcha exception. The right exception is now returned when a captcha is found
    * fixed invalid type hinting causing errors with hhvm
* Google updates:
    * **bc break** removed support for image captcha as google now uses recaptcha
* Other:
    * When an invalid classical result is found, throw an exception instead of returning invalid results causing fatal errors.

## 0.3.0 

*2018-04-04*

* Dependencies
    * **bc break** use version ``0.3.x`` of ``serps/core``
* Updates
    * **bc break** google url default domain is now ``"www.google.com`` instead of ``google.com``. This way we avoid extra redirects too the ``"www"`` subdomain.
    * Fix a bug with search result group parser that was triggering a php error.
* Dom Updates
    * Fix parsing for classical results on mobiles.
    * Fix parsing for knowledge cards on mobiles.

## 0.2.5

*2018-03-29*

* Bug fix:
    * Fix a bug with map results introduced in version 0.2.4 see [#94](https://github.com/serp-spider/search-engine-google/issues/94)

## 0.2.4

*2018-03-22*

* Bug fix:
    * Fix google update for map results
    * Fix google update for "destination" data in classical results
    * Fix google update for People Also Ask
    * Fix google update for answer box [#90](https://github.com/serp-spider/search-engine-google/issues/90)
    
## 0.2.3

*2017-12-11*

* Features:
    * Added parsing for people also ask results [#70](https://github.com/serp-spider/search-engine-google/issues/70)

* Bug fix:
    * Fix some mobile card results not parsing [#83](https://github.com/serp-spider/search-engine-google/issues/83)

## 0.2.2

*2017-11-25*

* Bug fix:
    * Parse ``bkWMgd`` groups (thanks to [Shiftas](https://github.com/Shiftas)) [#76](https://github.com/serp-spider/search-engine-google/issues/76)
    * Fix result count [#76](https://github.com/serp-spider/search-engine-google/issues/76)
    * Fix some mobile card results not parsing [#79](https://github.com/serp-spider/search-engine-google/issues/79) and [#78](https://github.com/serp-spider/search-engine-google/issues/78)
    * Fix twitter carousel parser for mobile [#81](https://github.com/serp-spider/search-engine-google/issues/81)
    * Fix related searches for mobile [#80](https://github.com/serp-spider/search-engine-google/issues/81)

* Features:
    * Parsing for "composed top stories" and standardizing old "top stories" [#67](https://github.com/serp-spider/search-engine-google/issues/67)

* Other:
    * Dependency to serps/core was updated from ~0.2.0 to ~0.2.4

## 0.2.1

*2017-07-16*

* Features:
    * Parsing for mobile knowledge results (fd95ffc07c137223e36fade739b4617c17fe6758)

* Bug fix
    * Fixing tweet carousel recognition (4f681da0435454b5ff592c657789010ccf8361ee)
    * Fixing tweet carousel non linked to an user


## 0.2.0 

*2017-05-01*

* Breaking Changes:
    * Images data are returned MediaInterface [#35](https://github.com/serp-spider/search-engine-google/issues/35)
    * Drop support for raw parser [5f41ddeb6a9076b363a83071e0f27a0254f1e330](https://github.com/serp-spider/search-engine-google/commit/5f41ddeb6a9076b363a83071e0f27a0254f1e330)
    * ``Serps\SearchEngine\Google\GoogleDom`` now extends ``Serps\Core\Dom\WebPage`` [dafe67e](https://github.com/serp-spider/search-engine-google/commit/dafe67eeae3eb46bb570fdc3eadd22d4abe47b7d)
    * ``Serps\SearchEngine\Google\GoogleError`` now extends ``Serps\Core\Dom\WebPage`` 
    and does not extend ``Serps\SearchEngine\Google\GoogleDom`` anymore [dafe67e](https://github.com/serp-spider/search-engine-google/commit/dafe67eeae3eb46bb570fdc3eadd22d4abe47b7d)
    * Class ``Serps\SearchEngine\Google\Css`` was removed and an equivalent is now provided from the core package in
    ``Serps\Core\Dom\Css`` [4e5b1a1](https://github.com/serp-spider/search-engine-google/commit/4e5b1a193abfe5093a48152b12878e7cef022b7b)
    * Vendor ``symfony/css-selector`` is not provided anymore, instead it moved in core package [4e5b1a1](https://github.com/serp-spider/search-engine-google/commit/4e5b1a193abfe5093a48152b12878e7cef022b7b)
    * ``GoogleClient::query($googleUrl, $proxy, $cookieJar)`` was refactored 
    to ``GoogleClient::query($googleUrl, $browser)`` in order to provide a more fluent management
    of browser specifications [a6fe671](https://github.com/serp-spider/search-engine-google/commit/a6fe6711d6fac42977cfc30212e438d8ab933584)
    * ``GoogleClient::query`` does not auto set language header anymore, that's now done from the browser instance [a6fe671](https://github.com/serp-spider/search-engine-google/commit/a6fe6711d6fac42977cfc30212e438d8ab933584)
    * ``GoogleClient::request`` and ``GoogleClient::getRequestBuilder()`` were removed and are replaced with
    browser implementation [a6fe671](https://github.com/serp-spider/search-engine-google/commit/a6fe6711d6fac42977cfc30212e438d8ab933584)
    * class ``Serps\SearchEngine\Google\GoogleClient\RequestBuilder`` was removed
    * fix the typo in the interface name ``ParsingRuleInterace`` that is now ``ParsingRuleInterface``
    * Method ``ParsingRuleInterace::match(GoogleDom $dom, \DOMElement $node)`` 
    is now ``ParsingRuleInterace::match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)``
    * the property ``is_carousel`` from top stories is now named ``isCarousel``
    
* Features:
    * Google cards results are now supported [#38](https://github.com/serp-spider/search-engine-google/pull/38)
    * Mobile page detection: GoogleSerp::isMobile() [564057ce0ee255cfa138440e033776b85f239acb](https://github.com/serp-spider/search-engine-google/commit/564057ce0ee255cfa138440e033776b85f239acb)
    * Mobile results have now their own parser
    * Parsing rule for mobile video groups [#41](https://github.com/serp-spider/search-engine-google/issues/41)
    * Parsing rule for mobile image groups
* Bug fixes:
    * Large video have the CLASSICAL type as mentioned in the doc [#36](https://github.com/serp-spider/search-engine-google/issues/36)
