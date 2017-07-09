<?php namespace Monger\SearchRequest;

use InvalidArgumentException;

class Facet {

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var string
	 */
	protected $sortType = 'value';

	/**
	 * @var string
	 */
	protected $sortDirection = 'asc';

	/**
	 * @var int
	 */
	protected $page = 1;

	/**
	 * @var int
	 */
	protected $limit = 10;

	/**
	 * @var int
	 */
	protected $minimumCount = 1;

	/**
	 * @var int
	 */
	protected $excludesOwnFilters = true;

	/**
	 * @param  string    $field
	 */
	public function __construct(array $values)
	{
		$values = array_merge($this->toArray(), $values);

		$this->setField($values['field']);
		$this->setSortType($values['sortType']);
		$this->setSortDirection($values['sortDirection']);
		$this->setPage($values['page']);
		$this->setLimit($values['limit']);
		$this->setMinimumCount($values['minimumCount']);
		$this->setExcludesOwnFilters($values['excludesOwnFilters']);
	}

	/**
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @return bool
	 */
	public function isCountSorting()
	{
		return $this->sortType === 'count';
	}

	/**
	 * @return bool
	 */
	public function isValueSorting()
	{
		return $this->sortType === 'value';
	}

	/**
	 * @return string
	 */
	public function getSortDirection()
	{
		return $this->sortDirection;
	}

	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @return int
	 */
	public function getSkip()
	{
		return ($this->page - 1) * $this->limit;
	}

	/**
	 * @return int
	 */
	public function getMinimumCount()
	{
		return $this->minimumCount;
	}

	/**
	 * @return bool
	 */
	public function shouldExcludeOwnFilters()
	{
		return $this->excludesOwnFilters;
	}

	/**
	 * Sets the field
	 *
	 * @param  string    $field
	 *
	 * @return $this
	 */
	public function setField($field)
	{
		if (!is_string($field))
		{
			throw new InvalidArgumentException("The facet field must be a string");
		}

		$this->field = $field;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function sortByCount()
	{
		return $this->setSortType('count');
	}

	/**
	 * @return $this
	 */
	public function sortByValue()
	{
		return $this->setSortType('value');
	}

	/**
	 * @param  string    $type
	 *
	 * @return $this
	 */
	public function setSortType($type)
	{
		if (!in_array($type, ['count', 'value']))
			throw new InvalidArgumentException("The facet sort type should be either 'count' or 'value'");

		$this->sortType = $type;

		return $this;
	}

	/**
	 * @param  string    $direction
	 *
	 * @return $this
	 */
	public function setSortDirection($direction)
	{
		if (!is_string($direction) || !in_array($direction, ['asc', 'desc']))
			throw new InvalidArgumentException("The sort direction must be either 'asc' or 'desc'");

		$this->sortDirection = $direction;

		return $this;
	}

	/**
	 * @param  int    $page
	 *
	 * @return $this
	 */
	public function page($page)
	{
		return $this->setPage($page);
	}

	/**
	 * @param  int    $page
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setPage($page)
	{
		if (!$this->isIntegeric($page) || $page <= 0)
			throw new InvalidArgumentException("A page can only be a positive integer.");

		$this->page = (int) $page;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function nextPage()
	{
		$this->page++;

		return $this;
	}

	/**
	 * @param  int    $limit
	 *
	 * @return $this
	 */
	public function limit($limit)
	{
		return $this->setLimit($limit);
	}

	/**
	 * @param  int    $limit
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setLimit($limit)
	{
		if (!$this->isIntegeric($limit) || $limit <= 0)
			throw new InvalidArgumentException("A page row limit can only be a positive integer.");

		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * @param  int    $minimumCount
	 *
	 * @return $this
	 */
	public function setMinimumCount($minimumCount)
	{
		if (!$this->isIntegeric($minimumCount) || ($minimumCount < 0))
			throw new InvalidArgumentException("The minimum count must be an integer.");

		$this->minimumCount = $minimumCount;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function excludeOwnFilters()
	{
		return $this->setExcludesOwnFilters(true);
	}

	/**
	 * @return $this
	 */
	public function includeOwnFilters()
	{
		return $this->setExcludesOwnFilters(false);
	}

	/**
	 * @param  bool    $value
	 *
	 * @return $this
	 */
	public function setExcludesOwnFilters($value)
	{
		$this->excludesOwnFilters = (bool) $value;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'field' => $this->field,
			'sortType' => $this->sortType,
			'sortDirection' => $this->sortDirection,
			'page' => $this->page,
			'limit' => $this->limit,
			'minimumCount' => $this->minimumCount,
			'excludesOwnFilters' => $this->excludesOwnFilters,
		];
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

}