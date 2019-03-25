# HATEOAS

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

**OpenCubes** ensures you come back to page 1 after applying / unsetting a filter / a sort option / a new page size / ...