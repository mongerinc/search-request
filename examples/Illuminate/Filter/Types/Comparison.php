<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter\Types;

use Monger\SearchRequest\Filter;
use Illuminate\Database\Query\Builder as Query;

class Comparison implements Type {

	public function shouldBeApplied(Filter $filter) : bool
	{
		return in_array($filter->getOperator(), ['=', '>', '>=', '<', '<=', '!=']);
	}

	public function apply(Filter $filter, Query $query)
	{
		$query->where($filter->getField(), $filter->getOperator(), $filter->getValue(), $filter->getBoolean());
	}

}