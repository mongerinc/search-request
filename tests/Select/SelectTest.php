<?php namespace Monger\SearchRequest\Tests\Select;

use Monger\SearchRequest\SearchRequest;

class SelectTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @test
	 */
	public function defaults()
	{
		$request = new SearchRequest;

		$this->assertEquals([], $request->getSelects());
	}

	/**
	 * @test
	 */
	public function singleString()
	{
		$request = new SearchRequest;

		$request->select('something');

		$this->assertEquals(['something'], $request->getSelects());
	}

	/**
	 * @test
	 */
	public function multipleStrings()
	{
		$request = new SearchRequest;

		$request->addSelect('something')->addSelect('somethingElse');

		$this->assertEquals(['something', 'somethingElse'], $request->getSelects());
	}

	/**
	 * @test
	 */
	public function singleArray()
	{
		$request = new SearchRequest;

		$request->select(['something', 'somethingElse']);

		$this->assertEquals(['something', 'somethingElse'], $request->getSelects());
	}

	/**
	 * @test
	 */
	public function multipleArray()
	{
		$request = new SearchRequest;

		$request->addSelect(['something'])->addSelect(['somethingElse']);

		$this->assertEquals(['something', 'somethingElse'], $request->getSelects());
	}

}