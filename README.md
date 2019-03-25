[![Latest Stable Version](https://poser.pugx.org/bentools/opencubes/v/stable)](https://packagist.org/packages/bentools/opencubes)
[![License](https://poser.pugx.org/bentools/opencubes/license)](https://packagist.org/packages/bentools/opencubes)
[![Build Status](https://img.shields.io/travis/bpolaszek/opencubes/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/opencubes)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/opencubes/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/opencubes?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/bpolaszek/opencubes.svg?style=flat-square)](https://scrutinizer-ci.com/g/bpolaszek/opencubes)
[![Total Downloads](https://poser.pugx.org/bentools/opencubes/downloads)](https://packagist.org/packages/bentools/opencubes)

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
$pager = $openCubes->getComponent(PagerComponent::getName(), ['total_items' => 160, 'default_size' => 100]);
$sorting = $openCubes->getComponent(SortComponent::getName());
$filters = $openCubes->getComponent(FilterComponent::getName());

// Pagination
echo $pager->getCurrentPage(); // 3
echo $pager->getPerPage(); // 50 (it would be 100 when omiting the per_page parameter)
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
- Ordered by `author.name` (use your own logic to parse the field path)
- Published between `2019-01-01` and `2019-01-31`
- In category id `12`
- Matching tag `foo` OR `bar` (AND clauses [can also be set](doc/Filter.md))
- But their names MUST NOT start by `foo`.

Translating these value objects to query your database or API is now up to you! OpenCubes provides no bridge for the moment, but maybe in a near future.


## Customization

Each component comes with a lot of customization possibilities (query parameters, default settings, ...).

[Read More...](doc/Customization.md)

Besides, you can also create your own URI parsers / builders by implementing [the appropriate interfaces](src/Component/Pager/PagerUriManagerInterface.php).

## HATEOAS

**OpenCubes** brings HATEOAS to your application by providing links for each component:

- Page / PageSize links
- Apply / remove sort link
- Apply / remove filter
- ...

Each native component comes with a default JSON serialization which exposes the appropriate Urls. Being JSONserializable is not mandatory for your own components, it has been designed for a ready-to-use implementation. 
Different serializations can be achieved through your favourite serializer ([Symfony](https://symfony.com/doc/current/components/serializer.html) / [JMS](https://jmsyst.com/libs/serializer) to name a few).

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