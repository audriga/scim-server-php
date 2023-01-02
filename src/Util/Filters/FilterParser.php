<?php

namespace Opf\Util\Filters;

use Attribute;
use Opf\Models\SCIM\Standard\Filters\AttributeExpression;
use Opf\Models\SCIM\Standard\Filters\FilterException;
use Opf\Models\SCIM\Standard\Filters\FilterExpression;

/**
 * A parser for SCIM filter expressions
 *
 * Note: currently this parser is very simplistic and directly tries to parse a string, representing a filter expession.
 * For now only attribute expression are supported: see https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
 * In later iterations, it could be changed, such that it uses a lexer for lexical analysis first and then performs
 * syntactic analysis, based on the lexer's output.
 */
class FilterParser
{
    public static function parseFilterExpression(string $filterExpression): FilterExpression
    {
        if (!isset($filterExpression) || empty($filterExpression) || !is_string($filterExpression)) {
            throw new FilterException(
                "Invalid filter expression passed for parsing: expression was either null, empty or not a string"
            );
        }

        $splitAttributePathFromFilterExpression = explode(" ", $filterExpression, 2);

        if (!isset($splitAttributePathFromFilterExpression[1]) || empty($splitAttributePathFromFilterExpression[1])) {
            throw new FilterException("Incorrectly formatted AttributeExpression");
        } else if (strcmp($splitAttributePathFromFilterExpression[1], "pr") === 0) {
            $attributeExpression = new AttributeExpression(
                $splitAttributePathFromFilterExpression[0], // The attribute path
                "pr",                                       // The comparison operator (which must be "pr" in this case)
                null                                        // The comparison value (must be null due to "pr")
            );
        } else {
            $splitFilterExpressionWithoutAttributePath = explode(" ", $splitAttributePathFromFilterExpression[1], 2);
            $attributeExpression = new AttributeExpression(
                $splitAttributePathFromFilterExpression[0],    // The attribute path
                $splitFilterExpressionWithoutAttributePath[0], // The comparison operator (which is different from "pr")
                $splitFilterExpressionWithoutAttributePath[1]  // The comparison value (can contain spaces)
            );
        }


        // if (count($splitFilterExpression) < 2 || count($splitFilterExpression) > 3) {
        //     throw new FilterException("Incorrectly formatted AttributeExpression");
        // }

        // $attributeExpression = new AttributeExpression(
        //     $splitFilterExpression[0],
        //     $splitFilterExpression[1],
        //     $splitFilterExpression[2]
        // );

        return $attributeExpression;
    }
}
