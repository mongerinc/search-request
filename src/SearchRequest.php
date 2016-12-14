<?php namespace Monger\SearchRequest;

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