

# How to debug new Serp parser in PHP

A PHP client parsing HTML from Google.
Comments, requests or bug reports are appreciated.


## Get started

Read about Xpath before any changes in any rule

---

### Installation

Look into composer.json from backend-live-rabbit for example.
Source code: https://github.com/BuntStudio/search-engine-google

### Testing
```
Testing script in: seomonitor-backend-live-rabbit/test_scripts/test_serp_parser.php
```
Fill the serp.html file with the desired html and hit play :)

### Debugging

After the html is detected for a device (desktop or mobile) with 
```php
$this->isMobile();
```

from GoogleSerp class, a new parser is loaded, depending on detected device
```php
new NaturalParser() //for desktop
```
or 
```php
new MobileNaturalParser() // for mobile 
```
Lets take the desktop device for analyze (NaturalParser class):

Every  element  in the html page has a class/id/html tag.
Every desired element to be detected in the html page will be added in method ``getParsableItems`` from class `NaturalParser`
The elements will be detected with xpath and they will be filtered with not containin `script` tag or  `style` tag (for optimization)
Like this
```php
 return $googleDom->xpathQuery("//*[
            @id='rso' or
            @id='rhs' or
            @id='iur' or
            @id='tads' or
            @id='tadsb' or
            @id='tvcap' or
            @id='extabar' or
            contains(@id, 'isl') or
            @class='C7r6Ue' or
            @class='e4xoPb' or
            @class='xpdopen' or
            @class='lr_container yc7KLc mBNN3d' or
            @class='LQQ1Bd' or
            div[@class='CH6Bmd'] or
            contains(@class, 'commercial-unit-desktop-top') or
            contains(@class, 'related-question-pair') or
            contains(@class, 'gws-plugins-horizon-jobs__li-ed') or
            g-section-with-header[@class='yG4QQe TBC9ub'] or
            @class = 'p64x9c card-section KDCVqf' or
            @id='result-stats' or
            @class = 'ULktNd rQUFld rrecc'
        ][not(self::script) and not(self::style)]")
```

After the elements are detected (a `DomNodeList` is returned),
 they will be parsed to method `parseGroups` from `AbstractParser` and iterate on every element detected will Xpath rules above
 
 
 Every device parser will have their own rules for parsing every result detected. See `generateRules` method from `NaturalParser`
 It's a list containing some rule(s) for parsing a result (serp features or organic result)
The rule for parsing classical results from html will
 ```php
extends AbstractRuleDesktop implements ParsingRuleInterface // for desktop
```
or 
```php
extends AbstractRuleMobile implements ParsingRuleInterface // for mobile
```

The rule for parsing Serp Features  results from html will
```php
implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
```

If method `match` will return true, then method `parse` will be called

Every rule  will have it's own custom rule(s)/code for parsing.


### Parsing classical results
See class `ClassicalResult` (for desktop) or `ClassicalResultMobile` (for mobile)
The html node that is detected in `NaturalParser` or ` MobileNaturalParser` will be parsed in method `parse`, after the `match` method will return true
Every one of this class (`ClassicalResult` or `ClassicalResultMobile`) will have some rules for skipping results. 

See method `skipResult`. Every rule for skipping has comments for reasoning of doing that.
For current node, `parseNode` is called and in this method `parseNodeWithRules` is called from `ClassicalResultEngine`

`ClassicalResult` extends  `AbstractRuleDesktop` witch extends `ClassicalResultEngine`

Let's take them one by one:



``AbstractRuleDesktop``
- you will find a method called `generateRules` containing an array with rules for parsing current node. 
- The reason of doing that is because the html node from google is different, is not always the same.
- whenever you will identify a new rule for parsing  a classical result in google (detecting text, url and description),  you will create a class (rule) that ` implements ParsingRuleByVersionInterface` 
- and create your own code for detecting result. After that, you will add this class (new rule) in the array from `generateRules` from `AbstractRuleDesktop`

`ClassicalResultEngine`
- calls method `parseNodeWithRules` and for every rule written in the `generateRules` method from `AbstractRuleDesktop` will try to parse the current html node
- inside every rule written, it is provided a parameter `OrganicResultObject $organicResultObject`
    - let's say you have written already two rules for detecting text, url and description
    - meaning that you already detected two cases of different html for a classical result
    - if in a rule, the url for a classical result cannot be detected, an Exception will be thrown and `ClassicalResultEngine` will try to parse the current node with the next rule from `generateRules`
    - if in a rule you detect the url, but cannot detect the title, `OrganicResultObject` will have only `protected link` and  an Exception will be thrown and  will try to parse the current node with the next rule from `generateRules`
    - if the title is detected  on the next rule, but cannot detect description (`OrganicResultObject` will have  `protected link` and `protected descption`) will try to parse the current node with the next rule 
    - and so one, until title and url, that are required, are detected
- after parsing the node with all rules and the url will not be detected, an Exception will be thrown and the current node will not be parsed further
   
 ### Parsing serp features
 - the serp features rules will be called same as `ClassicalResult` 
 - but will not have multiple rules of parsing in an array
    - will have different subrules of parsing in the same rule.
    - see `TopStories` how it is parsed 


