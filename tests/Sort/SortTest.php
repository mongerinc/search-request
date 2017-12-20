<?php namespace Monger\SearchRequest\Tests\Sort;

use Monger\SearchRequest\SearchRequest;

class SortTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @test
	 */
	public function none()
	{
		$request = new SearchRequest;

		$this->assertNull($request->getSort());
		$this->assertEquals([], $request->getSorts());
	}

	/**
	 * @test
	 */
	public function simple()
	{
		$request = new SearchRequest;

		$request->sort('time', 'desc');

		$this->assertEquals('time', $request->getSort()->getField());
		$this->assertEquals('desc', $request->getSort()->getDirection());

		$this->assertEquals([$request->getSort()], $request->getSorts());

		$this->assertEquals(['field' => 'time', 'direction' => 'desc'], $request->getSort()->toArray());
	}

	/**
	 * @test
	 */
	public function multiple()
	{
		$request = new SearchRequest;

		$request->addSort('time', 'desc')->addSort('size', 'asc');

		//the primary sort should be time descending
		$this->assertEquals('time', $request->getSort()->getField());
		$this->assertEquals('desc', $request->getSort()->getDirection());

		//the full set of sorts should be time first, size second
		$sorts = $request->getSorts();
		$this->assertEquals('time', $sorts[0]->getField());
		$this->assertEquals('desc', $sorts[0]->getDirection());
		$this->assertEquals('size', $sorts[1]->getField());
		$this->assertEquals('asc', $sorts[1]->getDirection());

		$this->assertEquals(['field' => 'time', 'direction' => 'desc'], $sorts[0]->toArray());
		$this->assertEquals(['field' => 'size', 'direction' => 'asc'], $sorts[1]->toArray());
	}

}