## SearchRequest

This library provides a set of classes that help represent requests for complex data and provides a way to convert requests to and from a standard JSON format. If you have interfaces with tons of parameters ($filters, $groupings, $page, $rowsPerPage, etc.), or if you're just looking for a standard way to communicate complex requests to other apps without racking your brain over how to represent this data in JSON, you will like this library.

[![Build Status](https://travis-ci.org/mongerinc/search-request.png?branch=master)](https://travis-ci.org/mongerinc/search-request)

Table of contents
-----------------
* [Installation](#installation)
* [Usage](#usage)
  * [JSON](#json)
  * [Sorting](#sorting)
  * [Pagination](#pagination)
  * [Filtering](#filtering)

### Installation

Install SearchRequest via composer by adding the following to the `require` block in your `composer.json` file:

```
"mongerinc/search-request": "1.*"
```

### Usage

When creating a `SearchRequest` from scratch, you first need to instantiate a request:

```php
$request = new SearchRequest;
```

As a starting point, each search request has no sorts, no filters, and no groupings. Pagination starts at page 1 and by default there is a limit of 10 rows per page.

#### JSON

Using the `toJson()` method, each `SearchRequest` instance can be compiled into a JSON string that you can use to communicate across application boundaries.

```
$request->toJson();
```

Likewise, you can build a new `SearchRequest` instance using a JSON string that was compiled by a `SearchRequest` instance.

```
$request = new SearchRequest($json);
```

#### Sorting

The most common method of sorting the request is by using the `sort()` method which overrides any existing sorts:

```php
$request->sort('field', 'asc');
```

The first parameter in any sort call is the string field and the second parameter is the sort direction which is limited to `asc` and `desc`.

If you want to create a request with multiple sorts, you can call the `addSort()` method instead. You can chain this method:

```php
$request->addSort('field', 'asc')->addSort('otherField', 'desc');
```

If you want to retrieve the sorts, you can either call the `getSort()` method to get the primary sort, or you can call `getSorts()` to get the array of all sorts. Each sort is represented by a `Sort` instance where you can ask for the field and the direction:

```php
$sort = $request->getSort();

$databaseQuery->orderBy($sort->getField(), $sort->getDirection());
```

#### Pagination

Creating a new `SearchRequest` defaults to page 1 and a limit of 10 rows per page. If you want to modify this, you can call the `page()` and `limit()` methods:

```php
$request->page(5)->limit(40);
```

If you want to simply go to the next page, you can call the `nextPage()` method and it will increment the current page by 1.

Retrieving the page and limit is done by the methods `getPage()` and `getLimit()`:

```php
$limit = $request->getLimit();
$page = $request->getPage();

$databaseQuery->take($limit)->skip(($page - 1) * $limit);
```

#### Filtering

Filtering a `SearchRequest` can be done using the `where()` method. An operator can be provided as the second argument where the possible types are `=`, `>`, `>=`, `<`, `<=`, `!=`, `in`, `not in`, `like`, `not like`, `exists`, `not exists`, `between`, and `not between`. If no operator is provided, it is assumed to be `=`.

```php
$request->where('someField', '>=', 5.45)
        ->where('isFun', true);            //assumed to be an equality
```

Reading filters from the search request can be done using the `getFilters()` method:

```php
foreach ($request->getFilters() as $filter)
{
	$databaseQuery->where($filter->getField(), $filter->getOperator(), $filter->getValue());
}
```

More complex filtering can be accomplished by using nested conditions. Assuming you wanted to make a request representing the following pseudo-SQL conditional statement:

```sql
...WHERE goodTimes = true AND (profit > 1000 OR revenue > 1000000)
```

...you would do the following...

```php
$request->where('goodTimes', true)
        ->where(function($filterSet)
        {
            $filterSet->where('profit', '>', 1000)
                      ->orWhere('revenue', '>', 1000000);
        });
```

When reading a complex set of conditionals back from the `SearchRequest`, there are several important concepts to understand:

1. Each nesting layer (including the top layer) is represented by a `FilterSet`. A `FilterSet` has a boolean (and/or) and can be comprised of any combination of `Filter` and `FilterSet` objects. These objects are provided in the order they were entered.
2. A `Filter` object represents a field, value, and conditional operator along with a boolean (and/or).

Since the nesting of conditionals is theoretically infinite, you may want to implement a recursive function to apply the request to the library of your choice (like a database query builder). An example of this can be seen in the `/examples` directory.