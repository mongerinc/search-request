<?php namespace My\App\Database\Query\SearchRequest\Illuminate;

use Monger\SearchRequest\Filter;
use Monger\SearchRequest\FilterSet;
use Monger\SearchRequest\SearchRequest;
use Illuminate\Database\Query\Builder as Query;
use My\App\Database\Query\SearchRequest\Illuminate\Filter\Filterer;

class Applier {

	protected $filterer;

	public function __construct(Filterer $filterer)
	{
		$this->filterer = $filterer;
	}

	public function apply(Query $query, SearchRequest $request)
	{
		$this->filterer->filter($query, $request->getFilterSet());

		$this->select($query, $request);

		$this->sort($query, $request);

		$this->group($query, $request);

		$query->forPage($request->getPage(), $request->getLimit());
	}

	protected function select(Query $query, SearchRequest $request)
	{
		if ($selects = $request->getSelects())
			$query->select($selects);
	}

	protected function sort(Query $query, SearchRequest $request)
	{
		foreach ($request->getSorts() as $sort)
		{
			$query->orderBy($sort->getField(), $sort->getDirection());
		}
	}

	protected function group(Query $query, SearchRequest $request)
	{
		foreach ($request->getGroups() as $group)
		{
			$query->groupBy($group);
		}
	}

}
