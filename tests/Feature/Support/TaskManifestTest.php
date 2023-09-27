<?php

namespace Tests\Feature\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shift\Cli\Sdk\Testing\InteractsWithProject;
use Shift\Cli\Support\TaskManifest;

class TaskManifestTest extends TestCase
{
    use InteractsWithProject;

    #[Test]
    public function list_returns_existing_manifest()
    {
        $this->fakeProject([
            'shift-tasks.php' => '<?php return ["namespace" => "Shift", "tasks" => ["task-name" => "fqcn"]];',
        ]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), []);

        $this->assertSame(['task-name' => 'fqcn'], $taskManifest->list());
    }

    #[Test]
    public function list_returns_default_tasks_for_no_manifest()
    {
        $this->fakeProject([]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), ['Tests\\Support\\GoodTask']);

        $this->assertSame(['good-task' => 'Tests\\Support\\GoodTask'], $taskManifest->list());
        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');

        $manifest = require $this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php';
        $this->assertSame('Shift', $manifest['namespace']);
        $this->assertArrayHasKey('tasks', $manifest);
    }

    #[Test]
    public function list_rebuilds_stale_manifest()
    {
        $this->fakeProject([
            'shift-tasks.php' => '<?php return ["namespace" => "Stale", "tasks" => ["task-name" => "foo"]];',
            'composer/installed.json' => \json_encode([
                'packages' => [
                    [
                        'extra' => ['laravel' => true],
                    ],
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['Tests\\Support\\GoodTask'],
                            ],
                        ],
                    ],
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['Tests\\Support\\GoodTask', 'Tests\\Support\\DirtyTask'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), ['Tests\\Support\\CommentTask']);

        $this->assertEqualsCanonicalizing(
            ['comment-task' => 'Tests\\Support\\CommentTask', 'good-task' => 'Tests\\Support\\GoodTask', 'dirty-task' => 'Tests\\Support\\DirtyTask'],
            $taskManifest->list()
        );

        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');

        $manifest = require $this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php';
        $this->assertSame('Shift', $manifest['namespace']);
        $this->assertArrayHasKey('tasks', $manifest);
    }

    #[Test]
    public function build_returns_merged_tasks_from_packages()
    {
        $this->fakeProject([
            'composer/installed.json' => \json_encode([
                'packages' => [
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['Tests\\Support\\CommentTask'],
                            ],
                        ],
                    ],
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['Tests\\Support\\BadTask'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), ['Tests\\Support\\GoodTask']);

        $this->assertEqualsCanonicalizing(
            ['good-task' => 'Tests\\Support\\GoodTask', 'comment-task' => 'Tests\\Support\\CommentTask', 'bad-task' => 'Tests\\Support\\BadTask'],
            $taskManifest->list()
        );

        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');

        $manifest = require $this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php';
        $this->assertSame('Shift', $manifest['namespace']);
        $this->assertArrayHasKey('tasks', $manifest);
    }
}
