# Statamic Enhanced GraphQL

A Statamic CMS GraphQL Addon that provides alternative GraphQL queries for collections, entries and global sets.

> ⚠️ This is a POC and while I consider it somewhat stable, it's not thoroughly tested. Use at your own risk.

## Features

This addon provides additional GraphQL queries for Statamic Pro.

- Transforms each collection, global set and taxonomy to individual GraphQL queries.
- Transform single entries to GraphQL queries.
- No [GraphQL Interfaces](https://graphql.org/learn/schema/#interfaces).
- Supports Pagination, Filtering & Sorting.

## Why

I'm using Statamic exclusively in Headless mode and make use of its GraphQL API. For end-to-end type-safety I'm writing
frontends in TypeScript. By default, Statamics default GraphQL implementation provides one query to query for all types
of entries using [GraphQL Interfaces](https://graphql.org/learn/schema/#interfaces), which means you need to implement a
lot of type guards to make use of the GraphQL types in the frontend, which adds boilerplate, query properties and
susceptibility to error.

This addon aims to provide individual queries for each collection and global set to keep the amount of type guards at a
minimum.

Querying for the content of an entry with the slug 'home' in a collection with the handle 'pages' would look like this
with Statamics default GraphQL implementation:

```graphql
query QueryHome {
  entry(collection: "pages", slug: "home") {
    __typename
    ... on Entry_Pages_Pages {
      content
    }
  }
}
```

Notice the additional `__typename` to implement a type guard in TypeScript:

```ts
const entryIsPage = (entry: EntryInterface): entry is Entry_Pages_Pages => {
  return entry.__typename === 'Entry_Pages_Pages'
}
```

The query this addon provides makes the type guard obsolete and the query more concise:

```graphql
query QueryHome {
  pagesEntry(slug: "home") {
    content
  }
}
```

Furthermore, you can transform individual entries to GraphQL queries:

```graphql
query QueryHome {
  home {
    content
  }
}
```

## How to Install

``` bash
composer require legrisch/statamic-enhanced-graphql
```

## Usage

This addon adds a settings section to Statamics Control Panel.

- Add collections to transform to queries. Make sure the collections only use one blueprint.
- Add global sets to transform to queries.
- Add taxonomies to transform to queries.
- Add single entry queries to transform to queries.

## TODO

- [X] filter on entries query
- [ ] queries for entries in collections with more than one blueprint
