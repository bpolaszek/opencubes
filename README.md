# OpenCubes

OpenCubes is a set of components that you can use in any PHP project to translate (PSR-7) Uris into different kinds of value objects:

- Pagination
- Applied filters
- Applied sorts
- ...

For example, when a User hits `http://your.application/books?page=3&per_page=50&sort[author.name]=asc&filters[editor_id][]=5&filters[editor_id][]=15&filters[author.name][STARTS_WITH]=Arch`, your application will probably know that it should retrieve some books but you generally translate sort, filters and pagination from the query string by yourself.

With OpenCubes, for each component, you will be able to define:

- The default settings at the application level (for example, the number of results per page)
- The default settings at the request level (for exemple, the default sorting of books if none is provided)
- An URL parser (to get the user's settings from the query string - but keep calm, we provide a default implementation!) 

Another great feature of OpenCubes is that it provides a default JSON serialization for each component. 

When you're working on API-centric applications, you can expose these components and your front-end (React, VueJS, Angular, or anything) will precisely know:

- Which filters have been applied (and the URIs to remove them)
- Which sorts have been applied (and the URIs to remove them), and which other sorts are available (and the URIs to apply them)
- Which page size has been applied (and the URI to remove it), and which other page sizes are available  (and the URIs to apply them)

We work hard to cover most of use cases, but you can define your own component factories, your own URL parsers & builders, and even your own components!


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