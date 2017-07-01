<?php namespace Monger\SearchRequest;

use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use InvalidArgumentException;

class FilterSet implements ArrayAccess, Countable, IteratorAggregate {

	/**
	 * @var array
	 */
	protected $filters = [];

	/**
	 * @var string
	 */
	protected $boolean;

	/**
	 * @var array
	 */
	protected $operators = ['=', '>', '>=', '<', '<=', '!=', 'in', 'not in', 'like', 'not like', 'exists', 'not exists', 'between', 'not between'];

	/**
	 * @param  string    $field
	 * @param  string    $operator
	 * @param  mixed     $value
	 * @param  string    $boolean
	 */
	public function __construct($boolean)
	{
		$this->boolean = $boolean;
	}

	/**
	 * Add a new filter condition
	 *
	 * @param  string|\Closure    $column
	 * @param  mixed              $operator    //if only two arguments are provided, this is the value
	 * @param  mixed              $value
	 * @param  mixed              $boolean
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function where($field, $operator = null, $value = null, $boolean = 'and')
	{
		//if we've received exactly two arguments, assume that $operator is the value and the actual operator is '='
		if (func_num_args() === 2)
		{
			list($value, $operator) = [$operator, '='];
		}

		//if the provided boolean is invalid, raise an exception
		if (!in_array($boolean, ['and', 'or']))
		{
			throw new InvalidArgumentException("A filter's boolean needs to be either 'and' or 'or'.");
		}

		//if the field is a Closure, assume that this is a nested conditional
		if ($field instanceof Closure)
		{
			return $this->whereNested($field, $boolean);
		}

		//if the operator isn't in the list of valid operators, assume the user is doing a null equality
		if (!in_array($operator, $this->operators))
		{
			list($value, $operator) = [$operator, '='];
		}

		//finally we can assume this is a simple filter that we can append onto the stack
		$this->filters[] = new Filter($field, $operator, $value, $boolean);

		return $this;
	}

	/**
	 * Add an "or" filter
	 *
	 * @param  string|\Closure    $column
	 * @param  mixed              $operator    //if only two arguments are provided, this is the value
	 * @param  mixed              $value
	 *
	 * @return $this
	 */
	public function orWhere($column, $operator = null, $value = null)
	{
		return $this->where($column, $operator, $value, 'or');
	}

	/**
	 * Add a nested filter
	 *
	 * @param  \Closure    $callback
	 * @param  string      $boolean
	 *
	 * @return $this
	 */
	public function whereNested(Closure $callback, $boolean = 'and')
	{
		$subFilterSet = new FilterSet($boolean);

		$this->filters[] = $subFilterSet;

		$callback($subFilterSet);

		return $this;
	}

	/**
	 * Add a between filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 * @param  string    $boolean
	 * @param  bool      $not
	 *
	 * @return $this
	 */
	public function whereBetween($field, array $values, $boolean = 'and', $not = false)
	{
		$operator = $not ? 'not between' : 'between';

		return $this->where($field, $operator, $values, $boolean);
	}

	/**
	 * Add an or between filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 *
	 * @return $this
	 */
	public function orWhereBetween($field, array $values)
	{
		return $this->whereBetween($field, $values, 'or');
	}

	/**
	 * Add a not between filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 * @param  string    $boolean
	 *
	 * @return $this
	 */
	public function whereNotBetween($field, array $values, $boolean = 'and')
	{
		return $this->whereBetween($field, $values, $boolean, true);
	}

	/**
	 * Add an or not between filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 *
	 * @return $this
	 */
	public function orWhereNotBetween($field, array $values)
	{
		return $this->whereNotBetween($field, $values, 'or');
	}

	/**
	 * Add an exists filter
	 *
	 * @param  string    $field
	 * @param  string    $boolean
	 * @param  bool      $not
	 *
	 * @return $this
	 */
	public function whereExists($field, $boolean = 'and', $not = false)
	{
		$operator = $not ? 'not exists' : 'exists';

		return $this->where($field, $operator, null, $boolean);
	}

	/**
	 * Add an or exists filter
	 *
	 * @param  string    $field
	 *
	 * @return $this
	 */
	public function orWhereExists($field)
	{
		return $this->whereExists($field, 'or');
	}

	/**
	 * Add a not exists filter
	 *
	 * @param  string    $field
	 * @param  string    $boolean
	 *
	 * @return $this
	 */
	public function whereNotExists($field, $boolean = 'and')
	{
		return $this->whereExists($field, $boolean, true);
	}

	/**
	 * Add an or not exists filter
	 *
	 * @param  string    $field
	 *
	 * @return $this
	 */
	public function orWhereNotExists($field)
	{
		return $this->whereNotExists($field, 'or');
	}

	/**
	 * Add an in filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 * @param  string    $boolean
	 * @param  bool      $not
	 *
	 * @return $this
	 */
	public function whereIn($field, array $values, $boolean = 'and', $not = false)
	{
		$operator = $not ? 'not in' : 'in';

		return $this->where($field, $operator, $values, $boolean);
	}

	/**
	 * Add an or in filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 *
	 * @return $this
	 */
	public function orWhereIn($field, array $values)
	{
		return $this->whereIn($field, $values, 'or');
	}

	/**
	 * Add a not in filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 * @param  string    $boolean
	 *
	 * @return $this
	 */
	public function whereNotIn($field, array $values, $boolean = 'and')
	{
		return $this->whereIn($field, $values, $boolean, true);
	}

	/**
	 * Add an or not in filter
	 *
	 * @param  string    $field
	 * @param  array     $values
	 *
	 * @return $this
	 */
	public function orWhereNotIn($field, array $values)
	{
		return $this->whereNotIn($field, $values, 'or');
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
	 * Converts the filter set into a representative array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'boolean' => $this->boolean,
			'filters' => array_map(function($filter)
			{
				return $filter->toArray();
			}, $this->filters)
		];
	}

	/**
	 * Adds the provided array filters to the filter set
	 *
	 * @param  array    $filters
	 */
	public function addFiltersFromArray(array $filters)
	{
		foreach ($filters as $filter)
		{
			if (isset($filter['filters']))
			{
				$instance = new FilterSet($filter['boolean']);

				$instance->addFiltersFromArray($filter['filters']);
			}
			else
			{
				$instance = new Filter($filter['field'], $filter['operator'], $filter['value'], $filter['boolean']);
			}

			$this->filters[] = $instance;
		}
	}

	/**
	 * Gets a top-level filter by its field name (only retrieves the first match)
	 *
	 * @param  string    $key
	 *
	 * @return mixed     //null | Filter
	 */
	public function getFilter($key)
	{
		foreach ($this->filters as $filter)
		{
			if (($filter instanceof Filter) && ($filter->getField() === $key))
			{
				return $filter;
			}
		}
	}

	/**
	 * Gets a top-level filter's value by its field name (only retrieves the first match)
	 *
	 * @param  string    $key
	 *
	 * @return mixed
	 */
	public function getFilterValue($key)
	{
		if ($filter = $this->getFilter($key))
			return $filter->getValue();
	}

	/**
	 * Count the number of conditions in this filter set
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->filters);
	}

	/**
	 * Get an iterator for the filters
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->filters);
	}

	/**
	 * Determine if a filter exists at an offset
	 *
	 * @param  mixed   $key
	 *
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->filters);
	}

	/**
	 * Get a filter at a given offset
	 *
	 * @param  mixed   $key
	 *
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->filters[$key];
	}

	/**
	 * Set the filter at a given offset
	 *
	 * @param  mixed   $key
	 * @param  mixed   $value
	 */
	public function offsetSet($key, $value)
	{
		if (is_null($key))
			$this->filters[] = $value;

		else
			$this->filters[$key] = $value;
	}

	/**
	 * Unset the filter at a given offset
	 *
	 * @param  string   $key
	 */
	public function offsetUnset($key)
	{
		unset($this->filters[$key]);
	}

}