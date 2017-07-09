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
		$this->setField($values, 'field');
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
	 * @return array
	 */
	public function toArray()
	{
		return [
			'field' => $this->field,
			'sortType' => $this->sortType,
			'sortDirection' => $this->sortDirection,
			'minimumCount' => $this->minimumCount,
			'excludesOwnFilters' => $this->excludesOwnFilters,
		];
	}

}