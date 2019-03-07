# The Pager Component

The Pager Component will help your app handle pagination. 

You'll be able to set the page size (the number of items per page), get the current page number, enabling / disabling pagination, and so on.

## Instanciating the component

The default pager factory comes with the following options:

- The default page size
- The available sizes (only useful if you expose OpenCubes in your front-end)
- Is the pagination enabled / disabled (default enabled)
- The total number of items (you can skip this step to set it later on the component, because you'll probably need the component itself to query your DB first)
- The pager delta (to avoid returning 1000 pages)

```php
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'pager' => [
        'default_size' => 50,
        'available_sizes' => [10, 50, 100, 500],
        'delta' => 3,
    ],
    'pager_uri' => [
        'page_query_param' => 'page',
        'pagesize_query_param' => 'per_page',
    ],
]);

$pager = $openCubes->getComponent('pager');
```
