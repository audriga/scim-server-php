<?php

namespace Opf\Test\Unit;

use Opf\Models\SCIM\Standard\Filters\FilterException;
use Opf\Util\Filters\FilterParser;
use Opf\Util\Filters\FilterUtil;
use PHPUnit\Framework\TestCase;

final class FilterUtilTest extends TestCase
{
    /** @var array */
    protected $scimGroups = [];

    /** @var array */
    protected $scimUsers = [];

    public function setUp(): void
    {
        $this->scimGroups = json_decode(file_get_contents(__DIR__ . '/../resources/filterTestGroups.json'), true);
        $this->scimUsers = json_decode(file_get_contents(__DIR__ . '/../resources/filterTestUsers.json'), true);
    }

    public function tearDown(): void
    {
        $this->scimGroups = [];
        $this->scimUsers = [];
    }

    public function testGroupFiltering()
    {
        // "ne" filter test
        $filterString = "displayName ne testGroup";
        $filteredScimGroups = FilterUtil::performFiltering($filterString, $this->scimGroups);

        $this->assertEquals(array_splice($this->scimGroups, 1, 2), $filteredScimGroups);
    }

    public function testUserFiltering()
    {
        // "sw" filter test
        $filterString = "userName sw testuser";
        $filteredScimUsers = FilterUtil::performFiltering($filterString, $this->scimUsers);

        $this->assertEquals(array_splice($this->scimUsers, 0, 3), $filteredScimUsers);
    }

    public function testInvalidFiltering()
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage("Invalid AttributeOperation passed to AttributeExpression");
        
        $filterString = "externalId bla some value";
        $filteredScimUsers = FilterUtil::performFiltering($filterString, $this->scimUsers);
    }

    public function testFilteringWithSpaces()
    {
        $filterString = "userName eq some user";
        $filteredScimUsers = FilterUtil::performFiltering($filterString, $this->scimUsers);
        
        $this->assertEquals(array($this->scimUsers[3]), $filteredScimUsers);
    }

    public function testIncorrectPRFilterExpression()
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage("\"pr\" filter operator must be used without a comparison value");

        $filterString = "userName pr \"some blabla\"";
        $parsedFilterExpression = FilterUtil::performFiltering($filterString, $this->scimUsers);
    }
}
