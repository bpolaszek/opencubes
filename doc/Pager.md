# The Pager Component

The Pager Component will help your app handle pagination. 

You'll be able to set the page size (the number of items per page), get the current page number, enabling / disabling pagination, and so on.

## The PagerComponentFactory

The default pager factory comes with the following options:

- The default page size
- The available sizes (only useful if you expose OpenCubes in your front-end)
- Is the pagination enabled / disabled (default enabled)
- The total number of items (you can skip this step to set it later on the component, because you'll probably need the component itself to query your DB first)
- The pager delta (to avoid returning 1000 pages)

To use it, instanciate the factory:

```php
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use BenTools\OpenCubes\OpenCubes;

$openCubes = new OpenCubes();
$pagerFactory = new PagerComponentFactory([
    PagerComponentFactory::OPT_TOTAL_ITEMS         => 0,
    PagerComponentFactory::OPT_DEFAULT_PAGESIZE    => 50,
    PagerComponentFactory::OPT_AVAILABLE_PAGESIZES => [10, 50, 100, 500],
    PagerComponentFactory::OPT_ENABLED  => true,
    PagerComponentFactory::OPT_DELTA               => 2,
]);

$openCubes->registerFactory($pagerFactory);
$component = $openCubes->getComponent('pager');
```

## The PagerUriManager

This class is responsible to read and build URIs around your configuration / the user input.

```php
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use function BenTools\UriFactory\Helper\uri;

$uriManager = new PagerUriManager([
    PagerUriManager::OPT_PAGE_QUERY_PARAM => 'page_number', // default is 'page'
    PagerUriManager::OPT_PAGESIZE_QUERY_PARAM => 'limit', // default is 'per_page'
]);

$uri = uri('https://example.org/?page_number=3&limit=500');
$uriManager->getCurrentPageNumber($uri); // 3
$uriManager->getCurrentPageSize($uri); // 500
```

In case parameters aren't provided through the query string, methods return null, 
which will mean the calling class should use default values (like page 1 and a default page size).

Register it in the pager factory:

```php
$pagerFactory = new PagerComponentFactory($options, $uriManager);
```