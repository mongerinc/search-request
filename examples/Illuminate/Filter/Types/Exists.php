<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter\Types;

use Monger\SearchRequest\Filter;
use Illuminate\Database\Query\Builder as Query;

class Exists implements Type {

	public function shouldBeApplied(Filter $filter) : bool
	{
		return in_array($filter->getOperator(), ['exists', 'not exists']);
	}

	public function apply(Filter $filter, Query $query)
	{
		$not = $filter->getOperator() === 'not exists';

		$query->whereNull($filter->getField(), $filter->getBoolean(), $not);
	}

}