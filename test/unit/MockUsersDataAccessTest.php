<?php

namespace Opf\Test\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager;
use Opf\Adapters\Users\MockUserAdapter;
use Opf\DataAccess\Users\MockUserDataAccess;
use Opf\Models\SCIM\Standard\Users\CoreUser;
use Opf\Util\Util;
use SQLite3;

final class MockUsersDataAccessTest extends TestCase
{
    /** @var Opf\Models\SCIM\Standard\Users\CoreUser */
    protected $coreUser = null;

    /** @var Opf\DataAccess\Users\MockUserDataAccess */
    protected $mockUserDataAccess = null;

    /** @var Opf\Adapters\Users\MockUserAdapter */
    protected $mockUserAdapter = null;

    public function setUp(): void
    {
        Util::setConfigFile(__DIR__ . '/../resources/mock-test-config.php');
        $this->coreUser = new CoreUser();
        $this->mockUserDataAccess = new MockUserDataAccess();
        $this->mockUserAdapter = new MockUserAdapter();
    }

    public function tearDown(): void
    {
        $this->coreUser = null;
        $this->mockUserDataAccess = null;
        $this->mockUserAdapter = null;
    }

    public function testCreateUser()
    {
        $testUserJson = json_decode(file_get_contents(__DIR__ . '/../resources/testUser.json'), true);
        $this->coreUser->fromSCIM($testUserJson);
        $mockUser = $this->mockUserAdapter->getMockUser($this->coreUser);
        $userCreateRes = $this->mockUserDataAccess->create($mockUser);
        $this->assertNotNull($userCreateRes);
    }

    public function testReadAllUsers()
    {
        $this->assertNotEmpty($this->mockUserDataAccess->getAll());
    }
}
