# Customization

Each component comes with a lot of customization possibilities. Example with the Sort Component:

```php
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'sort_uri' => [
        'query_param' => 'order_by',
    ],
]);
$sorting = $openCubes->getComponent(SortComponent::getName(), [
    'available_sorts' => [
        'published_at' => ['asc', 'desc'],
    ],
]);


echo json_encode([
    'sorting' => $sorting,
], JSON_PRETTY_PRINT);
```

Now, browse `https://your.application/books?order_by[author.name]=asc`:

```json
{
  "sorting": {
    "sorts": [
      {
        "field": "published_at",
        "is_applied": false,
        "directions": [
          {
            "field": "published_at",
            "direction": "asc",
            "is_applied": false,
            "link": "https://your.application/books?order_by[published_at]=asc"
          },
          {
            "field": "published_at",
            "direction": "desc",
            "is_applied": false,
            "link": "https://your.application/books?order_by[published_at]=desc"
          }
        ]
      },
      {
        "field": "author.name",
        "is_applied": true,
        "directions": [
          {
            "field": "author.name",
            "direction": "asc",
            "is_applied": true,
            "unset_link": "https://your.application/books"
          }
        ]
      }
    ]
  }
}
```

## Options hierarchy

Options can be set at the application, the instanciation, or the URI level.

Example:

```php
use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\OpenCubes;
use function BenTools\UriFactory\Helper\uri;

$openCubes = OpenCubes::create([
    'pager' => [
        'default_size' => 50,
    ]
]);
$pager = $openCubes->getComponent(PagerComponent::getName()); // Page size = 50
$pager = $openCubes->getComponent(PagerComponent::getName(), ['default_size' => 30]);  // Page size = 30
$pager = $openCubes->getComponent(PagerComponent::getName(), ['default_size' => 30], uri('https://example.org/?per_page=100'));  // Page size = 100
```

When no PSR-7 `UriInterface` object is provided, **OpenCubes** detects the current location.