<?php namespace Monger\SearchRequest\Tests\Pagination;

use Monger\SearchRequest\SearchRequest;

class InvalidationTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @var \Monger\SearchRequest\SearchRequest
	 */
	protected $request;

	/**
	 * Set up before each test
	 */
	public function setup()
	{
		$this->request = new SearchRequest;
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageNull()
	{
		$this->request->page(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageArray()
	{
		$this->request->page(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageFloat()
	{
		$this->request->page(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageNegative()
	{
		$this->request->page(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageBadString()
	{
		$this->request->page('not a number');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitNull()
	{
		$this->request->limit(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitArray()
	{
		$this->request->limit(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitFloat()
	{
		$this->request->limit(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitNegative()
	{
		$this->request->limit(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitBadString()
	{
		$this->request->limit('not a number');
	}

}