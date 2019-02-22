# The BreakDown Component

The BreakDown component is particularly useful in analytics applications.

For example, when you provide a traffic report, you can break it down by device, by browser, by country, etc.

This component will help you know:

- Which breakdown is currently applied (and the Uri to remove it)
- Which breakdowns are available (and the Uris to apply them).

## The BreakDownComponentFactory

The factory will handle your default options and is responsible to instanciate BreakDown components.

Configurable options are:
- Available breakdown groups (which breakdown options you will offer to your users)
- Default breakdown groups (when the user did not select any)
- Applied breakdown groups (hydrated from Uri)
- Is multigroup enabled (do you allow breaking down on multiple groups)

### Overview

```php
use BenTools\OpenCubes\Component\BreakDown\BreakDownComponentFactory;
use function BenTools\OpenCubes\current_location;

$breakdownFactory = new BreakDownComponentFactory([
    BreakDownComponentFactory::OPT_AVAILABLE_GROUPS => [
        'date',
        'device',
        'browser',
        'country',
    ],
    BreakDownComponentFactory::OPT_DEFAULT_GROUPS => [
        'date',
    ]
]);

$component = $breakdownFactory->createComponent(current_location());
```

### The BreakDownUriManager

```php
use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManager;

$uriManager = new BreakDownUriManager([
    BreakDownUriManager::OPT_BREAKDOWN_QUERY_PARAM => 'group_by', // default is 'breakdown'
]);

$breakdownFactory = new BreakDownComponentFactory($options, $uriManager);
```