<?php

namespace Opf\Test\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager;
use Opf\DataAccess\Users\MockUserDataAccess;
use SQLite3;

final class MockUsersDataAccessTest extends TestCase
{
    /** @var SQLite3 */
    protected $database = null;

    /** @var array */
    protected $dbSettings = null;

    /** @var Illuminate\Database\Capsule\Manager */
    protected $capsule = null;

    /** @var Opf\Models\MockUser */
    protected $mockUserDataAccess = null;

    public function setUp(): void
    {
        $this->database = new SQLite3(__DIR__ . '/../resources/test-scim-opf.sqlite');

        $userDbSql = "CREATE TABLE IF NOT EXISTS users (
            id varchar(160) NOT NULL UNIQUE,
            userName varchar(160) NOT NULL,
            active BOOLEAN NOT NULL DEFAULT 1,
            externalId varchar(160) NULL,
            profileUrl varchar(160) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL
        )";

        $this->database->exec($userDbSql);

        $createUserSql = "INSERT INTO users (
            id,
            userName,
            externalId,
            profileUrl
            ) VALUES (
            '12345678-9012-3456-7890-12345678',
            'testuser',
            'testuserexternal',
            'https://example.com/testuser'
        )";

        $this->database->exec($createUserSql);

        $this->dbSettings = [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../resources/test-scim-opf.sqlite',
            'prefix' => ''
        ];

        $this->capsule = new Manager();
        $this->capsule->addConnection($this->dbSettings);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        $this->mockUserDataAccess = new MockUserDataAccess();
    }

    public function tearDown(): void
    {
        $this->mockUserDataAccess = null;
        $this->capsule = null;
        $this->dbSettings = null;
        $this->database->exec("DROP TABLE users");
        $this->database = null;

        unlink(__DIR__ . '/../resources/test-scim-opf.sqlite');
    }

    public function testReadAllUsers()
    {
        $this->assertNotEmpty($this->mockUserDataAccess->all());
    }

    public function testCreateUser()
    {
        $testUserJson = json_decode(file_get_contents(__DIR__ . '/../resources/testUser.json'), true);
        $this->mockUserDataAccess->fromSCIM($testUserJson);
        $userCreateRes = $this->mockUserDataAccess->save();
        $this->assertTrue($userCreateRes);
    }
}
