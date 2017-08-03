<?php namespace Monger\SearchRequest\Tests;

use Monger\SearchRequest\Facet;
use Monger\SearchRequest\SearchRequest;

class FieldSubstitutionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function selects()
	{
		$request = new SearchRequest;

		$request->select(['first', 'second', 'third']);

		$request->substituteFields(['first' => 'subFirst', 'third' => 'subThird']);
		$this->assertEquals(['subFirst', 'second', 'subThird'], $request->toArray()['selects']);

		$request->substituteField('second', 'subSecond');
		$this->assertEquals(['subFirst', 'subSecond', 'subThird'], $request->toArray()['selects']);
	}

	/**
	 * @test
	 */
	public function sorts()
	{
		$request = new SearchRequest;

		$request->addSort('first', 'asc')->addSort('second', 'desc')->addSort('third', 'asc');

		$request->substituteFields([
			'first' => 'subFirst',
			'third' => 'subThird',
		]);

		$this->assertEquals([
			['field' => 'subFirst', 'direction' => 'asc'],
			['field' => 'second', 'direction' => 'desc'],
			['field' => 'subThird', 'direction' => 'asc'],
		], $request->toArray()['sorts']);

		$request->substituteField('second', 'subSecond');

		$this->assertEquals([
			['field' => 'subFirst', 'direction' => 'asc'],
			['field' => 'subSecond', 'direction' => 'desc'],
			['field' => 'subThird', 'direction' => 'asc'],
		], $request->toArray()['sorts']);
	}

	/**
	 * @test
	 */
	public function groups()
	{
		$request = SearchRequest::create()->groupBy('first')->groupBy('second')->groupBy('third');

		$request->substituteFields(['first' => 'subFirst', 'third' => 'subThird']);
		$this->assertEquals(['subFirst', 'second', 'subThird'], $request->toArray()['groups']);

		$request->substituteField('second', 'subSecond');
		$this->assertEquals(['subFirst', 'subSecond', 'subThird'], $request->toArray()['groups']);
	}

	/**
	 * @test
	 */
	public function facets()
	{
		$request = new SearchRequest;

		$request->facetMany(['first', 'second']);

		$request->substituteFields([
			'first' => 'subFirst',
		]);

		$this->assertNull($request->getFacet('first'));
		$this->assertTrue($request->getFacet('subFirst') instanceof Facet);
		$this->assertTrue($request->getFacet('second') instanceof Facet);
	}

	/**
	 * @test
	 */
	public function filters()
	{
		$request = new SearchRequest;

		$request->where('first', true)
		        ->where('second', true)
		        ->where(function($filterSet)
		        {
		            $filterSet->where('third', true)
		                      ->where('fourth', true)
		                      ->where(function($filterSet)
		                      {
		                          $filterSet->where('fifth', true)
		                                    ->where('sixth', true);
		                      });
		        });

		$request->substituteFields([
			'first' => 'subFirst',
			'third' => 'subThird',
			'fifth' => 'subFifth',
		]);

		$expected = [
			'boolean' => 'and',
			'filters' => [
				['field' => 'subFirst', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
				['field' => 'second', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
				[
					'boolean' => 'and',
					'filters' => [
						['field' => 'subThird', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
						['field' => 'fourth', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
						[
							'boolean' => 'and',
							'filters' => [
								['field' => 'subFifth', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
								['field' => 'sixth', 'value' => true, 'operator' => '=', 'boolean' => 'and'],
							]
						],
					]
				],
			],
		];

		$this->assertEquals($expected, $request->getFilterSet()->toArray());

		$request->substituteField('second', 'subSecond');

		$expected['filters'][1]['field'] = 'subSecond';

		$this->assertEquals($expected, $request->getFilterSet()->toArray());
	}

}