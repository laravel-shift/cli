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

        $taskManifest = new TaskManifest($this->currentSnapshotPath());

        $this->assertSame(['task-name' => 'fqcn'], $taskManifest->list());
    }

    #[Test]
    public function list_returns_default_tasks_for_no_manifest()
    {
        $this->fakeProject([]);

        $taskManifest = new TaskManifest($this->currentSnapshotPath());

        $this->assertSame($this->defaultTasks(), $taskManifest->list());
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

        $taskManifest = new TaskManifest($this->currentSnapshotPath());

        $this->assertEqualsCanonicalizing(
            array_merge(['package1-task' => '\\Package1\\Task', 'package2-task' => '\\Package2\\Task'], $this->defaultTasks()),
            $taskManifest->list()
        );

        $this->assertFileExists($this->currentSnapshotPath() . DIRECTORY_SEPARATOR . 'shift-tasks.php');
    }

    private function defaultTasks()
    {
        return [
            'anonymous-migrations' => \Shift\Cli\Tasks\AnonymousMigrations::class,
            'check-lint' => \Shift\Cli\Tasks\CheckLint::class,
            'class-strings' => \Shift\Cli\Tasks\ClassStrings::class,
            'debug-calls' => \Shift\Cli\Tasks\DebugCalls::class,
            'declare-strict' => \Shift\Cli\Tasks\DeclareStrictTypes::class,
            'down-migration' => \Shift\Cli\Tasks\DownMigration::class,
            'explicit-orderby' => \Shift\Cli\Tasks\ExplicitOrderBy::class,
            'facade-aliases' => \Shift\Cli\Tasks\FacadeAliases::class,
            'faker-methods' => \Shift\Cli\Tasks\FakerMethods::class,
            'laravel-carbon' => \Shift\Cli\Tasks\LaravelCarbon::class,
            'latest-oldest' => \Shift\Cli\Tasks\LatestOldest::class,
            'model-table' => \Shift\Cli\Tasks\ModelTableName::class,
            'order-model' => \Shift\Cli\Tasks\OrderModel::class,
            'remove-docblocks' => \Shift\Cli\Tasks\RemoveDocBlocks::class,
            'rules-arrays' => \Shift\Cli\Tasks\RulesArrays::class,
        ];
    }
}
