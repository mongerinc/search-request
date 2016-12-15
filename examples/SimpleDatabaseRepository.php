<?php namespace App\Thing\Repositories;

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

		foreach ($request->getFilters() as $filter)
		{
			$query->where($filter->getField(), $filter->getValue());
		}

		$query->orderBy($request->getSort()->getField(), $request->getSort()->getDirection());

		return $query->get()->toArray();
	}

}