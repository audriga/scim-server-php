<?php

namespace Opf\Test\Unit;

use Illuminate\Database\Capsule\Manager;
use Opf\Adapters\Groups\MockGroupAdapter;
use Opf\DataAccess\Groups\MockGroupDataAccess;
use Opf\Models\SCIM\Standard\Groups\CoreGroup;
use Opf\Util\Util;
use PHPUnit\Framework\TestCase;
use SQLite3;

final class MockGroupsDataAccessTest extends TestCase
{
    /** @var Opf\Models\SCIM\Standard\Groups\CoreGroup */
    protected $coreGroup = null;

    /** @var Opf\DataAccess\Groups\MockGroupDataAccess */
    protected $mockGroupDataAccess = null;

    /** @var Opf\Opf\Adapters\Groups\MockGroupAdapter */
    protected $mockGroupAdapter = null;

    public function setUp(): void
    {
        Util::setConfigFile(__DIR__ . '/../resources/mock-test-config.php');
        $this->coreGroup = new CoreGroup();
        $this->mockGroupAdapter = new MockGroupAdapter();
        $this->mockGroupDataAccess = new MockGroupDataAccess();
    }

    public function tearDown(): void
    {
        $this->coreGroup = null;
        $this->mockGroupAdapter = null;
        $this->mockGroupDataAccess = null;
    }

    public function testCreateGroup()
    {
        $testGroupJson = json_decode(file_get_contents(__DIR__ . '/../resources/testGroup.json'), true);
        $this->coreGroup->fromSCIM($testGroupJson);
        $mockGroup = $this->mockGroupAdapter->getMockGroup($this->coreGroup);
        $groupCreateRes = $this->mockGroupDataAccess->create($mockGroup);
        $this->assertNotNull($groupCreateRes);
    }

    public function testReadAllGroups()
    {
        $this->assertNotEmpty($this->mockGroupDataAccess->getAll());
    }
}
