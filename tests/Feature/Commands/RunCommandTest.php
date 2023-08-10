<?php

namespace Tests\Feature\Commands;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Shift\Cli\Sdk\Testing\Support\BadTask;
use Shift\Cli\Sdk\Testing\Support\CommentTask;
use Shift\Cli\Sdk\Testing\Support\DirtyTask;
use Shift\Cli\Sdk\Testing\Support\GoodTask;
use Shift\Cli\Sdk\Testing\Support\PathTask;
use Shift\Cli\Sdk\Testing\TestCase;
use Shift\Cli\Support\TaskManifest;

/**
 * @see \Shift\Cli\Commands\RunCommand
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
    public function it_outputs_comment_from_task()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['comment-task' => CommentTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run comment-task')
            ->assertSuccessful()
            ->expectsOutput('Leaving a test comment.')
            ->expectsOutput('Reference: https://laravel.com/docs/')
            ->expectsOutput('  - file-1.php')
            ->expectsOutput('  - file-2.php');
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
    public function it_finds_files_based_on_command_arguments()
    {
        $taskManifest = Mockery::mock(TaskManifest::class);
        $taskManifest->expects('list')
            ->withNoArgs()
            ->andReturn(['path-task' => PathTask::class]);
        $this->swap(TaskManifest::class, $taskManifest);

        $this->artisan('run path-task --path=bootstrap --path=app/Commands/RunCommand.php --path=tests/Feature/RunCommandTest.php')
            ->assertSuccessful();
    }
}
