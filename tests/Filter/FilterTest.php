<?php namespace Monger\SearchRequest\Tests\Filter;

use Monger\SearchRequest\Filter;
use Monger\SearchRequest\FilterSet;
use Monger\SearchRequest\SearchRequest;

class FilterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function none()
	{
		$request = new SearchRequest;

		$this->assertEquals(0, count($request->getFilters()));
		$this->assertEquals('and', $request->getFilters()->getBoolean());
		$this->assertTrue($request->getFilters()->isAnd());
		$this->assertFalse($request->getFilters()->isOr());
	}

	/**
	 * @test
	 */
	public function simpleEquality()
	{
		$request = new SearchRequest;

		$request->where('someField', true);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'someField', 'operator' => '=', 'value' => true, 'boolean' => 'and']
		]));
	}

	/**
	 * @test
	 */
	public function operators()
	{
		$request = new SearchRequest;
		$operators = ['=', '>', '>=', '<', '<=', '!=', 'in', 'not in', 'like', 'not like', 'exists', 'not exists', 'between', 'not between'];
		$expectedFilters = [];
		$field = 'a';

		foreach ($operators as $operator)
		{
			$value = 'some value';
			$request->where($field, $operator, $value);

			$expectedFilters[] = ['field' => $field, 'operator' => $operator, 'value' => $value, 'boolean' => 'and'];

			$field++;
		}

		$this->checkRequest($request, $this->buildExpectedFilterSet($expectedFilters));
	}

	/**
	 * @test
	 */
	public function simpleOr()
	{
		$request = new SearchRequest;

		$request->where('something', false)->orWhere('somethingElse', '>=', 56.24);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'something', 'operator' => '=', 'value' => false, 'boolean' => 'and'],
			['field' => 'somethingElse', 'operator' => '>=', 'value' => 56.24, 'boolean' => 'or'],
		]));
	}

	/**
	 * @test
	 */
	public function nestedOr()
	{
		$request = new SearchRequest;

		$request->where('something', false)->where(function(FilterSet $filterSet)
		{
			$filterSet->where('innerThing', '<', -45)->orWhere('secondInnerThing', 'potatoes');
		});

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'something', 'operator' => '=', 'value' => false, 'boolean' => 'and'],
			$this->buildExpectedFilterSet([
				['field' => 'innerThing', 'operator' => '<', 'value' => -45, 'boolean' => 'and'],
				['field' => 'secondInnerThing', 'operator' => '=', 'value' => 'potatoes', 'boolean' => 'or'],
			]),
		]));
	}

	/**
	 * @test
	 */
	public function deepNested()
	{
		$request = new SearchRequest;

		$request->where('something', true)->where(function(FilterSet $filterSet)
		{
			$filterSet->where('innerThing', 43.23)->orWhereIn('secondInnerThing', ['burlap', 'sacks'])->orWhere(function(FilterSet $filterSet)
			{
				$filterSet->where('france', 'large')->where('bananas', 'small');
			});
		});

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'something', 'operator' => '=', 'value' => true, 'boolean' => 'and'],
			$this->buildExpectedFilterSet([
				['field' => 'innerThing', 'operator' => '=', 'value' => 43.23, 'boolean' => 'and'],
				['field' => 'secondInnerThing', 'operator' => 'in', 'value' => ['burlap', 'sacks'], 'boolean' => 'or'],
				$this->buildExpectedFilterSet([
					['field' => 'france', 'operator' => '=', 'value' => 'large', 'boolean' => 'and'],
					['field' => 'bananas', 'operator' => '=', 'value' => 'small', 'boolean' => 'and'],
				], 'or'),
			]),
		]));
	}

	/**
	 * @test
	 */
	public function allIns()
	{
		$request = new SearchRequest;

		$request->whereIn('first', [1, 2, 3])
		        ->whereNotIn('second', ['four', 'five', 'six'])
		        ->orWhereIn('third', [7, 8, 9])
		        ->orWhereNotIn('fourth', ['ten', 'eleven', 'twelve']);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => 'in', 'value' => [1, 2, 3], 'boolean' => 'and'],
			['field' => 'second', 'operator' => 'not in', 'value' => ['four', 'five', 'six'], 'boolean' => 'and'],
			['field' => 'third', 'operator' => 'in', 'value' => [7, 8, 9], 'boolean' => 'or'],
			['field' => 'fourth', 'operator' => 'not in', 'value' => ['ten', 'eleven', 'twelve'], 'boolean' => 'or'],
		]));
	}

	/**
	 * @test
	 */
	public function allBetweens()
	{
		$request = new SearchRequest;

		$request->whereBetween('first', [1, 2])
		        ->whereNotBetween('second', [3, 4])
		        ->orWhereBetween('third', [5, 6])
		        ->orWhereNotBetween('fourth', [7, 8]);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => 'between', 'value' => [1, 2], 'boolean' => 'and'],
			['field' => 'second', 'operator' => 'not between', 'value' => [3, 4], 'boolean' => 'and'],
			['field' => 'third', 'operator' => 'between', 'value' => [5, 6], 'boolean' => 'or'],
			['field' => 'fourth', 'operator' => 'not between', 'value' => [7, 8], 'boolean' => 'or'],
		]));
	}

	/**
	 * @test
	 */
	public function allExists()
	{
		$request = new SearchRequest;

		$request->whereExists('first')->whereNotExists('second')->orWhereExists('third')->orWhereNotExists('fourth');

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => 'exists', 'value' => null, 'boolean' => 'and'],
			['field' => 'second', 'operator' => 'not exists', 'value' => null, 'boolean' => 'and'],
			['field' => 'third', 'operator' => 'exists', 'value' => null, 'boolean' => 'or'],
			['field' => 'fourth', 'operator' => 'not exists', 'value' => null, 'boolean' => 'or'],
		]));
	}

	/**
	 * Checks the filter set for the provided request against the given expected filter set
	 *
	 * @param  \Monger\SearchRequest\SearchRequest   $request
	 * @param  array                                 $expectedFilterSet
	 */
	protected function checkRequest(SearchRequest $request, array $expectedFilterSet)
	{
		$this->checkFilterSet($request->getFilters(), $expectedFilterSet);
	}

	/**
	 * Checks that the provided filter set matches the expectations
	 *
	 * @param  \Monger\SearchRequest\FilterSet   $filterSet
	 * @param  array                             $expectedFilterSet
	 */
	protected function checkFilterSet(FilterSet $filterSet, array $expectedFilterSet)
	{
		$expectedFilters = $expectedFilterSet['filters'];

		$this->assertEquals(count($expectedFilters), count($filterSet));
		$this->assertEquals($expectedFilterSet['boolean'], $filterSet->getBoolean());

		foreach ($expectedFilters as $i => $expectedFilter)
		{
			$filter = $filterSet[$i];
			$checkMethod = isset($expectedFilter['filters']) ? 'checkFilterSet' : 'checkFilter';

			$this->$checkMethod($filter, $expectedFilter);
		}
	}

	/**
	 * Checks the provided filter instance against the expected filter
	 *
	 * @param  \Monger\SearchRequest\Filter    $filter
	 * @param  array                           $expectedFilter
	 */
	protected function checkFilter(Filter $filter, array $expectedFilter)
	{
		$this->assertEquals($expectedFilter['field'], $filter->getField());
		$this->assertEquals($expectedFilter['operator'], $filter->getOperator());
		$this->assertEquals($expectedFilter['value'], $filter->getValue());
		$this->assertEquals($expectedFilter['boolean'], $filter->getBoolean());

		$this->assertEquals($expectedFilter['boolean'] === 'and', $filter->isAnd());
		$this->assertEquals($expectedFilter['boolean'] === 'or', $filter->isOr());
	}

	/**
	 * Builds an expected filter set given the provided list of expected filters
	 *
	 * @param  array    $expectedFilters
	 * @param  string   $boolean
	 *
	 * @return array
	 */
	protected function buildExpectedFilterSet(array $expectedFilters, $boolean = 'and')
	{
		return [
			'boolean' => $boolean,
			'filters' => $expectedFilters
		];
	}

}