<?php namespace Monger\SearchRequest\Tests\Facet\Invalidation;

use Monger\SearchRequest\SearchRequest;

class CreationTest extends \PHPUnit_Framework_TestCase {

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
		$this->request->facet(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldArray()
	{
		$this->request->facet(['not a string']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function fieldNumber()
	{
		$this->request->facet(5.4);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function multiple()
	{
		$this->request->addFacets(['something', null]);
	}

}