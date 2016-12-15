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

		$this->assertEquals($request->toJson(), $expectedRequest->toJson());
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
		        ->addSort('something', 'asc')->addSort('otherThing', 'desc')
		        ->where('fun', 'more')->orWhere(function($filterSet)
		        {
		            $filterSet->where('hats', '>', 'large')->where('butts', 'small');
		        });

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
			'page' => 5,
			'limit' => 50,
			'sorts' => [
				['field' => 'something', 'direction' => 'asc'],
				['field' => 'otherThing', 'direction' => 'desc'],
			],
			'filters' => [
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
			]
		]);
	}

}