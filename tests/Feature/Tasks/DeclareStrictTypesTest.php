<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\DeclareStrictTypes;

/**
 * @see \Shift\Cli\Tasks\DeclareStrictTypes
 */
class DeclareStrictTypesTest extends TestCase
{
    use InteractsWithProject;

    private DeclareStrictTypes $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new DeclareStrictTypes();
    }

    #[Test]
    public function it_adds_declaration_to_files()
    {
        $this->fakeProject([
            'app/Support/NoOpClass.php' => 'tests/fixtures/strict-types/no-op.php',
            'app/Support/SimpleClass.php' => 'tests/fixtures/strict-types/simple.php',
            'app/Support/ComplexClass.php' => 'tests/fixtures/strict-types/complex.php',
            'resources/views/template.blade.php' => '@noop',
            'some/other/file.php' => '<html lang="en"><title>File with <?php echo "PHP"; ?></title></html>',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOpClass.php');
        $this->assertFileNotChanged('resources/views/template.blade.php');
        $this->assertFileNotChanged('some/other/file.php');
        $this->assertFileChanges('tests/fixtures/strict-types/simple.after.php', 'app/Support/SimpleClass.php');
        $this->assertFileChanges('tests/fixtures/strict-types/complex.after.php', 'app/Support/ComplexClass.php');
    }
}
