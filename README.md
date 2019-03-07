# OpenCubes

**OpenCubes** is a set of components that you can use in any PHP project to translate (PSR-7) Uris into different kinds of value objects:

- Pagination
- Applied filters
- Applied sorts
- ...

For example, when a User hits `https://your.application/books?page=3&per_page=50&sort[author.name]=asc&filters[editor_id][]=5`, your controller will have to parse the query parameters to make your internal requests to your persistence system (Doctrine, ElasticSearch, a 3rd-party API, ...).

**OpenCubes** comes in the middle: it parses the URI and gives you Pagination, Filter and Sort objects, with default settings that you can define at the application and/or at the request level.

Besides, **OpenCubes** components can bring HATEOAS to your application by providing URIs for different actions: apply / unset filter, apply / unset sorting, etc.


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