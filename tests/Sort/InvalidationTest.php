<?php namespace Monger\SearchRequest\Tests\Sort;

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
		$this->request->sort(null, 'asc');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldArray()
	{
		$this->request->sort(['not a string'], 'asc');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldNumber()
	{
		$this->request->sort(56, 'asc');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function directionBadString()
	{
		$this->request->sort('someField', 'rising');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function directionNull()
	{
		$this->request->sort('someField', null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function directionArray()
	{
		$this->request->sort('someField', ['asc']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function directionNumber()
	{
		$this->request->sort('someField', -24);
	}

}