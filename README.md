# OpenCubes

OpenCubes is a set of components that you can use in any PHP project to translate (PSR-7) Uris into different kinds of value objects:

- Pagination
- Applied filters
- Applied sorts
- ...

For example, when a User hits `https://your.application/books?page=3&per_page=50&sort[author.name]=asc&filters[editor_id][]=5`, your application will probably know that it should retrieve some books but you generally translate sort, filters and pagination from the query string by yourself.

With OpenCubes, for each component, you will be able to define:

- The default settings at the application level (for example, the number of results per page)
- The default settings at the request level (for exemple, the default sorting of books if none is provided)
- An URL parser (to get the user's settings from the query string - but keep calm, we provide a default implementation!) 

Another great feature of OpenCubes is that it provides a default JSON serialization for each component: 
when you're working on API-centric applications, you can expose these components and your front-end (React, VueJS, Angular, or anything) will precisely know:

- Which filters have been applied (and the URIs to remove them)
- Which sorts have been applied (and the URIs to remove them), and which other sorts are available (and the URIs to apply them)
- Which page size has been applied (and the URI to remove it), and which other page sizes are available  (and the URIs to apply them)

We work hard to cover most of use cases, but you can define your own component factories, your own URL parsers & builders, and even your own components!

## Overview

```php
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use BenTools\OpenCubes\OpenCubes;

/**
 * @var FilterComponent $filters
 * @var SortComponent $sorting
 */
$openCubes = OpenCubes::create();
$filters = $openCubes->getComponent(FilterComponent::getName());
$sorting = $openCubes->getComponent(SortComponent::getName(), [
    SortComponentFactory::OPT_AVAILABLE_SORTS => [
        'created_at' => ['asc', 'desc'],
        'name' => ['asc', 'desc']
    ],
    SortComponentFactory::OPT_DEFAULT_SORTS => [
        'created_at' => 'desc', // When no sorting is defined from Uri
    ],
]);

echo json_encode([
    'filters' => $filters,
    'sorting' => $sorting,
], JSON_PRETTY_PRINT);
```


Now, consider you're browsing your app at the following Url:
 
`https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo`

You will get:

- A `BenTools\OpenCubes\Component\Filter\FilterComponent` object containing:
    - A `BenTools\OpenCubes\Component\Filter\Model\RangeFilter` object (date)
    - A `BenTools\OpenCubes\Component\Filter\Model\SimpleFilter` object (category_id)
    - A `BenTools\OpenCubes\Component\Filter\Model\CollectionFilter` object (tags)
    - A `BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter` object (name)
- A `BenTools\OpenCubes\Component\Sort\SortComponent` object containing:
    - 4 `BenTools\OpenCubes\Component\Sort\Model\Sort` objects

The `json_encode` part will produce:

```json

{
    "filters": {
        "date": {
            "type": "range",
            "field": "date",
            "left": "2019-01-01",
            "right": "2019-01-31",
            "is_applied": true,
            "is_negated": false,
            "unset_link": "https://your.application/books?filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
        },
        "category_id": {
            "type": "simple",
            "field": "category_id",
            "value": {
                "key": "12",
                "value": "12",
                "is_applied": true,
                "count": null,
                "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
            },
            "is_applied": true,
            "is_negated": false,
            "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
        },
        "tags": {
            "type": "collection",
            "field": "tags",
            "satisfied_by": "ANY",
            "is_applied": true,
            "is_negated": false,
            "values": [
                {
                    "key": "foo",
                    "value": "foo",
                    "is_applied": true,
                    "count": null,
                    "unset_link": null
                },
                {
                    "key": "bar",
                    "value": "bar",
                    "is_applied": true,
                    "count": null,
                    "unset_link": null
                }
            ],
            "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[name][NOT][STARTS_WITH]=foo"
        },
        "name": {
            "type": "string_match",
            "field": "name",
            "operator": "STARTS_WITH",
            "value": {
                "key": "foo",
                "value": "foo",
                "is_applied": true,
                "count": null,
                "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar"
            },
            "is_applied": true,
            "is_negated": true,
            "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar"
        }
    },
    "sorting": {
        "sorts": [
            {
                "field": "created_at",
                "is_applied": true,
                "directions": [
                    {
                        "field": "created_at",
                        "direction": "asc",
                        "is_applied": false,
                        "link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo&sort[created_at]=asc"
                    },
                    {
                        "field": "created_at",
                        "direction": "desc",
                        "is_applied": true,
                        "unset_link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
                    }
                ]
            },
            {
                "field": "name",
                "is_applied": false,
                "directions": [
                    {
                        "field": "name",
                        "direction": "asc",
                        "is_applied": false,
                        "link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo&sort[name]=asc"
                    },
                    {
                        "field": "name",
                        "direction": "desc",
                        "is_applied": false,
                        "link": "https://your.application/books?filters[date]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo&sort[name]=desc"
                    }
                ]
            }
        ]
    }
}
```

As you can see, OpenCubes:

- Parsed the query string into different kind of filters 
- Provided the URLs to unset applied filters
- Detected no sort was given through the Uri and returned you should order books by `created_at` desc
- Provided a native JSON serialization of its components for exposing them in an API.

## Dive into components

- [The Pager Component](doc/Pager.md)
- [The Sort Component](doc/Sort.md)
- [The Filter Component](doc/Filter.md)
- [The BreakDown Component](doc/BreakDown.md)


## Installation

_OpenCubes is at its early stage of development and is still subject to breaking changes._ 

```bash
composer require bentools/opencubes:dev-master
```


## Tests

```bash
./vendor/bin/phpunit
```


## License

MIT.