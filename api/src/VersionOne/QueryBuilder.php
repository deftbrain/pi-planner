<?php

namespace App\VersionOne;

/**
 * Implements the Builder design pattern for a VersionOne Bulk API query.
 *
 * @link https://refactoring.guru/design-patterns/builder Builder design pattern explanation
 * @link https://versionone.github.io/api-docs/#bulk VersionOne Bulk API documentation
 */
class QueryBuilder
{
    public const OPERATOR_EQUAL = '=';
    public const OPERATOR_NOT_EQUAL = '!=';
    public const OPERATOR_LESS = '<';
    public const OPERATOR_LESS_OR_EQUAL = '<=';
    public const OPERATOR_GREATER = '>';
    public const OPERATOR_GREATER_OR_EQUAL = '>=';

    /**
     * @var array The operators must be sorted by length DESC.
     */
    private const OPERATORS = [
        self::OPERATOR_EQUAL,
        self::OPERATOR_NOT_EQUAL,
        self::OPERATOR_LESS,
        self::OPERATOR_LESS_OR_EQUAL,
        self::OPERATOR_GREATER,
        self::OPERATOR_GREATER_OR_EQUAL,
    ];

    /**
     * @var array
     */
    private const PROPERTIES_WITHOUT_CONVERSION = ['from', 'select', 'update'];

    /**
     * @var array|null
     */
    private $filter;

    /**
     * @var string|null
     */
    private $from;

    /**
     * @var string|null
     */
    private $select;

    /**
     * @var array
     */
    private $update;

    public function getQuery(): array
    {
        $query = [];

        foreach (self::PROPERTIES_WITHOUT_CONVERSION as $propertyName) {
            if ($this->$propertyName) {
                $query[$propertyName] = $this->$propertyName;
            }
        }

        if ($this->filter) {
            $filter = [];
            foreach ($this->filter as $attribute => $value) {
                [$attribute, $operator] = $this->parseNameAndOperator($attribute);
                $value = array_map(function ($v) { return "'$v'";}, (array) $value);
                $value = implode(',', $value);
                $filter[] = sprintf('%s%s%s', $attribute, $operator, $value);
            }
            $query['filter'] = $filter;
        }

        return $query;
    }

    public function filter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function from(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function select(array $select): self
    {
        $this->select = array_values($select);
        return $this;
    }

    public function update(array $update): self
    {
        $this->update = $update;
        return $this;
    }

    private function parseNameAndOperator(string $attribute): array
    {
        foreach (self::OPERATORS as $operator) {
            $operatorLength = strlen($operator);
            if ($operator === substr($attribute, -$operatorLength)) {
                $attribute = rtrim(substr($attribute, 0, -$operatorLength));
                return [$attribute, $operator];
            }
        }

        return [$attribute, self::OPERATOR_EQUAL];
    }
}
