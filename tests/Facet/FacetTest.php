<?php namespace Monger\SearchRequest\Tests\Facet;

use Monger\SearchRequest\SearchRequest;

class FacetTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function defaults()
	{
		$request = new SearchRequest;
		$facet = $request->facet('someField');

		$this->assertEquals(true, $facet->isValueSorting());
		$this->assertEquals(false, $facet->isCountSorting());
		$this->assertEquals('asc', $facet->getSortDirection());
		$this->assertEquals(1, $facet->getPage());
		$this->assertEquals(10, $facet->getLimit());
		$this->assertEquals(1, $facet->getMinimumCount());
		$this->assertEquals(true, $facet->shouldExcludeOwnFilters());
	}

	/**
	 * @test
	 */
	public function sort()
	{
		$request = new SearchRequest;
		$facet = $request->facet('someField');

		$facet->sortByCount();
		$this->assertEquals(false, $facet->isValueSorting());
		$this->assertEquals(true, $facet->isCountSorting());

		$facet->sortByValue();
		$this->assertEquals(true, $facet->isValueSorting());
		$this->assertEquals(false, $facet->isCountSorting());

		$facet->setSortDirection('asc');
		$this->assertEquals('asc', $facet->getSortDirection());

		$facet->setSortDirection('desc');
		$this->assertEquals('desc', $facet->getSortDirection());
	}

	/**
	 * @test
	 */
	public function pagination()
	{
		$request = new SearchRequest;
		$facet = $request->facet('someField');

		$facet->page(5)->limit(25);
		$this->assertEquals(5, $facet->getPage());
		$this->assertEquals(25, $facet->getLimit());

		$facet->nextPage();
		$this->assertEquals(6, $facet->getPage());
	}

	/**
	 * @test
	 */
	public function minimumCount()
	{
		$request = new SearchRequest;
		$facet = $request->facet('someField')->setMinimumCount(5);

		$this->assertEquals(5, $facet->getMinimumCount());
	}

	/**
	 * @test
	 */
	public function excludeOwnFilters()
	{
		$request = new SearchRequest;
		$facet = $request->facet('someField');

		$facet->excludeOwnFilters();
		$this->assertEquals(true, $facet->shouldExcludeOwnFilters());

		$facet->includeOwnFilters();
		$this->assertEquals(false, $facet->shouldExcludeOwnFilters());
	}

	/**
	 * @test
	 */
	public function addManyFacets()
	{
		$fields = ['someField', 'someOtherField'];
		$request = new SearchRequest;
		$request->addFacets($fields);

		$this->assertEquals(2, count($request->getFacets()));

		foreach ($request->getFacets() as $index => $facet)
		{
			$this->assertEquals([
				'field' => $fields[$index],
				'sortType' => 'value',
				'sortDirection' => 'asc',
				'page' => 1,
				'limit' => 10,
				'minimumCount' => 1,
				'excludesOwnFilters' => true,
			], $facet->toArray());
		}
	}

	/**
	 * @test
	 */
	public function getFacetByName()
	{
		$request = new SearchRequest;
		$request->addFacets(['someField', 'someOtherField']);
		$facet = $request->getFacet('someOtherField');

		$this->assertEquals([
			'field' => 'someOtherField',
			'sortType' => 'value',
			'sortDirection' => 'asc',
			'page' => 1,
				'limit' => 10,
			'minimumCount' => 1,
			'excludesOwnFilters' => true,
		], $facet->toArray());
	}

}