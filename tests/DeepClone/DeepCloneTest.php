<?php namespace Monger\SearchRequest\Tests\DeepClone;

use Monger\SearchRequest\SearchRequest;

class DeepCloneTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function deepClone()
	{
		$originalRequest = SearchRequest::create()->where('something', true);
		$newRequest = clone $originalRequest;

		$originalRequest->where('somethingElse', false);

		$this->assertNull($newRequest->getFilterValue('somethingElse'));
	}

}