<?php

namespace Opf\Models\SCIM\Standard\Filters;

class AttributeExpression extends FilterExpression
{
    /** @var string $attributePath */
    private $attributePath;

    /** @var \Opf\Models\SCIM\Standard\Filters\AttributeOperator $compareOperator */
    private $compareOperator;

    /** @var bool|string|int|null $comparisonValue */
    private $comparisonValue;

    public function __construct($attributePath, $compareOperator, $comparisonValue)
    {
        if (isset($attributePath) && !empty($attributePath) && is_string($attributePath)) {
            $this->attributePath = $attributePath;
        } else {
            throw new FilterException(
                "Attribute path passed to Attribute Expression was either empty, null or not a string"
            );
        }

        switch ($compareOperator) {
            case "eq":
                $this->compareOperator = AttributeOperator::OP_EQ;
                break;

            case "ne":
                $this->compareOperator = AttributeOperator::OP_NE;
                break;

            case "co":
                $this->compareOperator = AttributeOperator::OP_CO;
                break;

            case "sw":
                $this->compareOperator = AttributeOperator::OP_SW;
                break;

            case "ew":
                $this->compareOperator = AttributeOperator::OP_EW;
                break;

            case "gt":
                $this->compareOperator = AttributeOperator::OP_GT;
                break;

            case "lt":
                $this->compareOperator = AttributeOperator::OP_LT;
                break;

            case "ge":
                $this->compareOperator = AttributeOperator::OP_GE;
                break;

            case "le":
                $this->compareOperator = AttributeOperator::OP_LE;
                break;

            case "pr":
                $this->compareOperator = AttributeOperator::OP_PR;
                break;

            default:
                throw new FilterException("Invalid AttributeOperation passed to AttributeExpression");
                break;
        }

        $this->comparisonValue = $comparisonValue;
    }

    public function getAttributePath()
    {
        return $this->attributePath;
    }

    public function getCompareOperator()
    {
        return $this->compareOperator;
    }

    public function getComparisonValue()
    {
        return $this->comparisonValue;
    }
}
