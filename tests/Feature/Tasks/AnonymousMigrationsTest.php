<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\AnonymousMigrations;

/**
 * @see \Shift\Cli\Tasks\AnonymousMigrations
 */
class AnonymousMigrationsTest extends TestCase
{
    use InteractsWithProject;

    private AnonymousMigrations $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new AnonymousMigrations();
    }

    #[Test]
    public function it_does_nothing_when_no_matches_are_found()
    {
        $this->fakeProject([
            'database/migrations/2023_04_05_no_op.php' => '<?php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('database/migrations/2023_04_05_no_op.php');
    }

    #[Test]
    public function it_converts_migrations_and_stubs()
    {
        $this->fakeProject([
            'database/migrations/2014_10_12_000000_create_users_table.php' => 'tests/fixtures/anonymous-migrations/simple.php',
            'other/migrations/2014_10_12_000000_create_users_table.php' => 'tests/fixtures/anonymous-migrations/simple.php',
            'stubs/migration.stub' => 'tests/fixtures/anonymous-migrations/stub.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/anonymous-migrations/simple.after.php', 'database/migrations/2014_10_12_000000_create_users_table.php');
        $this->assertFileChanges('tests/fixtures/anonymous-migrations/simple.after.php', 'other/migrations/2014_10_12_000000_create_users_table.php');
        $this->assertFileChanges('tests/fixtures/anonymous-migrations/stub.after.php', 'stubs/migration.stub');
    }
}
