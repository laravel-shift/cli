<?php

namespace Tests;

use PHPUnit\Framework\Assert;

trait InteractsWithProject
{
    private string $uid;

    private string $cwd;

    private array $structure;

    public static function setUpBeforeClass(): void
    {
        if (! SnapshotState::$purged) {
            exec('find ' . __DIR__ . DIRECTORY_SEPARATOR . 'snapshots -mindepth 1 -maxdepth 1 -type d -exec rm -r {} +');

            SnapshotState::$purged = true;
        }

        parent::setUpBeforeClass();
    }

    protected function tearDown(): void
    {
        if (isset($this->cwd)) {
            chdir($this->cwd);
        }

        parent::tearDown();
    }

    public function fakeProject(array $structure): void
    {
        $this->structure = $structure;

        $project = $this->snapshotDirectory();
        mkdir($project);

        foreach ($this->structure as $src => $fixture) {
            if (! is_dir($project . DIRECTORY_SEPARATOR . dirname($src))) {
                mkdir($project . DIRECTORY_SEPARATOR . dirname($src), recursive: true);
            }

            if (str_starts_with($fixture, 'tests/fixtures/')) {
                Assert::assertFileExists($this->fixturePath($fixture));
                copy($this->fixturePath($fixture), $project . DIRECTORY_SEPARATOR . $src);
            } else {
                file_put_contents($project . DIRECTORY_SEPARATOR . $src, $fixture);
            }
        }

        $this->cwd = getcwd();
        chdir($this->snapshotDirectory());
    }

    public function assertFileChanges(string $expected, string $actual): void
    {
        if (str_starts_with($expected, 'tests/fixtures/')) {
            Assert::assertFileEquals($this->fixturePath($expected), $actual);

            return;
        }

        Assert::assertStringEqualsFile($actual, $expected);
    }

    public function assertFileNotChanged(string $actual): void
    {
        Assert::assertArrayHasKey($actual, $this->structure, 'Failed asserting original file existed');
        $expected = $this->structure[$actual];

        if (str_starts_with($expected, 'tests/fixtures/')) {
            Assert::assertFileEquals($this->fixturePath($expected), $actual, 'Failed asserting there were no file changes');

            return;
        }

        Assert::assertStringEqualsFile($actual, $expected, 'Failed asserting there were no file changes');
    }

    public function assertFileMoved(string $expected, string $original): void
    {
        Assert::assertFileDoesNotExist($original);
        Assert::assertFileExists($expected);
    }

    public function assertFileRemoved(string $original)
    {
        Assert::assertFileDoesNotExist($original);
    }

    private function fixturePath(string $fixture)
    {
        return __DIR__ . substr($fixture, 5);
    }

    private function snapshotDirectory(): string
    {
        if (! isset($this->uid)) {
            $caller = debug_backtrace(0, 3)[2];
            $this->uid = md5($caller['class'] . '::' . $caller['function'] . '::' . serialize($caller['args']));
        }

        return __DIR__ . '/snapshots/' . $this->uid;
    }
}
