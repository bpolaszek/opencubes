# The Sort Component

The Sort component will help you:

- Get which sort has been applied by the user
- Get which sort should be applied by default
- Provide your user which sorting options are available

## The SortUriManager

This the class that will parse and build Uris. The default implementation will base on the `sort` query param.

```php
namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\Sort\Model\Sort;
use Psr\Http\Message\UriInterface;

interface SortUriManagerInterface
{

    /**
     * Return the applied sorts from the given Uri, as an associative array (i.e. ['some_field' => 'desc']).
     *
     * @param UriInterface $uri
     * @return array
     */
    public function getAppliedSorts(UriInterface $uri): array;

    /**
     * Build an Uri with the given sorts.
     *
     * @param UriInterface          $uri
     * @param array|string[]|Sort[] $sorts
     * @return UriInterface
     */
    public function buildSortUri(UriInterface $uri, array $sorts): UriInterface;
}
```

## The SortComponentFactory

The factory will handle your default options and is responsible to instanciate Sort components.

Configurable options are:
- Available sorts (which sorting options you will offer to your users)
- Default sorts (when the user did not select any sort)
- Applied sorts (hydrated from Uri)
- Is multisort enabled (do you allow sorting on multiple fields)

### Overview

```php
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use BenTools\OpenCubes\Component\Sort\SortUriManager;
use function BenTools\OpenCubes\current_location;

$sortingFactory = new SortComponentFactory([
    SortComponentFactory::OPT_AVAILABLE_SORTS => [
        'created_at' => ['asc', 'desc'],
        'price'      => ['desc', 'asc'],
        'random'     => ['rand'],
    ],
    SortComponentFactory::OPT_DEFAULT_SORTS => [
        'name' => 'asc',
    ],
], new SortUriManager([
    SortUriManager::OPT_SORT_QUERY_PARAM => 'order_by',
]));

$sorting = $sortingFactory->createComponent(current_location());
```