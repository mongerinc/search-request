<?php namespace Monger\SearchRequest\Tests\Pagination;

use Monger\SearchRequest\SearchRequest;

class PaginationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function defaults()
	{
		$request = new SearchRequest;

		$this->assertEquals(1, $request->getPage());
		$this->assertEquals(10, $request->getLimit());
	}

	/**
	 * @test
	 */
	public function custom()
	{
		$request = new SearchRequest;

		$request->page(5)->limit(50);

		$this->assertEquals(5, $request->getPage());
		$this->assertEquals(50, $request->getLimit());

		$request->nextPage();
		$this->assertEquals(6, $request->getPage());
	}

	/**
	 * @test
	 */
	public function integerStrings()
	{
		$request = new SearchRequest;

		$request->page('5')->limit('50');

		$this->assertEquals(5, $request->getPage());
		$this->assertEquals(50, $request->getLimit());
	}

}