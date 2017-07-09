## Changelog

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