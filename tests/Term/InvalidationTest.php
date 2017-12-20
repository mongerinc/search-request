<?php namespace Monger\SearchRequest\Tests\Term;

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
	public function pageArray()
	{
		$this->request->term(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageFloat()
	{
		$this->request->term(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageNegative()
	{
		$this->request->term(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageInt()
	{
		$this->request->term(6);
	}

}