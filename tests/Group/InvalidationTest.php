<?php namespace Monger\SearchRequest\Tests\Group;

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
	public function fieldNull()
	{
		$this->request->groupBy(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldFloat()
	{
		$this->request->groupBy(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldInt()
	{
		$this->request->groupBy(1);
	}

}