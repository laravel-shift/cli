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
            'shift-tasks.php' => '<?php return ["task-name" => "fqcn"];',
        ]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), []);

        $this->assertSame(['task-name' => 'fqcn'], $taskManifest->list());
    }

    #[Test]
    public function list_returns_default_tasks_for_no_manifest()
    {
        $this->fakeProject([]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), ['task-name' => 'Fully\\Qualified\\Class\\Name']);

        $this->assertSame(['task-name' => 'Fully\\Qualified\\Class\\Name'], $taskManifest->list());
        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');
    }

    #[Test]
    public function build_returns_merged_tasks_from_packages()
    {
        $this->fakeProject([
            'composer/installed.json' => json_encode([
                'packages' => [
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['package1-task' => '\\Package1\\Task'],
                            ],
                        ],
                    ],
                    [
                        'extra' => [
                            'shift' => [
                                'tasks' => ['package2-task' => '\\Package2\\Task'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath(), ['task-name' => 'Fully\\Qualified\\Class\\Name']);

        $this->assertEqualsCanonicalizing(
            ['task-name' => 'Fully\\Qualified\\Class\\Name', 'package1-task' => '\\Package1\\Task', 'package2-task' => '\\Package2\\Task'],
            $taskManifest->list()
        );

        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');
    }
}
