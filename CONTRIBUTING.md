CONTRIBUTING
============

Any contribution is welcome.


Issue
-----

If you encounter an issue with the library you are encouraged to report it and we can work together on solving it.

Before submitting the issue please make sure you are using the latest version of serps/search-engine-google. 
If you are and if you still have the issue you can report it 
on the [issue tracker](https://github.com/serp-spider/search-engine-google/issues/new).

When reporting an issue try to provide as much details as possible. If the issue is related to a page that cannont
parse correctly, the following details will be very helpful to fix the issue:

- The url that fails to parse
- What is expected
- What you are getting
- You might attach the html of the page that does not parse 
- You might send screenshot of what is notparsing correctly


Code contribution
-----------------

### Tests

All contributions must be tested following as much as possible the current test structure.
Look at current tests in ``test/suites`` for more details.

### Coding Standards

The code follows the PSR-2 coding standards. 
We provided two useful commands to check and fix automatically code standards:

- Checking standards: ``composer cscheck``
- Fixing standards: ``composer csfix``


### Tools

- run full test suit: ``composer test``
- run some tests only: ``composer test testName`` 
(``testName`` will be used in [phpunit --filter](https://phpunit.de/manual/current/en/textui.html#textui.examples.filter-patterns))
