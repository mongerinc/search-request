<?php namespace Monger\SearchRequest\Tests\DeepClone;

use Monger\SearchRequest\SearchRequest;

class DeepCloneTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function deepClone()
	{
		$originalRequest = SearchRequest::create();

		$originalRequest->term('something')
		                ->select('something')
		                ->where('something', true)
		                ->addSort('something', 'asc')
		                ->groupBy('something')
		                ->page(2)->limit(10)
		                ->facet('something')->sortByCount()->setSortDirection('desc')->setMinimumCount(5)->includeOwnFilters()->page(2)->limit(100);

		$newRequest = clone $originalRequest;

		$expected = $this->getExpected();
		$this->assertEquals(json_encode($expected), $newRequest->toJson());

		$originalRequest->all();
		$expected['unlimited'] = true;
		$newRequest = clone $originalRequest;
		$originalRequest->where('somethingElse', false);
		$this->assertEquals(json_encode($expected), $newRequest->toJson());
	}

	/**
	 * Gets the expected json for the search request scenario
	 *
	 * @return array
	 */
	protected function getExpected()
	{
		return [
			'term' => 'something',
			'page' => 2,
			'limit' => 10,
			'unlimited' => false,
			'selects' => ['something'],
			'groups' => ['something'],
			'sorts' => [
				['field' => 'something', 'direction' => 'asc'],
			],
			'filterSet' => [
				'boolean' => 'and',
				'filters' => [
					['field' => 'something', 'operator' => '=', 'value' => true, 'boolean' => 'and'],
				]
			],
			'facets' => [
				[
					'field' => 'something',
					'sortType' => 'count',
					'sortDirection' => 'desc',
					'page' => 2,
					'limit' => 100,
					'minimumCount' => 5,
					'excludesOwnFilters' => false,
				]
			],
		];
	}

}