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
		$this->assertEquals(200, $request->getSkip());

		$request->nextPage();
		$this->assertEquals(6, $request->getPage());
		$this->assertEquals(250, $request->getSkip());
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
		$this->assertEquals(200, $request->getSkip());
	}

	/**
	 * @test
	 */
	public function resetsByDefault()
	{
		$request = new SearchRequest;

		$request->page(5)->where('foo', true);
		$this->assertEquals(1, $request->getPage());

		$request->page(5)->sort('foo', 'desc');
		$this->assertEquals(1, $request->getPage());

		$request->page(5)->groupBy('foo');
		$this->assertEquals(1, $request->getPage());

		$request->page(5)->term('foo');
		$this->assertEquals(1, $request->getPage());
	}

	/**
	 * @test
	 */
	public function noResetsWhenDisabled()
	{
		$request = SearchRequest::create()->disableAutomaticPageReset();

		$request->page(5)->where('foo', true);
		$this->assertEquals(5, $request->getPage());

		$request->page(5)->sort('foo', 'desc');
		$this->assertEquals(5, $request->getPage());

		$request->page(5)->groupBy('foo');
		$this->assertEquals(5, $request->getPage());

		$request->page(5)->term('foo');
		$this->assertEquals(5, $request->getPage());
	}

	/**
	 * @test
	 */
	public function resetsWhenReenabled()
	{
		$request = SearchRequest::create()->disableAutomaticPageReset()->enableAutomaticPageReset();

		$request->page(5)->where('foo', true);
		$this->assertEquals(1, $request->getPage());
	}

	/**
	 * @test
	 */
	public function unlimited()
	{
		$request = SearchRequest::create();
		$this->assertFalse($request->isUnlimited());

		$request->unlimited();
		$this->assertTrue($request->isUnlimited());

		$request->unlimited(false);
		$this->assertFalse($request->isUnlimited());

		$request->all();
		$this->assertTrue($request->isUnlimited());
	}

	/**
	 * @test
	 */
	public function resetDoesntLimitRequest()
	{
		$request = new SearchRequest;

		$request->all();

		$request->where('foo', true);
		$this->assertTrue($request->isUnlimited());

		$request->sort('foo', 'desc');
		$this->assertTrue($request->isUnlimited());

		$request->term('foo');
		$this->assertTrue($request->isUnlimited());

		$request->groupBy('foo');
		$this->assertTrue($request->isUnlimited());
	}

	/**
	 * @test
	 */
	public function settingPaginationFalsifiesUnlimited()
	{
		$request = SearchRequest::create()->unlimited()->page(1);
		$this->assertFalse($request->isUnlimited());

		$request = SearchRequest::create()->unlimited()->limit(1);
		$this->assertFalse($request->isUnlimited());
	}

}