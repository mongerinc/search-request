<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter\Types;

use Monger\SearchRequest\Filter;
use Illuminate\Database\Query\Builder as Query;

interface Type {

	public function shouldBeApplied(Filter $filter) : bool;

	public function apply(Filter $filter, Query $query);

}