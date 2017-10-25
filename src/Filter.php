<?php namespace Monger\SearchRequest;

use InvalidArgumentException;

class Filter {

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var string
	 */
	protected $operator;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $boolean;

	/**
	 * @param  string    $field
	 * @param  string    $operator
	 * @param  mixed     $value
	 * @param  string    $boolean
	 */
	public function __construct($field, $operator, $value, $boolean)
	{
		$this->field = $field;
		$this->operator = $operator;
		$this->value = $value;
		$this->boolean = $boolean;
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
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getBoolean()
	{
		return $this->boolean;
	}

	/**
	 * @return bool
	 */
	public function isAnd()
	{
		return $this->boolean === 'and';
	}

	/**
	 * @return bool
	 */
	public function isOr()
	{
		return $this->boolean === 'or';
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
		$this->field = $field;

		return $this;
	}

	/**
	 * Sets the operator
	 *
	 * @param  string    $operator
	 *
	 * @return $this
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;

		return $this;
	}

	/**
	 * Sets the value
	 *
	 * @param  string    $value
	 *
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Sets the boolean
	 *
	 * @param  string    $boolean
	 *
	 * @return $this
	 */
	public function setBoolean($boolean)
	{
		$this->boolean = $boolean;

		return $this;
	}

	/**
	 * Converts the filter to a representative array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'field' => $this->field,
			'operator' => $this->operator,
			'value' => $this->value,
			'boolean' => $this->boolean,
		];
	}

}