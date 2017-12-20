<?php namespace Monger\SearchRequest\Tests\Term;

use Monger\SearchRequest\SearchRequest;

class TermTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @test
	 */
	public function defaults()
	{
		$request = new SearchRequest;

		$this->assertEquals(null, $request->getTerm());
	}

	/**
	 * @test
	 */
	public function string()
	{
		$request = new SearchRequest;

		$request->term('something');

		$this->assertEquals('something', $request->getTerm());
	}

	/**
	 * @test
	 */
	public function clear()
	{
		$request = new SearchRequest;

		$request->term('something');
		$request->term(null);

		$this->assertEquals(null, $request->getTerm());
	}

}