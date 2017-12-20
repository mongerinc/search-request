<?php namespace Monger\SearchRequest\Tests\Facet\Invalidation;

use Monger\SearchRequest\SearchRequest;

class ModificationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Monger\SearchRequest\Facet
	 */
	protected $facet;

	/**
	 * Set up before each test
	 */
	public function setup()
	{
		$this->facet = SearchRequest::create()->facet('someField');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function sortDirectionBadString()
	{
		$this->facet->setSortDirection('rising');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function sortDirectionNull()
	{
		$this->facet->setSortDirection(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function sortDirectionArray()
	{
		$this->facet->setSortDirection(['asc']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function sortDirectionNumber()
	{
		$this->facet->setSortDirection(-24);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageNull()
	{
		$this->facet->page(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageArray()
	{
		$this->facet->page(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageFloat()
	{
		$this->facet->page(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageNegative()
	{
		$this->facet->page(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function pageBadString()
	{
		$this->facet->page('not a number');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitNull()
	{
		$this->facet->limit(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitArray()
	{
		$this->facet->limit(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitFloat()
	{
		$this->facet->limit(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitNegative()
	{
		$this->facet->limit(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function limitBadString()
	{
		$this->facet->limit('not a number');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function minimumCountNull()
	{
		$this->facet->setMinimumCount(null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function minimumCountArray()
	{
		$this->facet->setMinimumCount(['not an integer']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function minimumCountFloat()
	{
		$this->facet->setMinimumCount(56.54);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function minimumCountNegative()
	{
		$this->facet->setMinimumCount(-5);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function minimumCountString()
	{
		$this->facet->setMinimumCount('not a number');
	}

}