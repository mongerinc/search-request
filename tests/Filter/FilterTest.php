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

		$this->assertEquals(0, count($request->getFilterSet()));
		$this->assertEquals('and', $request->getFilterSet()->getBoolean());
		$this->assertTrue($request->getFilterSet()->isAnd());
		$this->assertFalse($request->getFilterSet()->isOr());
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
	public function multipleSameField()
	{
		$request = new SearchRequest;

		$request->where('someField', false)->where('someField', '>=', 40.24);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'someField', 'operator' => '=', 'value' => false, 'boolean' => 'and'],
			['field' => 'someField', 'operator' => '>=', 'value' => 40.24, 'boolean' => 'and'],
		]));
	}

	/**
	 * @test
	 */
	public function operators()
	{
		$request = new SearchRequest;
		$operators = ['=', '>', '>=', '<', '<=', '!=', 'in', 'not in', 'like', 'not like', 'exists', 'not exists', 'between', 'not between', 'regex', 'not regex'];
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
	 * @test
	 */
	public function allLikes()
	{
		$request = new SearchRequest;

		$request->whereLike('first', 'foo')
		        ->whereNotLike('second', '%moo')
		        ->orWhereLike('third', 'goo%')
		        ->orWhereNotLike('fourth', '%spookyboo%');

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => 'like', 'value' => 'foo', 'boolean' => 'and'],
			['field' => 'second', 'operator' => 'not like', 'value' => '%moo', 'boolean' => 'and'],
			['field' => 'third', 'operator' => 'like', 'value' => 'goo%', 'boolean' => 'or'],
			['field' => 'fourth', 'operator' => 'not like', 'value' => '%spookyboo%', 'boolean' => 'or'],
		]));
	}

	/**
	 * @test
	 */
	public function allRegex()
	{
		$request = new SearchRequest;

		$request->whereRegex('first', 'foo')
		        ->whereNotRegex('second', '.*')
		        ->orWhereRegex('third', 'foo.{45}?')
		        ->orWhereNotRegex('fourth', 'whatever');

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => 'regex', 'value' => 'foo', 'boolean' => 'and'],
			['field' => 'second', 'operator' => 'not regex', 'value' => '.*', 'boolean' => 'and'],
			['field' => 'third', 'operator' => 'regex', 'value' => 'foo.{45}?', 'boolean' => 'or'],
			['field' => 'fourth', 'operator' => 'not regex', 'value' => 'whatever', 'boolean' => 'or'],
		]));
	}

	/**
	 * @test
	 */
	public function removeExisting()
	{
		$request = new SearchRequest;

		$request->where('first', 'foo')
		        ->where('second', '<', 100)
		        ->where('second', '>', 50)
		        ->where('third', false)
		        ->where('fourth', true);

		$request->removeFilters('second');

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => '=', 'value' => 'foo', 'boolean' => 'and'],
			['field' => 'third', 'operator' => '=', 'value' => false, 'boolean' => 'and'],
			['field' => 'fourth', 'operator' => '=', 'value' => true, 'boolean' => 'and'],
		]));

		$request->removeFilters(['third', 'fourth']);

		$this->checkRequest($request, $this->buildExpectedFilterSet([
			['field' => 'first', 'operator' => '=', 'value' => 'foo', 'boolean' => 'and'],
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
		$this->checkFilterSet($request->getFilterSet(), $expectedFilterSet);

		//verify that the first-set top-level filter values match what can be pulled off the request itself
		$expectedFirstFilterValues = $this->getExpectedFirstFilterValues($expectedFilterSet['filters']);

		foreach ($expectedFirstFilterValues as $key => $value)
		{
			$this->assertEquals($value, $request->getFilterValue($key));
			$this->assertEquals($value, $request->getFilter($key)->getValue());
		}
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
		$this->assertEquals($expectedFilterSet, $filterSet->toArray());

		$this->checkFilters($filterSet, $expectedFilters);
	}

	/**
	 * Checks the filters against the expected filters for the provided filter set
	 *
	 * @param  \Monger\SearchRequest\FilterSet   $filterSet
	 * @param  array                             $expectedFilters
	 */
	protected function checkFilters(FilterSet $filterSet, array $expectedFilters)
	{
		//iterate over the expected filters and check each one independently
		foreach ($expectedFilters as $i => $expectedFilter)
		{
			$filter = $filterSet[$i];
			$checkMethod = isset($expectedFilter['filters']) ? 'checkFilterSet' : 'checkFilter';

			$this->$checkMethod($filter, $expectedFilter);
		}

		//verify that the first-set top-level filter values match
		$expectedFirstFilterValues = $this->getExpectedFirstFilterValues($expectedFilters);

		foreach ($expectedFirstFilterValues as $key => $value)
		{
			$this->assertEquals($value, $filterSet->getFilterValue($key));
			$this->assertEquals($value, $filterSet->getFilter($key)->getValue());
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
	 * Builds a key=>value array of the top-level filters in the given set of expected filters (choosing only the first instance of each field name)
	 *
	 * @param  array   $expectedFilters
	 *
	 * @return array
	 */
	protected function getExpectedFirstFilterValues(array $expectedFilters)
	{
		$fieldValues = [];

		foreach ($expectedFilters as $expectedFilter)
		{
			if (!isset($expectedFilter['filters']) && !isset($fieldValues[$expectedFilter['field']]))
			{
				$fieldValues[$expectedFilter['field']] = $expectedFilter['value'];
			}
		}

		return $fieldValues;
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