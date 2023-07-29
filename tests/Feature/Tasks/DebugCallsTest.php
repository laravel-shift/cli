<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Facades\Comment;
use Shift\Cli\Tasks\DebugCalls;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \Shift\Cli\Tasks\DebugCalls
 */
class DebugCallsTest extends TestCase
{
    use InteractsWithProject;

    private DebugCalls $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new DebugCalls();
    }

    #[Test]
    public function it_does_nothing_when_no_matches_are_found()
    {
        $this->fakeProject([
            'app/Support/NoOp.php' => '<?php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOp.php');
    }

    #[Test]
    public function it_removes_calls_to_debugging_functions()
    {
        $this->fakeProject([
            'app/Support/SomeClass.php' => 'tests/fixtures/debug-class/simple.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(1, $result);

        $comments = Comment::flush();
        $this->assertCount(1, $comments);
        $this->assertStringContainsString('/app/Support/SomeClass.php', $comments[0]->content());
        $this->assertSame([
            'Line 6: contains call to `var_dump`',
            'Line 7: contains call to `var_dump`',
            'Line 9: contains call to `print_r`',
            'Line 12: contains call to `var_export`',
            'Line 15: contains call to `dump`',
            'Line 16: contains call to `dd`',
        ], $comments[0]->paths());

        $this->assertFileChanges('tests/fixtures/debug-class/simple.after.php', 'app/Support/SomeClass.php');
    }
}
