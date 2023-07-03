<?php

namespace Tests\Feature\Tasks;

use App\Tasks\ModelTableName;
use PHPUnit\Framework\Attributes\Test;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \App\Tasks\ModelTableName
 */
class ModelTableNameTest extends TestCase
{
    use InteractsWithProject;

    private ModelTableName $subject;

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

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/table-name/model.after.php', 'app/Models/User.php');
        $this->assertFileChanges('tests/fixtures/table-name/pivot.after.php', 'app/Models/RoleUser.php');
    }
}
