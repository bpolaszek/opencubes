# The Filter Component

The filter component is the more complex one, because there are different kind of filters:

- The simple filter, i.e. key == value
- The collection filter, think `in_array()`
- The range filter, i.e. from a date to another, or an number greater than 10
- The string match filter, to filter strings that starts with, ends with, or match a pattern
- The composite filter, which matches different kinds of filters for the same field.


## Instanciating the component

```php
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'filter_uri' => [
        'query_param' => 'filters',
    ]
]);

$filters = $openCubes->getComponent('filter');
```

### The Simple Filter

_https://example.org/cars?filters[brand]=Toyota_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\SimpleFilter $filter */
$filter = $filters->get('brand');
$filter->getType(); // simple
$filter->getValue(); // Toyota
$filter->isNegated(); // false - which means the brand must be Toyota
```

_https://example.org/cars?filters[brand][NOT]=Toyota_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\SimpleFilter $filter */
$filter = $filters->get('brand');
$filter->getType(); // simple
$filter->getValue(); // Toyota
$filter->isNegated(); // true - which means the brand MUST NOT be Toyota
```


### The Collection Filter

_https://example.org/cars?filters[colors][]=black&filters[colors][]=white_

_https://example.org/cars?filters[colors][ANY][]=black&filters[colors][ANY][]=white_ (alias)


```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\CollectionFilter $filter */
$filter = $filters->get('colors');
$filter->getType(); // collection
$filter->getValues(); // ['black', 'white']
$filter->isNegated(); // false
$filter->getSatisfiedBy(); // ANY - which means the car can be black OR white
```

_https://example.org/cars?filters[colors][ALL][]=black&filters[colors][ALL][]=white_ 

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\CollectionFilter $filter */
$filter = $filters->get('colors');
$filter->getType(); // collection
$filter->getValues(); // ['black', 'white']
$filter->isNegated(); // false
$filter->getSatisfiedBy(); // ALL - which means the car must be black AND white
```

_https://example.org/cars?filters[colors][NOT][ANY][]=black&filters[colors][NOT][ANY][]=white_ 

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\CollectionFilter $filter */
$filter = $filters->get('colors');
$filter->getType(); // collection
$filter->getValues(); // ['black', 'white']
$filter->isNegated(); // true
$filter->getSatisfiedBy(); // ANY - which means the car MUST NOT be black OR white
```

_https://example.org/cars?filters[colors][NOT][ALL][]=black&filters[colors][NOT][ALL][]=white_ 

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\CollectionFilter $filter */
$filter = $filters->get('colors');
$filter->getType(); // collection
$filter->getValues(); // ['black', 'white']
$filter->isNegated(); // true
$filter->getSatisfiedBy(); // ALL - which means the car MUST NOT be black AND white
```


### The Range Filter

_https://example.org/cars?filters[release_year]=[2009+TO+2019]_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\RangeFilter $filter */
$filter = $filters->get('release_year');
$filter->getType(); // range
$filter->getLeft(); // 2009
$filter->getRight(); // 2019
$filter->isNegated(); // false
```

_https://example.org/cars?filters[release_year][NOT]=[2009+TO+2019]_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\RangeFilter $filter */
$filter = $filters->get('release_year');
$filter->getType(); // range
$filter->getLeft(); // 2009
$filter->getRight(); // 2019
$filter->isNegated(); // true
```

_https://example.org/cars?filters[release_year]=[2009+TO+*]_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\RangeFilter $filter */
$filter = $filters->get('release_year');
$filter->getType(); // range
$filter->getLeft(); // 2009
$filter->getRight(); // null - which means all cars released after 2009
$filter->isNegated(); // false
```


### The String Match filter

_https://example.org/cars?filters[model][ENDS_WITH]=ris_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter $filter */
$filter = $filters->get('model');
$filter->getType(); // string_match
$filter->getValue(); // ris
$filter->getOperator(); // ENDS_WITH - available operators are LIKE, STARTS_WITH, ENDS_WITH, REGEXP
$filter->isNegated(); // false
```

_https://example.org/cars?filters[model][NOT][ENDS_WITH]=ris_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter $filter */
$filter = $filters->get('model');
$filter->getType(); // string_match
$filter->getValue(); // ris
$filter->getOperator(); // ENDS_WITH - available operators are LIKE, STARTS_WITH, ENDS_WITH, REGEXP
$filter->isNegated(); // true - so Yaris and Auris should be excluded
```


### The Composite filter

_https://example.org/cars?filters[model][]=Prius&filters[model][]=Yaris&filters[model][NOT][ENDS_WITH]=ris_

```php
/** @var \BenTools\OpenCubes\Component\Filter\Model\CompositeFilter $filter */
$filter = $filters->get('model');
$filter->getType(); // composite
$filter->getSatisfiedBy(); // ALL
$filter->isNegated(); // false
$filter->getFilters()[0]; // collection filter ['Prius', 'Yaris']
$filter->getFilters()[1]; // string_match filter - must not end by -ris
```