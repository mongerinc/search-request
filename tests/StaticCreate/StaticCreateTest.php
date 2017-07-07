<?php namespace Monger\SearchRequest\Tests\Term;

use Monger\SearchRequest\SearchRequest;

class StaticCreateTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function emptyInput()
	{
		$this->assertTrue(SearchRequest::create() instanceof SearchRequest);
	}

	/**
	 * @test
	 */
	public function jsonInput()
	{
		$baselineRequest = new SearchRequest;
		$baselineRequest->where('something', true);

		$staticallyCreatedRequest = SearchRequest::create($baselineRequest->toJson());

		$this->assertEquals($baselineRequest->toArray(), $staticallyCreatedRequest->toArray());
	}

}