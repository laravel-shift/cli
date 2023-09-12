# Shift CLI
A tool by [Shift](https://laravelshift.com/) to run automated tasks for maintaining your Laravel projects. With this tool, you may also create your own automated tasks and easily share them.

This tool is current an _alpha release_. Eventually it will replace the [Shift Workbench](https://laravelshift.com/workbench). All of its free tasks are currently available. Premium tasks will be added in the coming weeks.

## Installation
The Shift CLI is bundled as a PHAR, so it has no dependencies and can be installed without conflicts. To use the Shift CLI for your Laravel project, you may run:

```sh
composer require --dev laravel-shift/cli
```

To easily use for all your Laravel projects, you may install the Shift CLI globally by running:

```sh
composer global require laravel-shift/cli
```

## Basic Usage
The recommended way to use the Shift CLI is to simply run the `shift-cli` command from the root of your Laravel project. This will run the default set of [automated tasks](#automated-tasks). The default tasks are based on conventions found in the latest version of Laravel and its documented examples.

To run an individual task, or multiple tasks, you may pass them by name to the `run` command. For example, to run the `anonymous-migrations` and `facades-aliases` tasks, you may run:

```sh
shift-cli run anonymous-migrations facade-aliases
```

By default, the automation is run against all PHP files within your Laravel project. To limit the automation to a path or file, you may set the `--path` option. For example, to run the `anonymous-migrations` task against only the `database/migrations` directory, you may run:

```sh
shift-cli run --path=database/migrations anonymous-migrations
```

You may also use the `--dirty` option to only run the automation against files which have changed since your last commit. For example, to run the `anonymous-migrations` task against only the modified files, you may run:

```sh
shift-cli run --dirty anonymous-migrations
```

## Automated Tasks
- **anonymous-migrations**: (default) Convert class based database migrations into anonymous classes.
- **check-lint**: Check PHP files for syntax errors.
- **class-strings**: (default) Convert strings which contain class references to actual references using `::class`.
- **debug-calls**: Remove calls to debugging functions (`var_dump`, `print_r`, `dd`, etc) from code.
- **declare-strict**: Ensure PHP files declare `strict_types=1`.
- **down-migration**: Remove the `down` method from migrations.
- **explicit-orderby**: (default) Ensure queries use the `orderBy` and `orderByDesc` methods, instead of string arguments.
- **facade-aliases**: (default) Ensure references to Laravel facades are fully qualified, instead of global aliases.
- **faker-methods**: (default) Convert Faker data to method calls, instead of the deprecated property access. 
- **laravel-carbon**: Convert references to `Carbon\Carbon` to use the `Illuminate\Support\Carbon` wrapper.
- **latest-oldest**: Ensure queries use `lastest` and `oldest` methods, instead of longhand `orderBy` methods.
- **model-table**: (default) Remove the `table` property from models which follow Laravel conventions.
- **order-model**: Order model classes by visibility and method type.
- **remove-docblocks**: Remove PHP DocBlocks from code.
- **rules-arrays**: (default) Ensure form request rules are defined as arrays, instead of strings.

## Support Policy
The automated tasks within the Shift CLI prioritize the latest stable version of Laravel (currently Laravel 10). While there will be a grace period when new versions of Laravel are released, you are encouraged to keep your application upgraded (try using [Shift](https://laravelshift.com)).

## Contributing
Contributions are welcome in the form of opening an issue or submitting a pull request. For issues to be considered, they should follow one of the templates. For PRs to be considered, they should have tests and all checks should pass.
