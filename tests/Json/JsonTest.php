<?php namespace Monger\SearchRequest\Tests\Json;

use Monger\SearchRequest\SearchRequest;

class JsonTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function toJson()
	{
		$request = $this->getExpectedRequest();

		$this->assertEquals($this->getExpectedJson(), $request->toJson());
	}

	/**
	 * @test
	 */
	public function fromJson()
	{
		$request = new SearchRequest($this->getExpectedJson());
		$expectedRequest = $this->getExpectedRequest();

		$this->assertEquals($expectedRequest->toJson(), $request->toJson());
	}

	/**
	 * Gets the expected search request
	 *
	 * @return \Monger\SearchRequest\SearchRequest
	 */
	protected function getExpectedRequest()
	{
		$request = new SearchRequest;

		$request->page(5)->limit(50)
		        ->term('search this')
		        ->select(['field1', 'field2'])
		        ->addSort('something', 'asc')->addSort('otherThing', 'desc')
		        ->groupBy('field')->groupBy('anotherField')
		        ->where('fun', 'more')->orWhere(function($filterSet)
		        {
		            $filterSet->where('hats', '>', 'large')->where('butts', 'small');
		        })
		        ->facet('something')->page(2)->limit(100)->sortByCount()->setSortDirection('desc')->setMinimumCount(5)->includeOwnFilters();

		return $request;
	}

	/**
	 * Gets the expected json for the search request scenario
	 *
	 * @return string
	 */
	protected function getExpectedJson()
	{
		return json_encode([
			'term' => 'search this',
			'page' => 5,
			'limit' => 50,
			'selects' => ['field1', 'field2'],
			'groups' => ['field', 'anotherField'],
			'sorts' => [
				['field' => 'something', 'direction' => 'asc'],
				['field' => 'otherThing', 'direction' => 'desc'],
			],
			'filterSet' => [
				'boolean' => 'and',
				'filters' => [
					['field' => 'fun', 'operator' => '=', 'value' => 'more', 'boolean' => 'and'],
					[
						'boolean' => 'or',
						'filters' => [
							['field' => 'hats', 'operator' => '>', 'value' => 'large', 'boolean' => 'and'],
							['field' => 'butts', 'operator' => '=', 'value' => 'small', 'boolean' => 'and'],
						]
					]
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