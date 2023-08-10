<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\ClassStrings;

/**
 * @see \Shift\Cli\Tasks\ClassStrings
 */
class ClassStringsTest extends TestCase
{
    use InteractsWithProject;

    private ClassStrings $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ClassStrings();
    }

    #[Test]
    public function it_does_nothing_when_no_matches_are_found()
    {
        $this->fakeProject([
            'composer.json' => 'tests/fixtures/class-strings/composer.json',
            'app/Support/NoOp.php' => '<?php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOp.php');
    }

    #[Test]
    public function it_replaces_string_references_to_classes()
    {
        $this->fakeProject([
            'composer.json' => 'tests/fixtures/class-strings/composer.json',
            'app/Models/Comment.php' => '<?php',
            'app/Models/Post.php' => 'tests/fixtures/class-strings/simple.php',
            'app/Models/User.php' => '<?php',
            'modules/Foo.php' => '<?php',
            'src/Bar.php' => '<?php',
            'app/Providers/RouteServiceProvider.php' => 'tests/fixtures/class-strings/complex.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Models/Comment.php');
        $this->assertFileNotChanged('app/Models/User.php');
        $this->assertFileChanges('tests/fixtures/class-strings/simple.after.php', 'app/Models/Post.php');
        $this->assertFileChanges('tests/fixtures/class-strings/complex.after.php', 'app/Providers/RouteServiceProvider.php');
    }
}
