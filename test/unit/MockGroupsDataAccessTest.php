<?php

namespace Opf\Test\Unit;

use Illuminate\Database\Capsule\Manager;
use Opf\DataAccess\Groups\MockGroupDataAccess;
use PHPUnit\Framework\TestCase;
use SQLite3;

final class MockGroupsDataAccessTest extends TestCase
{
    /** @var SQLite3 */
    protected $database = null;

    /** @var array */
    protected $dbSettings = null;

    /** @var Illuminate\Database\Capsule\Manager */
    protected $capsule = null;

    /** @var Opf\Models\CoreGroup */
    protected $mockGroupDataAccess = null;

    public function setUp(): void
    {
        $this->database = new SQLite3(__DIR__ . '/../resources/test-scim-opf.sqlite');

        $groupDbSql = "CREATE TABLE IF NOT EXISTS groups (
            id varchar(160) NOT NULL UNIQUE,
            displayName varchar(160) NOT NULL DEFAULT '',
            members TEXT NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL
        )";

        $this->database->exec($groupDbSql);

        $createGroupSql = "INSERT INTO groups (
            id,
            displayName,
            members
            ) VALUES (
                '12345678-9012-3456-7890-12345679',
                'testGroup',
                '12345678-9012-3456-7890-12345678'
        )";

        $this->database->exec($createGroupSql);

        $this->dbSettings = [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../resources/test-scim-opf.sqlite',
            'prefix' => ''
        ];

        $this->capsule = new Manager();
        $this->capsule->addConnection($this->dbSettings);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        $this->mockGroupDataAccess = new MockGroupDataAccess();
    }

    public function tearDown(): void
    {
        $this->mockGroupDataAccess = null;
        $this->capsule = null;
        $this->dbSettings = null;
        $this->database->exec("DROP TABLE groups");
        $this->database = null;

        unlink(__DIR__ . '/../resources/test-scim-opf.sqlite');
    }

    public function testReadAllGroups()
    {
        $this->assertNotEmpty($this->mockGroupDataAccess->all());
    }

    public function testCreateGroup()
    {
        $testGroupJson = json_decode(file_get_contents(__DIR__ . '/../resources/testGroup.json'), true);
        $this->mockGroupDataAccess->fromSCIM($testGroupJson);
        $groupCreateRes = $this->mockGroupDataAccess->save();
        $this->assertTrue($groupCreateRes);
    }
}
