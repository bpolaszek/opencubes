# OpenCubes

**OpenCubes** is a framework-agnostic set of components that parses PSR-7 URIs into value objects:

- Pagination
- Filters
- Sorting
- Breakdown (group by)

## Overview

Consider you're browsing `https://your.application/books?page=3&per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo`:

```php
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\Component\Filter\Model\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\Model\RangeFilter;
use BenTools\OpenCubes\Component\Filter\Model\SimpleFilter;
use BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter;
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create();
$pager = $openCubes->getComponent(PagerComponent::getName(), ['total_items' => 160, 'default_size' => 50]);
$sorting = $openCubes->getComponent(SortComponent::getName());
$filters = $openCubes->getComponent(FilterComponent::getName());

// Pagination
echo $pager->getCurrentPage(); // 3
echo $pager->getCurrentOffset(); // 100

// Sorting
foreach ($sorting->getAppliedSorts() as $sort) {
    echo $sort->getField(); // author.name
    echo $sort->getDirection(); // asc
}

// Filters
foreach ($filters->getAppliedFilters() as $filter) {

    if ($filter instanceof RangeFilter) {
        echo $filter->getField(); // published_at
        echo $filter->getLeft(); // 2019-01-01
        echo $filter->getRight(); // 2019-01-31
    }

    if ($filter instanceof SimpleFilter) {
        echo $filter->getField(); // category_id
        echo $filter->getValue(); // 12
    }

    if ($filter instanceof CollectionFilter) {
        echo $filter->getField(); // tags
        print_r($filter->getValues()); // ['foo', 'bar']
    }

    if ($filter instanceof StringMatchFilter) {
        echo $filter->getField(); // name
        echo $filter->getValue(); // 'foo'
        echo $filter->getOperator(); // StringMatchFilter::STARTS_WITH
        var_dump($filter->isNegated()); // true
    }

}
```

Now, you can ask your persistence system (Doctrine, ElasticSearch, Solr, 3rd-party API, ...) to return books:

- From offset `100` to `150`
- Ordered by `author.name`
- Published between `2019-01-01` and `2019-01-31`
- In category id `12`
- Having tags `foo` or `bar`
- But their names must not start by `foo`.


## HATEOAS

**OpenCubes** brings HATEOAS to your application by providing links for each component:

- Page / PageSize links
- Apply / remove sort link
- Apply / remove filter
- ...

Each native component (you're free to create your own ones) comes with a default JSON serialization which exposes the appropriate Urls.

```php
echo json_encode([
    'filters' => $filters,
    'sorting' => $sorting,
], JSON_PRETTY_PRINT);
```

```json
{
  "filters": {
    "published_at": {
      "type": "range",
      "field": "published_at",
      "left": "2019-01-01",
      "right": "2019-01-31",
      "is_applied": true,
      "is_negated": false,
      "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
    },
    "category_id": {
      "type": "simple",
      "field": "category_id",
      "value": {
        "key": "12",
        "value": "12",
        "is_applied": true,
        "count": null,
        "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
      },
      "is_applied": true,
      "is_negated": false,
      "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
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
      "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[name][NOT][STARTS_WITH]=foo"
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
        "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar"
      },
      "is_applied": true,
      "is_negated": true,
      "unset_link": "https://your.application/books?per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar"
    }
  },
  "sorting": {
    "sorts": [
      {
        "field": "author.name",
        "is_applied": true,
        "directions": [
          {
            "field": "author.name",
            "direction": "asc",
            "is_applied": true,
            "unset_link": "https://your.application/books?per_page=50&filters[published_at]=[2019-01-01 TO 2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo"
          }
        ]
      }
    ]
  }
}
```

## Customization

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


## Dive into components

- [The Pager Component](doc/Pager.md)
- [The Sort Component](doc/Sort.md)
- [The Filter Component](doc/Filter.md)
- [The BreakDown Component](doc/BreakDown.md)


## Installation

_OpenCubes is still at its early stage of development and subject to breaking changes. Feel free to contribute or report any issue._ 

```bash
composer require bentools/opencubes:dev-master
```


## Tests

```bash
./vendor/bin/phpunit
```


## License

MIT.