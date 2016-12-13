<?php namespace Monger\SearchRequest;

class SearchRequest {

	/**
	 * Holds the sort objects in order of precedence
	 *
	 * @var array
	 */
	protected $sorts = [];

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

}