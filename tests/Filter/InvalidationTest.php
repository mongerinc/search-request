<?php namespace Monger\SearchRequest\Tests\Filter;

use Monger\SearchRequest\SearchRequest;

class InvalidationTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function invalidBoolean()
	{
		$request = new SearchRequest;

		$request->where('foo', null, null, 'not correct');
	}

}