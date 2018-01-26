## Changelog

### 5.6.1
- Fixing automatic page reset undoing unlimited

### 5.6.0
- Adding `__callStatic` magic method

### 5.5.0
- Adding the unlimited flag for ignoring pagination

### 5.4.1
- Fixing `toJson()` for DateTime values

### 5.4.0
- Automatically resetting the page to 1 whenever a filter, term, sort, or grouping changes. Same for facets with the other filter/sort changes.

### 5.3.2
- Fixing the exists query request applier example

### 5.3.1
- Updating the examples

### 5.3.0
- Allow overriding of all filter values

### 5.2.1
- Fixing deep-cloning duplicating sorts/facets/groups

### 5.2.0
- Handle deep-cloning of the search request
- Fixing a few test namespaces

### 5.1.0
- Removing filters by name

### 5.0.0
- Added selects

### 4.0.0
- Added grouping

### 3.3.0
- Added regex filter operator
- Updated readme to include word operator companion methods
- Added helper methods for `like` and `not like` filters

### 3.2.0
- Added faceting

### 3.1.0
- Adding a static create() method for more fluent request building
- Ensuring all FilterSet pass-through methods return the search request where appropriate

### 3.0.1
- Version bumping readme

### 3.0.0
- Add mechanism for changing all declared field names in the request to other values
- Changed `getFilters()` to `getFilterSet()` for more clarity

### 2.1.0
- Added term property for global text searches

### 2.0.0
- Changing JSON/arrayable top-level filters to filterSet for more clarity
- Some minor comment cleanup

### 1.2.0
- Added a `getSkip()` method to the search request to help people avoid making that calcuation.
- Added contributing guidelines.

### 1.1.1
- Fixing the name of the example sorting method.

### 1.1.0
- `getFilter($field)` and `getFilterValue($field)` can now be called on the `SearchRequest` and the `FilterSet` to pull back the filter and value respectively of the first instance of a `Filter` with that field name.

### 1.0.0
- Initial release.