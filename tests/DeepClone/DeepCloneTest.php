<?php namespace Monger\SearchRequest\Tests\DeepClone;

use Monger\SearchRequest\SearchRequest;

class DeepCloneTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function deepClone()
	{
		$originalRequest = SearchRequest::create();

		$originalRequest->page(2)->limit(10)
		                ->term('something')
		                ->select('something')
		                ->where('something', true)
		                ->addSort('something', 'asc')
		                ->groupBy('something')
		                ->facet('something')->page(2)->limit(100)->sortByCount()->setSortDirection('desc')->setMinimumCount(5)->includeOwnFilters();

		$newRequest = clone $originalRequest;

		$this->assertEquals($this->getExpectedJson(), $newRequest->toJson());
	}

	/**
	 * Gets the expected json for the search request scenario
	 *
	 * @return string
	 */
	protected function getExpectedJson()
	{
		return json_encode([
			'term' => 'something',
			'page' => 2,
			'limit' => 10,
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
		]);
	}

}