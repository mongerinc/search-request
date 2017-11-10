<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter;

use Monger\SearchRequest\Filter;
use Monger\SearchRequest\FilterSet;
use Illuminate\Database\Query\Builder as Query;

class Filterer {

	protected $typeFactory;

	public function __construct(TypeFactory $typeFactory)
	{
		$this->typeFactory = $typeFactory;
	}

	public function filter(Query $query, FilterSet $filterSet)
	{
		$this->applyFilterSet($query, $filterSet);
	}

	protected function applyFilterSet(Query $query, FilterSet $filterSet)
	{
		foreach ($filterSet as $filter)
		{
			if ($filter instanceof Filter)
			{
				$this->applyFilter($query, $filter);
			}
			else if ($filter instanceof FilterSet)
			{
				$subFilterSet = $filter;
				$conditionalFunction = $subFilterSet->isAnd() ? 'where' : 'orWhere';

				$query->{$conditionalFunction}(function($query) use ($subFilterSet)
				{
					$this->applyFilterSet($query, $subFilterSet);
				});
			}
		}
	}

	protected function applyFilter(Query $query, Filter $filter)
	{
		if ($filterTypeApplier = $this->typeFactory->buildFromFilter($filter))
		{
			$filterTypeApplier->apply($filter, $query);
		}
	}

}