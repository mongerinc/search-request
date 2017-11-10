<?php namespace My\App\Database\Query\SearchRequest\Illuminate\Filter;

use Monger\SearchRequest\Filter;

class TypeFactory {

	protected $typeClasses = [
		Types\In::class,
		Types\Exists::class,
		Types\Between::class,
		Types\Comparison::class,
	];

	public function build() : array
	{
		$types = [];

		foreach ($this->typeClasses as $typeClass)
		{
			$types[] = new $typeClass;
		}

		return $types;
	}

	public function buildFromFilter(Filter $filter) : Types\Type
	{
		foreach ($this->build() as $type)
		{
			if ($type->shouldBeApplied($filter))
			{
				return $type;
			}
		}
	}

}