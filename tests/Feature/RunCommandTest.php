<?php

namespace Tests\Feature;

use App\Support\TaskManifest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\BadTask;
use Tests\Support\DirtyTask;
use Tests\Support\GoodTask;
use Tests\Support\PathTask;
use Tests\TestCase;

/**
 * @see \App\Commands\RunCommand
 */
class RunCommandTest extends TestCase
{
    #[Test]
    public function it_performs_the_task()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['good-task' => GoodTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run good-task')
            ->assertSuccessful();
    }

    #[Test]
    public function it_throws_exception_for_unknown_task()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn([]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task not registered: foo-task');

        $this->artisan('run foo-task')
            ->assertFailed();
    }

    #[Test]
    public function it_outputs_error_for_failed_task()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['bad-task' => BadTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run bad-task')
            ->expectsOutput('Failed to run task: bad-task')
            ->assertExitCode(123);
    }

    #[Test]
    public function it_sets_dirty_based_on_command_options()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['dirty-task' => DirtyTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run dirty-task --dirty')
            ->assertSuccessful();
    }

    #[Test]
    public function it_sets_paths_based_on_command_arguments()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['path-task' => PathTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run path-task --path=foo.php --path=src/bar.php --path=tests/qux.php')
            ->assertSuccessful();
    }
}