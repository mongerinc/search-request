<?php namespace App\Thing\Repositories;

use SomeFramework\SomeQueryBuilder;
use Monger\SearchRequest\SearchRequest;

class SimpleDatabaseRepository implements RepositoryInterface {

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