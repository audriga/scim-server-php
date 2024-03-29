<?php

namespace Opf\Test\Unit;

use Opf\Models\SCIM\Standard\Filters\AttributeExpression;
use Opf\Models\SCIM\Standard\Filters\AttributeOperator;
use Opf\Models\SCIM\Standard\Filters\FilterException;
use Opf\Models\SCIM\Standard\Filters\FilterExpression;
use Opf\Util\Filters\FilterParser;
use PHPUnit\Framework\TestCase;

final class FilterParserTest extends TestCase
{
    public function testParseAttributeFilterExpression()
    {
        // Test an "eq" filter expression
        $filterString = "userName eq sometestusername";
        $attributeFilterExpression = FilterParser::parseFilterExpression($filterString);

        $this->assertInstanceOf(FilterExpression::class, $attributeFilterExpression);
        $this->assertInstanceOf(AttributeExpression::class, $attributeFilterExpression);

        $this->assertEquals("userName", $attributeFilterExpression->getAttributePath());
        $this->assertEquals(AttributeOperator::OP_EQ, $attributeFilterExpression->getCompareOperator());
        $this->assertEquals("sometestusername", $attributeFilterExpression->getComparisonValue());


        // Test a "pr" filter expression
        $filterString = "meta.created pr";
        $attributeFilterExpression = FilterParser::parseFilterExpression($filterString);

        $this->assertInstanceOf(FilterExpression::class, $attributeFilterExpression);
        $this->assertInstanceOf(AttributeExpression::class, $attributeFilterExpression);

        $this->assertEquals("meta.created", $attributeFilterExpression->getAttributePath());
        $this->assertEquals(AttributeOperator::OP_PR, $attributeFilterExpression->getCompareOperator());
        $this->assertNull($attributeFilterExpression->getComparisonValue());
    }

    public function testParseTooShortFilterExpression()
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage("Incorrectly formatted AttributeExpression");

        $filterString = "somestring";
        $parsedFilterExpression = FilterParser::parseFilterExpression($filterString);
    }

    public function testFilterExpressionWithSpacesInValue()
    {
        $filterString = "userName eq \"some value\"";
        $parsedFilterExpression = FilterParser::parseFilterExpression($filterString);

        $this->assertInstanceOf(FilterExpression::class, $parsedFilterExpression);
        $this->assertInstanceOf(AttributeExpression::class, $parsedFilterExpression);

        $this->assertEquals("userName", $parsedFilterExpression->getAttributePath());
        $this->assertEquals(AttributeOperator::OP_EQ, $parsedFilterExpression->getCompareOperator());
        $this->assertEquals("\"some value\"", $parsedFilterExpression->getComparisonValue());
    }

    public function testParseIncorrectExpression()
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage("Invalid AttributeOperation passed to AttributeExpression");

        $filterString = "userName blabla \"some moreblabla\"";
        $parsedFilterExpression = FilterParser::parseFilterExpression($filterString);
    }
}
