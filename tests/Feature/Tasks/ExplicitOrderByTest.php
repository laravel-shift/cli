<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Tasks\ExplicitOrderBy;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \Shift\Cli\Tasks\ExplicitOrderBy
 */
class ExplicitOrderByTest extends TestCase
{
    use InteractsWithProject;

    private ExplicitOrderBy $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ExplicitOrderBy();
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
            'app/Support/ExampleClass.php' => 'tests/fixtures/explicit-orderby/example.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/explicit-orderby/example.after.php', 'app/Support/ExampleClass.php');
    }
}
