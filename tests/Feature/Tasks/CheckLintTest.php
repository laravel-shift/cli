<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Facades\Comment;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Tasks\CheckLint;

/**
 * @see \Shift\Cli\Tasks\CheckLint
 */
class CheckLintTest extends TestCase
{
    use InteractsWithProject;

    private CheckLint $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new CheckLint();
    }

    #[Test]
    public function it_does_nothing_when_syntax_errors_are_not_found()
    {
        $this->fakeProject([
            'app/Support/NoOp.php' => '<?php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileNotChanged('app/Support/NoOp.php');
    }

    #[Test]
    public function it_returns_failure_and_leaves_comment_when_syntax_errors_are_found()
    {
        $this->fakeProject([
            'invalid-file.php' => '<?php echo "Unescaped quote"";',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(1, $result);

        $comments = Comment::flush();
        $this->assertCount(1, $comments);
        $this->assertStringContainsString('/invalid-file.php', $comments[0]->content());
        $this->assertSame(['Line 1: unexpected double-quote mark, expecting "," or ";"'], $comments[0]->paths());
    }
}
