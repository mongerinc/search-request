<?php namespace Monger\SearchRequest\Tests\FieldSubstitution;

use Monger\SearchRequest\SearchRequest;

class InvalidationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Monger\SearchRequest\SearchRequest
	 */
	protected $request;

	/**
	 * Set up before each test
	 */
	public function setup()
	{
		$this->request = new SearchRequest;
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function originalNull()
	{
		$this->request->substituteField(null, 'someField');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function originalArray()
	{
		$this->request->substituteField(['not a string'], 'someField');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function originalNumber()
	{
		$this->request->substituteField(56, 'someField');
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function substitutionNull()
	{
		$this->request->substituteField('someField', null);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function substitutionArray()
	{
		$this->request->substituteField('someField', ['asc']);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function substitutionNumber()
	{
		$this->request->substituteField('someField', -24);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function multipleMissingOriginal()
	{
		$this->request->substituteFields([
			'someField',
		]);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function multipleNullSubstitution()
	{
		$this->request->substituteFields([
			'someField' => null,
		]);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function multipleNumberSubstitution()
	{
		$this->request->substituteFields([
			'someField' => 5,
		]);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function multipleArraySubstitution()
	{
		$this->request->substituteFields([
			'someField' => ['not a string'],
		]);
	}

	/**
	 * @test
	 * @expectedException TypeError
	 */
	public function multipleNotArray()
	{
		$this->request->substituteFields(null);
	}

}