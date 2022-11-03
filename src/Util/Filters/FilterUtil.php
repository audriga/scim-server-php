<?php

namespace Opf\Util\Filters;

use Exception;
use Opf\Models\SCIM\Standard\Filters\AttributeExpression;
use Opf\Models\SCIM\Standard\Filters\AttributeOperator;
use Opf\Models\SCIM\Standard\Filters\FilterException;

class FilterUtil
{
    /**
     * @param string $filterExpression The SCIM filter expression that is to be parsed and applied
     * @param array $scimData An array of SCIM objects (each represented as an array) that is to be filtered
     *
     * @return array Array of filtered SCIM objects (again each represented as an array)
     */
    public static function performFiltering(string $filterExpression, array $scimData): array
    {

        try {
            $parsedFilterExpression = FilterParser::parseFilterExpression($filterExpression);
        } catch (Exception $e) {
            throw $e;
        }

        $filteredScimData = [];

        // Call appropriate filtering method
        // For now, we only have a method for filtering, based on AttributeExpression
        switch (true) {
            case $parsedFilterExpression instanceof AttributeExpression:
                $filteredScimData = self::filterWithAttributeExpression($parsedFilterExpression, $scimData);
                break;

            default:
                throw new FilterException("Unknown filter expression type");
                break;
        }

        return $filteredScimData;
    }

    private static function filterWithAttributeExpression(
        AttributeExpression $attributeExpression,
        array $scimData
    ): array {
        // In case of null or an empty array, we return an empty array, since we have nothing to filter
        if (!isset($scimData) || empty($scimData)) {
            return [];
        }

        $filteredScimData = [];

        $attributePath = $attributeExpression->getAttributePath();
        $compareOperator = $attributeExpression->getCompareOperator();
        $comparisonValue = $attributeExpression->getComparisonValue();

        // If we have a nested attribute (i.e., an attribute with subattribute(s)),
        // then we split the attribute path by "." and store all the single parts
        // in an array
        // On the other hand (i.e., no nested attribute), we store the attribute path
        // in an array which only contains the attribute path as a single element
        if (strpos($attributePath, ".") !== false) {
            $attributePath = explode(".", $attributePath);
        } else {
            $attributePath = array($attributePath);
        }

        // Decide which filter function to call, based on the comparison operator
        $filterFunctionToUse = null;

        switch ($compareOperator) {
            case AttributeOperator::OP_EQ:
                $filterFunctionToUse = "stringEqualsFilter";
                break;

            case AttributeOperator::OP_NE:
                $filterFunctionToUse = "stringNotEqualsFilter";
                break;

            case AttributeOperator::OP_CO:
                $filterFunctionToUse = "stringContainsFilter";
                break;

            case AttributeOperator::OP_SW:
                $filterFunctionToUse = "stringStartsWithFilter";
                break;

            case AttributeOperator::OP_EW:
                $filterFunctionToUse = "stringEndsWithFilter";
                break;

            case AttributeOperator::OP_PR:
                if (isset($comparisonValue) && !empty($comparisonValue)) {
                    throw new FilterException("\"pr\" filter operator must be used without a comparison value");
                    break;
                } else {
                    $filterFunctionToUse = "hasValueFilter";
                    break;
                }

            case AttributeOperator::OP_GT:
                $filterFunctionToUse = "greaterThanFilter";
                break;

            case AttributeOperator::OP_GE:
                $filterFunctionToUse = "greaterThanOrEqualToFilter";
                break;

            case AttributeOperator::OP_LT:
                $filterFunctionToUse = "lessThanFilter";
                break;

            case AttributeOperator::OP_LE:
                $filterFunctionToUse = "lessThanOrEqualToFilter";
                break;

            default:
                throw new FilterException("Unknown comparison operator found");
                break;
        }

        // Put the function to call in a variable that we can use for calls below
        $filterFunctionToUse = array(FilterUtil::class, $filterFunctionToUse);

        foreach ($scimData as $scimObject) {
            // Obtain the attribute of the SCIM objects that we want to filter for
            $attribute = $scimObject;
            foreach ($attributePath as $attributePathComponent) {
                if (array_key_exists($attributePathComponent, $attribute)) {
                    $attribute = $attribute[$attributePathComponent];
                } else {
                    throw new FilterException(
                        "Attribute " . $attributePathComponent .
                        " to filter by is undefined for the given SCIM resources"
                    );
                }
            }

            $filterResult = false;
            // If the filter function to call is "hasValueFilter", we need to pass it only the attribute
            if (strcmp($filterFunctionToUse[1], "hasValueFilter") === 0) {
                $filterResult = $filterFunctionToUse($attribute);
            } else {
                $filterResult = $filterFunctionToUse($attribute, $comparisonValue);
            }

            if ($filterResult) {
                $filteredScimData[] = $scimObject;
            }
        }

        return $filteredScimData;
    }

    private static function stringEqualsFilter($attribute, $comparisonValue)
    {
        if (strcmp($attribute, $comparisonValue) === 0) {
            return true;
        }

        return false;
    }

    private static function stringNotEqualsFilter($attribute, $comparisonValue)
    {
        if (strcmp($attribute, $comparisonValue) !== 0) {
            return true;
        }

        return false;
    }

    private static function stringContainsFilter($attribute, $comparisonValue)
    {
        if (strpos($attribute, $comparisonValue) !== false) {
            return true;
        }

        return false;
    }

    private static function stringStartsWithFilter($attribute, $comparisonValue)
    {
        if (substr($attribute, 0, strlen($comparisonValue)) === $comparisonValue) {
            return true;
        }

        return false;
    }

    private static function stringEndsWithFilter($attribute, $comparisonValue)
    {
        if (substr($attribute, -strlen($comparisonValue)) === $comparisonValue) {
            return true;
        }

        return false;
    }

    private static function hasValueFilter($attribute)
    {
        if (isset($attribute) && !empty($attribute)) {
            return true;
        }

        return false;
    }

    private static function greaterThanFilter($attribute, $comparisonValue)
    {
        $comparisonValueType = gettype($comparisonValue);

        $attributeType = gettype($attribute);

        // First, make sure that the attribute and the comparison value have the same type
        if (strcmp($attributeType, $comparisonValueType) !== 0) {
            throw new FilterException(
                "\"gt\" filter operator requires the attribute and the comparison value to be of the same type"
            );
        }

        switch ($attributeType) {
            case "string":
                // Try to parse string to date to see if we need to compare timestamps
                if (
                    strtotime($attribute) !== false
                    && strtotime($comparisonValue) !== false
                ) {
                    if (strtotime($attribute) > strtotime($comparisonValue)) {
                        return true;
                    }
                } else { // If not date, but just regular string, then perform lexicographic comparison
                    if (strcasecmp($attribute, $comparisonValue) > 0) {
                        return true;
                    }
                }
                break;

            case "integer":
                if ($attribute > $comparisonValue) {
                    return true;
                }
                break;

            // For any other data type, throw an exception
            // TODO: Return 400 with "scimType" of "invalidFilter" as per
            // https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
            default:
                throw new FilterException("Unsupported type for \"gt\" operation");
                break;
        }

        return false;
    }

    private static function greaterThanOrEqualToFilter($attribute, $comparisonValue)
    {
        $comparisonValueType = gettype($comparisonValue);

        $attributeType = gettype($attribute);

        // First, make sure that the attribute and the comparison value have the same type
        if (strcmp($attributeType, $comparisonValueType) !== 0) {
            throw new FilterException(
                "\"ge\" filter operator requires the attribute and the comparison value to be of the same type"
            );
        }

        switch ($attributeType) {
            case "string":
                // Try to parse string to date to see if we need to compare timestamps
                if (
                    strtotime($attribute) !== false
                    && strtotime($comparisonValue) !== false
                ) {
                    if (strtotime($attribute) >= strtotime($comparisonValue)) {
                        return true;
                    }
                } else { // If not date, but just regular string, then perform lexicographic comparison
                    if (strcasecmp($attribute, $comparisonValue) >= 0) {
                        return true;
                    }
                }
                break;

            case "integer":
                if ($attribute >= $comparisonValue) {
                    return true;
                }
                break;

            // For any other data type, throw an exception
            // TODO: Return 400 with "scimType" of "invalidFilter" as per
            // https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
            default:
                throw new FilterException("Unsupported type for \"ge\" operation");
                break;
        }

        return false;
    }

    private static function lessThanFilter($attribute, $comparisonValue)
    {
        $comparisonValueType = gettype($comparisonValue);

        $attributeType = gettype($attribute);

        // First, make sure that the attribute and the comparison value have the same type
        if (strcmp($attributeType, $comparisonValueType) !== 0) {
            throw new FilterException(
                "\"lt\" filter operator requires the attribute and the comparison value to be of the same type"
            );
        }

        switch ($attributeType) {
            case "string":
                // Try to parse string to date to see if we need to compare timestamps
                if (
                    strtotime($attribute) !== false
                    && strtotime($comparisonValue) !== false
                ) {
                    if (strtotime($attribute) < strtotime($comparisonValue)) {
                        return true;
                    }
                } else { // If not date, but just regular string, then perform lexicographic comparison
                    if (strcasecmp($attribute, $comparisonValue) < 0) {
                        return true;
                    }
                }
                break;

            case "integer":
                if ($attribute < $comparisonValue) {
                    return true;
                }
                break;

            // For any other data type, throw an exception
            // TODO: Return 400 with "scimType" of "invalidFilter" as per
            // https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
            default:
                throw new FilterException("Unsupported type for \"lt\" operation");
                break;
        }

        return false;
    }

    private static function lessThanOrEqualToFilter($attribute, $comparisonValue)
    {
        $comparisonValueType = gettype($comparisonValue);

        $attributeType = gettype($attribute);

        // First, make sure that the attribute and the comparison value have the same type
        if (strcmp($attributeType, $comparisonValueType) !== 0) {
            throw new FilterException(
                "\"le\" filter operator requires the attribute and the comparison value to be of the same type"
            );
        }

        switch ($attributeType) {
            case "string":
                // Try to parse string to date to see if we need to compare timestamps
                if (
                    strtotime($attribute) !== false
                    && strtotime($comparisonValue) !== false
                ) {
                    if (strtotime($attribute) <= strtotime($comparisonValue)) {
                        return true;
                    }
                } else { // If not date, but just regular string, then perform lexicographic comparison
                    if (strcasecmp($attribute, $comparisonValue) <= 0) {
                        return true;
                    }
                }
                break;

            case "integer":
                if ($attribute <= $comparisonValue) {
                    return true;
                }
                break;

            // For any other data type, throw an exception
            // TODO: Return 400 with "scimType" of "invalidFilter" as per
            // https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
            default:
                throw new FilterException("Unsupported type for \"le\" operation");
                break;
        }

        return false;
    }
}
