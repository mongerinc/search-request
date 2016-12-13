<?php namespace Monger\SearchRequest;

use InvalidArgumentException;

class Sort {

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var string
	 */
	protected $direction;

	/**
	 * @param  string    $field
	 * @param  string    $direction
	 */
	public function __construct($field, $direction = 'asc')
	{
		if (!is_string($field))
			throw new InvalidArgumentException("The sort field should be a string.");

		if (!in_array($direction, ['asc', 'desc']))
			throw new InvalidArgumentException("A sort direction needs to be either 'asc' or 'desc'.");

		$this->field = $field;
		$this->direction = $direction;
	}

	/**
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @return string
	 */
	public function getDirection()
	{
		return $this->direction;
	}

	/**
	 * Changes the direction from 'asc' to 'desc' or vice versa
	 */
	public function changeDirection()
	{
		$this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
	}

}