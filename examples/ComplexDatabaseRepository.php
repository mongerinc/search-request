<?php namespace App\Thing\Repositories;

use Monger\SearchRequest\Filter;
use Monger\SearchRequest\FilterSet;
use SomeFramework\SomeQueryBuilder;
use Monger\SearchRequest\SearchRequest;

class ComplexDatabaseRepository implements RepositoryInterface {

	/**
	 * Get any number of things defined by the provided search request
	 *
	 * @param  \Monger\SearchRequest\SearchRequest    $request
	 *
	 * @return array
	 */
	public function getThings(SearchRequest $request)
	{
		$query = $this->newQuery();

		$query->forPage($request->getPage(), $request->getLimit());

		$this->applyFilters($query, $request->getFilters());

		$this->applySorts($query, $request->getSorts());

		return $query->get()->toArray();
	}

	/**
	 * Apply the provided filter set to the database query
	 *
	 * @param  \SomeFramework\SomeQueryBuilder    $query
	 * @param  \Monger\SearchRequest\FilterSet    $filterSet
	 */
	protected function applyFilters(SomeQueryBuilder $query, FilterSet $filterSet)
	{
		foreach ($filterSet as $filter)
		{
			//if this is a basic filter, simply apply it
			if ($filter instanceof Filter)
			{
				$this->applyFilter($query, $filter);
			}
			//if this is a sub-FilterSet, we want to wrap the condition with the correct boolean function and call this function recursively
			else if ($filter instanceof FilterSet)
			{
				$subFilterSet = $filter;
				$conditionalFunction = $subFilterSet->isAnd() ? 'where' : 'orWhere';

				$query->{$conditionalFunction}(function($query) use ($subFilterSet)
				{
					$this->applyFilters($query, $subFilterSet);
				});
			}
		}
	}

	/**
	 * Apply the provided filter to the database query
	 *
	 * @param  \SomeFramework\SomeQueryBuilder    $query
	 * @param  \Monger\SearchRequest\Filter       $filter
	 */
	protected function applyFilter($query, Filter $filter)
	{
		$conditionalFunction = $filter->isAnd() ? 'where' : 'orWhere';

		$query->{$conditionalFunction}($filter->getField(), $filter->getOperator(), $filter->getValue());
	}

	/**
	 * Apply the provided sorts to the database query
	 *
	 * @param  \SomeFramework\SomeQueryBuilder    $query
	 * @param  array                              $sorts
	 */
	protected function applySorts($query, array $sorts)
	{
		foreach ($sorts as $sort)
		{
			$query->orderBy($sort->getField(), $sort->getDirection());
		}
	}

}