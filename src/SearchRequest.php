<?php namespace Monger\SearchRequest;

use BadMethodCallException;
use InvalidArgumentException;

class SearchRequest {

	/**
	 * Holds the selects
	 *
	 * @var array
	 */
	protected $selects = [];

	/**
	 * Holds the sort objects in order of precedence
	 *
	 * @var array
	 */
	protected $sorts = [];

	/**
	 * Holds the current Facets
	 *
	 * @var array
	 */
	protected $facets = [];

	/**
	 * Holds the current groups
	 *
	 * @var array
	 */
	protected $groups = [];

	/**
	 * The global search term
	 *
	 * @var string
	 */
	protected $term;

	/**
	 * The requested page
	 *
	 * @var int
	 */
	protected $page = 1;

	/**
	 * The page row limit
	 *
	 * @var int
	 */
	protected $limit = 10;

	/**
	 * Determines if pagination should be ignored
	 *
	 * @var bool
	 */
	protected $unlimited = false;

	/**
	 * The list of applicable filters
	 *
	 * @var FilterSet
	 */
	protected $filterSet;

	/**
	 * Determines if the page should reset when filter/group/sort changes
	 *
	 * @var bool
	 */
	protected $pageShouldAutomaticallyReset = true;

	/**
	 * @param  mixed    $json    //null | string
	 */
	public function __construct($json = null)
	{
		if ($json)
		{
			$this->overrideWithJson($json);
		}
		else
		{
			$this->filterSet = new FilterSet('and');
		}
	}

	/**
	 * Create a new search request instance
	 *
	 * @param  mixed    $json
	 *
	 * @return SearchRequest
	 */
	public static function create($json = null)
	{
		return new SearchRequest($json);
	}

	/**
	 * Overrides all values with the json input
	 *
	 * @param  string    $json
	 *
	 * @return SearchRequest
	 */
	public function overrideWithJson($json)
	{
		$inputs = json_decode($json, true);

		$this->sorts = [];
		$this->facets = [];
		$this->groups = [];
		$this->term = $inputs['term'];
		$this->selects = $inputs['selects'];
		$this->addSortsFromArray($inputs['sorts']);
		$this->addFacets($inputs['facets']);
		$this->groupBy($inputs['groups']);
		$this->addFilterSetFromArray($inputs['filterSet']);
		$this->page($inputs['page']);
		$this->limit($inputs['limit']);
		$this->unlimited = isset($inputs['unlimited']) ? $inputs['unlimited'] : false;
	}

	/**
	 * Set the selects
	 *
	 * @param  mixed    $field
	 *
	 * @return $this
	 */
	public function select($field)
	{
		if (!is_string($field) && !is_array($field))
			throw new InvalidArgumentException("A select field must be a string or an array of strings.");

		$this->selects = (array) $field;

		return $this;
	}

	/**
	 * Add a select
	 *
	 * @param  mixed    $field
	 *
	 * @return $this
	 */
	public function addSelect($field)
	{
		if (!is_string($field) && !is_array($field))
			throw new InvalidArgumentException("A select field must be a string or an array of strings.");

		$this->selects = array_merge($this->selects, (array) $field);

		return $this;
	}

	/**
	 * Get the selects
	 *
	 * @return array
	 */
	public function getSelects()
	{
		return $this->selects;
	}

	/**
	 * Adds the sorts from the provided input array
	 *
	 * @param  array    $sorts
	 */
	public function addSortsFromArray(array $sorts)
	{
		foreach ($sorts as $sort)
		{
			$this->addSort($sort['field'], $sort['direction']);
		}
	}

	/**
	 * Adds the filter set from the provided input array
	 *
	 * @param  array    $filterSet
	 */
	public function addFilterSetFromArray(array $filterSet)
	{
		$this->filterSet = new FilterSet($filterSet['boolean']);

		$this->filterSet->addFiltersFromArray($filterSet['filters']);
	}

	/**
	 * Adds the provided global search term
	 *
	 * @param  string    $term
	 *
	 * @return $this
	 */
	public function term($term)
	{
		if (!is_string($term) && !is_null($term))
			throw new InvalidArgumentException("A search term can only be a string or null.");

		$this->term = $term;

		if ($this->pageShouldAutomaticallyReset)
			$this->page = 1;

		return $this;
	}

	/**
	 * Gets the current global search term
	 *
	 * @return mixed
	 */
	public function getTerm()
	{
		return $this->term;
	}

	/**
	 * Overrides all sorts and sets the given field/direction as the primary sort
	 *
	 * @param  string    $field
	 * @param  string    $direction
	 *
	 * @return $this
	 */
	public function sort($field, $direction = 'asc')
	{
		$this->sorts = [];

		$this->addSort($field, $direction);

		return $this;
	}

	/**
	 * Adds a sort onto the existing set
	 *
	 * @param  string    $field
	 * @param  string    $direction
	 *
	 * @return $this
	 */
	public function addSort($field, $direction = 'asc')
	{
		$this->sorts[] = new Sort($field, $direction);

		if ($this->pageShouldAutomaticallyReset)
			$this->page = 1;

		return $this;
	}

	/**
	 * Gets the primary sort
	 *
	 * @return mixed    //null | Sort
	 */
	public function getSort()
	{
		return isset($this->sorts[0]) ? $this->sorts[0] : null;
	}

	/**
	 * Gets all sorts
	 *
	 * @return array
	 */
	public function getSorts()
	{
		return $this->sorts;
	}

	/**
	 * Add a facet
	 *
	 * @param  string    $field
	 *
	 * @return Facet
	 */
	public function facet($field)
	{
		$this->facets[] = $facet = new Facet(['field' => $field]);

		return $facet;
	}

	/**
	 * Add a facet
	 *
	 * @param  string    $field
	 *
	 * @return $this
	 */
	public function facetMany(array $fields)
	{
		foreach ($fields as $field)
		{
			$this->facet($field);
		}

		return $this;
	}

	/**
	 * Adds a group of facets
	 *
	 * @param  array    $facets
	 *
	 * @return $this
	 */
	public function addFacets(array $facets)
	{
		foreach ($facets as $facet)
		{
			$this->facets[] = new Facet($facet);
		}
	}

	/**
	 * Gets the facet for the provided field
	 *
	 * @param  string    $field
	 *
	 * @return mixed    //null | Facet
	 */
	public function getFacet($field)
	{
		foreach ($this->facets as $facet)
		{
			if ($facet->getField() === $field)
			{
				return $facet;
			}
		}
	}

	/**
	 * Gets all facets
	 *
	 * @return array
	 */
	public function getFacets()
	{
		return $this->facets;
	}

	/**
	 * Add a grouping
	 *
	 * @param  mixed    $field
	 *
	 * @return $this
	 */
	public function groupBy($field)
	{
		if (!is_string($field) && !is_array($field))
			throw new InvalidArgumentException("Group by can only be a string or array.");

		$this->groups = array_merge($this->groups, (array) $field);

		if ($this->pageShouldAutomaticallyReset)
			$this->page = 1;

		return $this;
	}

	/**
	 * Gets groups
	 *
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * Sets the requested page
	 *
	 * @param  int    $page
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function page($page)
	{
		if (!$this->isIntegeric($page) || $page <= 0)
			throw new InvalidArgumentException("A page can only be a positive integer.");

		$this->page = (int) $page;
		$this->unlimited(false);

		return $this;
	}

	/**
	 * Increments the page by one
	 *
	 * @return $this
	 */
	public function nextPage()
	{
		$this->page++;

		return $this;
	}

	/**
	 * Sets the requested page row limit
	 *
	 * @param  int    $limit
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function limit($limit)
	{
		if (!$this->isIntegeric($limit) || $limit <= 0)
			throw new InvalidArgumentException("A page row limit can only be a positive integer.");

		$this->limit = (int) $limit;
		$this->unlimited(false);

		return $this;
	}

	/**
	 * Sets the unlimited flag
	 *
	 * @param  bool    $unlimited
	 *
	 * @return this
	 */
	public function unlimited($unlimited = true)
	{
		$this->unlimited = (bool) $unlimited;

		return $this;
	}

	/**
	 * Alias for calling unlimited with true
	 *
	 * @return this
	 */
	public function all()
	{
		return $this->unlimited(true);
	}

	/**
	 * Gets the current page
	 *
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Gets the current page row limit
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Gets the unlimited flag
	 *
	 * @return bool
	 */
	public function isUnlimited()
	{
		return $this->unlimited;
	}

	/**
	 * Gets the current number of rows to skip
	 *
	 * @return int
	 */
	public function getSkip()
	{
		return ($this->page - 1) * $this->limit;
	}

	/**
	 * Disables automatic page resetting
	 *
	 * @return $this
	 */
	public function disableAutomaticPageReset()
	{
		$this->pageShouldAutomaticallyReset = false;

		return $this;
	}

	/**
	 * Enables automatic page resetting
	 *
	 * @return $this
	 */
	public function enableAutomaticPageReset()
	{
		$this->pageShouldAutomaticallyReset = true;

		return $this;
	}

	/**
	 * Gets the top-level filter set
	 *
	 * @return FilterSet
	 */
	public function getFilterSet()
	{
		return $this->filterSet;
	}

	/**
	 * Substitutes all field names in the request that match the provided set of substitutions
	 *
	 * @param  array    $substitutions
	 */
	public function substituteFields(array $substitutions)
	{
		foreach ($substitutions as $original => $substitution)
		{
			$this->substituteField($original, $substitution);
		}
	}

	/**
	 * Substitutes all field names in the request that match the provided substitution
	 *
	 * @param  string    $original
	 * @param  string    $substitution
	 */
	public function substituteField($original, $substitution)
	{
		if (!is_string($original) || !is_string($substitution))
			throw new InvalidArgumentException("Field subtitutions must consist of an original string and a substitution string.");

		foreach ($this->selects as $key => $select)
		{
			if ($select === $original)
			{
				$this->selects[$key] = $substitution;
			}
		}

		foreach ($this->sorts as $sort)
		{
			if ($sort->getField() === $original)
			{
				$sort->setField($substitution);
			}
		}

		foreach ($this->facets as $facet)
		{
			if ($facet->getField() === $original)
			{
				$facet->setField($substitution);
			}
		}

		foreach ($this->groups as $key => $group)
		{
			if ($group === $original)
			{
				$this->groups[$key] = $substitution;
			}
		}

		$this->filterSet->substituteField($original, $substitution);
	}

	/**
	 * Converts the search request into a representative array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'term' => $this->term,
			'page' => $this->page,
			'limit' => $this->limit,
			'unlimited' => $this->unlimited,
			'selects' => $this->selects,
			'groups' => $this->groups,
			'sorts' => array_map(function(Sort $sort) {return $sort->toArray();}, $this->sorts),
			'filterSet' => $this->filterSet->toArray(),
			'facets' => array_map(function(Facet $facet) {return $facet->toArray();}, $this->facets),
		];
	}

	/**
	 * Compiles a JSON string from the search request
	 *
	 * @return string
	 */
	public function toJson()
	{
		$request = $this->toArray();

		array_walk_recursive($request, function(&$value)
		{
			if (is_a($value, 'DateTime'))
				$value = $value->format('Y-m-d H:i:s');
		});

		return json_encode($request);
	}

	/**
	 * Determines if the provided value is integer-like. If it's a string, use a preg_match, otherwise just check if it's an int
	 *
	 * @param  mixed    $value
	 *
	 * @return bool
	 */
	protected function isIntegeric($value)
	{
		return is_string($value) ? (preg_match('/^-?[0-9]*$/D', $value) === 1) : is_int($value);
	}

	/**
	 * Handle deep cloning of the search request
	 */
	public function __clone()
	{
		$this->overrideWithJson($this->toJson());
	}

	/**
	 * Handle dynamic method calls into the class
	 *
	 * @param  string   $method
	 * @param  array    $parameters
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if ($this->isFilterSetPassthrough($method))
		{
			$result = call_user_func_array([$this->filterSet, $method], $parameters);

			return $result instanceof FilterSet ? $this : $result;
		}

		$className = __CLASS__;

		throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

	/**
	 * Handle dynamic static method calls into the class
	 *
	 * @param  string   $method
	 * @param  array    $parameters
	 *
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new static;

		return call_user_func_array([$instance, $method], $parameters);
	}

	/**
	 * Determines if the provided method should be passed through to the filter set
	 *
	 * @param  string    $method
	 *
	 * @return bool
	 */
	protected function isFilterSetPassthrough($method)
	{
		$isWhere = strpos(strtolower($method), 'where') !== false;
		$isFilterFetcher = strpos($method, 'getFilter') !== false;
		$isFilterRemover = strpos($method, 'removeFilter') !== false;

		if ($isWhere && $this->pageShouldAutomaticallyReset)
			$this->page = 1;

		return $isWhere || $isFilterFetcher || $isFilterRemover;
	}

}