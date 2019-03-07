# The BreakDown Component

The BreakDown component is particularly useful in analytics applications.

For example, when you provide a traffic report, you can break it down by device, by browser, by country, etc.

This component will help you know:

- Which breakdown is currently applied (and the Uri to remove it)
- Which breakdowns are available (and the Uris to apply them).

## Instanciating the component

Configurable options are:
- Available breakdown groups (which breakdown options you will offer to your users)
- Default breakdown groups (when the user did not select any)
- Applied breakdown groups (hydrated from Uri)
- Is multigroup enabled (do you allow breaking down on multiple groups)

```php
use BenTools\OpenCubes\OpenCubes;

$openCubes = OpenCubes::create([
    'breakdown' => [
        'available_groups' => ['device', 'country', 'date'],
        'default_groups' => ['date'],
    ],
    'breakdown_uri' => [
        'query_param' => 'group_by',
        'remove_sort' => true, // Reset applied sort after grouping
    ]
]);

$breakdown = $openCubes->getComponent('breakdown');
```