<?php namespace Monger\SearchRequest\Tests\Group;

use Monger\SearchRequest\SearchRequest;

class GroupTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @test
	 */
	public function defaults()
	{
		$request = new SearchRequest;

		$this->assertEquals([], $request->getGroups());
	}

	/**
	 * @test
	 */
	public function singleString()
	{
		$request = new SearchRequest;

		$request->groupBy('something');

		$this->assertEquals(['something'], $request->getGroups());
	}

	/**
	 * @test
	 */
	public function multipleStrings()
	{
		$request = new SearchRequest;

		$request->groupBy('something')->groupBy('somethingElse');

		$this->assertEquals(['something', 'somethingElse'], $request->getGroups());
	}

	/**
	 * @test
	 */
	public function singleArray()
	{
		$request = new SearchRequest;

		$request->groupBy(['something', 'somethingElse']);

		$this->assertEquals(['something', 'somethingElse'], $request->getGroups());
	}

	/**
	 * @test
	 */
	public function multipleArray()
	{
		$request = new SearchRequest;

		$request->groupBy(['something'])->groupBy(['somethingElse']);

		$this->assertEquals(['something', 'somethingElse'], $request->getGroups());
	}

}