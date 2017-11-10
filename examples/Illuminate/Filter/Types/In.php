<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter\Types;

use Monger\SearchRequest\Filter;
use Illuminate\Database\Query\Builder as Query;

class In implements Type {

	public function shouldBeApplied(Filter $filter) : bool
	{
		return in_array($filter->getOperator(), ['in', 'not in']);
	}

	public function apply(Filter $filter, Query $query)
	{
		$not = $filter->getOperator() === 'not in';

		$query->whereIn($filter->getField(), (array) $filter->getValue(), $filter->getBoolean(), $not);
	}

}