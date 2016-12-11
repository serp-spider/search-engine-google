# CHANGELOG

## 0.2.0 

*20xx-xx-xx*

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
    
* Features:
    * Google cards results are now supported [#38](https://github.com/serp-spider/search-engine-google/pull/38)
    * Mobile page detection: GoogleSerp::isMobile() [564057ce0ee255cfa138440e033776b85f239acb](https://github.com/serp-spider/search-engine-google/commit/564057ce0ee255cfa138440e033776b85f239acb)
    * Mobile results have now their own parser
    * Parsing rule for mobile video groups [#41](https://github.com/serp-spider/search-engine-google/issues/41)
* Bug fixes:
    * Large video have the CLASSICAL type as mentioned in the doc [#36](https://github.com/serp-spider/search-engine-google/issues/36)
