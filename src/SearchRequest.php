<?php namespace Monger\SearchRequest;

use BadMethodCallException;
use InvalidArgumentException;

class SearchRequest {

	/**
	 * Holds the sort objects in order of precedence
	 *
	 * @var array
	 */
	protected $sorts = [];

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
	 * The list of applicable filters
	 *
	 * @var FilterSet
	 */
	protected $filterSet;

	/**
	 * @param  mixed    $json    //null | string
	 */
	public function __construct($json = null)
	{
		if ($json)
		{
			$inputs = json_decode($json, true);

			$this->page = $inputs['page'];
			$this->limit = $inputs['limit'];
			$this->addSortsFromArray($inputs['sorts']);
			$this->addFilterSetFromArray($inputs['filters']);
		}
		else
		{
			$this->filterSet = new FilterSet('and');
		}
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
	 * Overrides all sorts and sets the given field/direction as the primary sort
	 *
	 * @param  string    $field
	 * @param  string    $direction
	 *
	 * @return $this
	 */
	public function sort($field, $direction = 'asc')
	{
		$this->sorts = [new Sort($field, $direction)];

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

		return $this;
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
	 * Gets the top-level filter set
	 *
	 * @return FilterSet
	 */
	public function getFilters()
	{
		return $this->filterSet;
	}

	/**
	 * Converts the search request into a representative array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'page' => $this->page,
			'limit' => $this->limit,
			'sorts' => array_map(function(Sort $sort) {return $sort->toArray();}, $this->sorts),
			'filters' => $this->filterSet->toArray(),
		];
	}

	/**
	 * Compiles a JSON string from the search request
	 *
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->toArray());
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
		if (strpos(strtolower($method), 'where') !== false)
		{
			return call_user_func_array([$this->filterSet, $method], $parameters);
		}

		$className = static::class;

		throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}