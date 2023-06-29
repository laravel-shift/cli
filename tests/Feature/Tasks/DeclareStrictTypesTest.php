<?php

namespace Tests\Feature\Tasks;

use App\Facades\Comment;
use App\Tasks\DeclareStrictTypes;
use PHPUnit\Framework\Attributes\Test;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \App\Tasks\DeclareStrictTypes
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
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOpClass.php');
        $this->assertFileChanges('tests/fixtures/strict-types/simple.after.php', 'app/Support/SimpleClass.php');
        $this->assertFileChanges('tests/fixtures/strict-types/complex.after.php', 'app/Support/ComplexClass.php');
    }
}
