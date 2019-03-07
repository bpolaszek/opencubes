# The Sort Component

The Sort component will help you:

- Get which sort has been applied by the user
- Get which sort should be applied by default
- Provide your user which sorting options are available


## Instanciating the component

Configurable options are:
- Available sorts (which sorting options you will offer to your users)
- Default sorts (when the user did not select any sort)
- Applied sorts (hydrated from Uri)
- Is multisort enabled (do you allow sorting on multiple fields)

```php
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'sort' => [
        'available_sorts' => ['device' => ['asc', 'desc'], 'country' => ['asc', 'desc']],
        'default_sorts' => ['date' => 'asc'],
    ],
    'sort_uri' => [
        'query_param' => 'order_by',
    ]
]);

$sorting = $openCubes->getComponent('sort');
```