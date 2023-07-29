<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Tasks\FacadeAliases;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \Shift\Cli\Tasks\FacadeAliases
 */
class FacadeAliasesTest extends TestCase
{
    use InteractsWithProject;

    private FacadeAliases $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new FacadeAliases();
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
    public function it_replaces_global_references_with_fqcn()
    {
        $this->fakeProject([
            'config/simple.php' => 'tests/fixtures/facade-aliases/simple.php',
            'app/Support/ComplexClass.php' => 'tests/fixtures/facade-aliases/complex.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/facade-aliases/simple.after.php', 'config/simple.php');
        $this->assertFileChanges('tests/fixtures/facade-aliases/complex.after.php', 'app/Support/ComplexClass.php');
    }
}
