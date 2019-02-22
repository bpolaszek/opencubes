# The Sort Component

The Sort component will help you:

- Get which sort has been applied by the user
- Get which sort should be applied by default
- Provide your user which sorting options are available


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