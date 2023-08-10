<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\DownMigration;

/**
 * @see \Shift\Cli\Tasks\DownMigration
 */
class DownMigrationTest extends TestCase
{
    use InteractsWithProject;

    private DownMigration $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new DownMigration();
    }

    #[Test]
    public function it_does_nothing_when_no_methods_are_found()
    {
        $this->fakeProject([
            'database/migrations/no_op.php' => 'tests/fixtures/down-migration/no-op.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('database/migrations/no_op.php');
    }

    #[Test]
    public function it_replaces_string_references_to_classes()
    {
        $this->fakeProject([
            'database/migrations/basic_migration.php' => 'tests/fixtures/down-migration/simple.php',
            'database/migrations/complex_migration.php' => 'tests/fixtures/down-migration/complex.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/down-migration/simple.after.php', 'database/migrations/basic_migration.php');
        $this->assertFileChanges('tests/fixtures/down-migration/complex.after.php', 'database/migrations/complex_migration.php');
    }
}
