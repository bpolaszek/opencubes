# The Pager Component

The Pager Component will help your app handle pagination. 

You'll be able to set the page size (the number of items per page), get the current page number, enabling / disabling pagination, and so on.

## The PagerUriManager

This class is responsible to read and build URIs around your configuration / the user input.

```php
use Psr\Http\Message\UriInterface;

interface PagerUriManagerInterface
{

    /**
     * Extract the current page size from the given Uri.
     *
     * @param UriInterface $uri
     * @return int|null
     */
    public function getCurrentPageSize(UriInterface $uri): ?int;

    /**
     * Extract the current page number from the given Uri.
     *
     * @param UriInterface $uri
     * @return int|null
     */
    public function getCurrentPageNumber(UriInterface $uri): ?int;

    /**
     * Build an Uri with the given page number.
     *
     * @param UriInterface $uri
     * @param int          $pageNumber
     * @param bool|null    $paginationEnabled
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    public function buildPageUri(UriInterface $uri, int $pageNumber): UriInterface;

    /**
     * Build an Uri with the given page size.
     * When $size is null, the Uri should no longer carry this value.
     *
     * @param UriInterface $uri
     * @param int          $size
     * @return UriInterface
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     */
    public function buildSizeUri(UriInterface $uri, ?int $size): UriInterface;
}
```

The default implementation will read `page` and  `per_page` query params. 

You can change those params without reimplementing the interface by instanciating the UriManager like this:

```php
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'pager_uri' => [
        PagerUriManager::OPT_PAGE_QUERY_PARAM     => 'page_number',
        PagerUriManager::OPT_PAGESIZE_QUERY_PARAM => 'limit'
    ],
]);

$component = $openCubes->getComponent('pager');
```


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
    PagerComponentFactory::OPT_PAGESIZING_ENABLED  => true,
    PagerComponentFactory::OPT_DELTA               => 2,
]);

$openCubes->registerFactory($pagerFactory);
$component = $openCubes->getComponent('pager');
```