## SearchRequest

This library provides a set of classes that help represent requests for complex data and provides a way to convert requests to and from a standard JSON format. If you have interfaces with tons of parameters ($filters, $groupings, $page, $rowsPerPage, etc.), or if you're just looking for a standard way to communicate complex requests to other apps without racking your brain over how to represent this data in JSON, you will like this library.

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

#### Sorts

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