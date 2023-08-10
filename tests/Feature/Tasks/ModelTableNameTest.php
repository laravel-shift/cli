<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Facades\Reflector;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\ModelTableName;

/**
 * @see \Shift\Cli\Tasks\ModelTableName
 */
class ModelTableNameTest extends TestCase
{
    use InteractsWithProject;

    private ModelTableName $subject;

    private function mockReflectionClass(string $name, string $table, bool $pivot = false)
    {
        $mock = \Mockery::mock(\ReflectionClass::class);
        $mock->expects('isSubclassOf')
            ->with('Illuminate\\Database\\Eloquent\\Model')
            ->andReturn(true);
        $mock->expects('getDefaultProperties')
            ->andReturn(['table' => $table]);
        $mock->expects('isSubclassOf')
            ->with('Illuminate\\Database\\Eloquent\\Relations\\Pivot')
            ->andReturn($pivot);
        $mock->expects('getShortName')
            ->withNoArgs()
            ->andReturn($name);
        $mock->expects('getProperty->getDefaultValue')
            ->andReturn($table);

        return $mock;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ModelTableName();
    }

    #[Test]
    public function it_does_nothing_when_nothing_is_found()
    {
        $this->fakeProject([
            'app/Support/NoOp.php' => '<?php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOp.php');
    }

    #[Test]
    public function it_replaces_arguments_with_explicit_methods()
    {
        $this->fakeProject([
            'app/Models/User.php' => 'tests/fixtures/table-name/model.php',
            'app/Models/RoleUser.php' => 'tests/fixtures/table-name/pivot.php',
        ]);

        $reflector = \Mockery::mock('Reflector');
        $reflector->expects('classFromPath')
            ->with($this->currentSnapshotPath() . '/app/Models/User.php')
            ->andReturn($this->mockReflectionClass('User', 'users'));
        $reflector->expects('classFromPath')
            ->with($this->currentSnapshotPath() . '/app/Models/RoleUser.php')
            ->andReturn($this->mockReflectionClass('RoleUser', 'role_user', true));
        Reflector::swap($reflector);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/table-name/model.after.php', 'app/Models/User.php');
        $this->assertFileChanges('tests/fixtures/table-name/pivot.after.php', 'app/Models/RoleUser.php');
    }
}
