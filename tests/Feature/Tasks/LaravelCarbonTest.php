<?php

namespace Tests\Feature\Tasks;

use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Tasks\LaravelCarbon;
use Tests\InteractsWithProject;
use Tests\TestCase;

/**
 * @see \Shift\Cli\Tasks\LaravelCarbon
 */
class LaravelCarbonTest extends TestCase
{
    use InteractsWithProject;

    private LaravelCarbon $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new LaravelCarbon();
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
    public function it_replaces_references_to_carbon()
    {
        $this->fakeProject([
            'app/Http/Controllers/SimpleController.php' => 'tests/fixtures/laravel-carbon/simple.php',
            'app/Support/TimeService.php' => 'tests/fixtures/laravel-carbon/complex.php',
        ]);

        $result = $this->subject->perform();

        $this->assertSame(0, $result);

        $this->assertFileChanges('tests/fixtures/laravel-carbon/simple.after.php', 'app/Http/Controllers/SimpleController.php');
        $this->assertFileChanges('tests/fixtures/laravel-carbon/complex.after.php', 'app/Support/TimeService.php');
    }
}
