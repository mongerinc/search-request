<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter\Types;

use Monger\SearchRequest\Filter;
use Illuminate\Database\Query\Builder as Query;

class Between implements Type {

	public function shouldBeApplied(Filter $filter) : bool
	{
		return in_array($filter->getOperator(), ['between', 'not between']);
	}

	public function apply(Filter $filter, Query $query)
	{
		$not = $filter->getOperator() === 'not between';

		$query->whereBetween($filter->getField(), $filter->getValue(), $filter->getBoolean(), $not);
	}

}