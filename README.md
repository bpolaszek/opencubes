# OpenCubes

**OpenCubes** is a framework-agnostic set of components that parses PSR-7 URIs into value objects:

- Pagination
- Filters
- Sorting
- Breakdown (group by)

## Overview

Look at the following URL: 

> https://your.application/books?page=3&per_page=50&sort[author.name]=asc&filters[published_at]=[2019-01-01+TO+2019-01-31]&filters[category_id]=12&filters[tags][]=foo&filters[tags][]=bar&filters[name][NOT][STARTS_WITH]=foo

Here's how OpenCubes parses it:

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
echo count($pager); // 4

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

Now, we can ask our persistence system (Doctrine, ElasticSearch, Solr, 3rd-party API, ...) to return books:

- From offset `100`, limit to `50` items
- Ordered by `author.name`
- Published between `2019-01-01` and `2019-01-31`
- In category id `12`
- Having tags `foo` or `bar`
- But their names must not start by `foo`.


## Customization

Each component comes with a lot of customization possibilities.

[Read More...](doc/Customization.md)

## HATEOAS

**OpenCubes** brings HATEOAS to your application by providing links for each component:

- Page / PageSize links
- Apply / remove sort link
- Apply / remove filter
- ...

Each native component (you're free to create your own ones) comes with a default JSON serialization which exposes the appropriate Urls.

[Read More...](doc/HATEOAS.md)

## Dive into components

- [The Pager Component](doc/Pager.md)
- [The Sort Component](doc/Sort.md)
- [The Filter Component](doc/Filter.md)
- [The BreakDown Component](doc/BreakDown.md)


## Installation

_OpenCubes is still at its early stage of development and subject to breaking changes._
 
_Feel free to contribute or report any issue._ 

```bash
composer require bentools/opencubes:1.0.x-dev
```

## Tests

```bash
./vendor/bin/phpunit
```


## License

MIT.