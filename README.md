<p align="right">
    <a href="https://github.com/laravel-shift/cli/actions"><img src="https://github.com/laravel-shift/cli/workflows/Test/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/laravel-shift/cli"><img src="https://poser.pugx.org/laravel-shift/cli/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://github.com/badges/poser/blob/master/LICENSE"><img src="https://poser.pugx.org/laravel-shift/cli/license.svg" alt="License"></a>
</p>

# Shift CLI
A tool by [Shift](https://laravelshift.com/) to run automated tasks for refactoring and modernizing your Laravel projects.

The Shift CLI replaces the [Shift Workbench](https://laravelshift.com/workbench) desktop app - allowing you to run the same tasks conveniently in your local PHP environment. No Electron. No Docker. Similar to the Workbench, the free tasks are available to run immediately. Premium tasks are available with [a license](https://laravelshift.com/cli#licenses).


## Installation
The Shift CLI is bundled as a PHAR, so it has no dependencies and can be installed without conflicts. To use the Shift CLI in your Laravel project, you may install it locally by running:

```sh
composer require --dev laravel-shift/cli
```

To easily use the Shift CLI for all your Laravel projects, you may install it globally by running:

```sh
composer global require laravel-shift/cli
```


## Basic Usage
The recommended way to use the Shift CLI is to simply run the `shift-cli` command from the root of your Laravel project. This will run the default set of [automated tasks](#automated-tasks). The default tasks are based on conventions found in the latest version of Laravel and its documented examples.

To run an individual task, or multiple tasks, you may pass them by name to the `run` command. For example, to run the `anonymous-migrations` and `facades-aliases` tasks, you may run:

```sh
shift-cli run anonymous-migrations facade-aliases
```

By default, the automation is run against all PHP files under your current path. To limit the automation to a path or file, you may set the `--path` option. For example, to run the `anonymous-migrations` task against only the `database/migrations` directory, you may run:

```sh
shift-cli run --path=database/migrations anonymous-migrations
```

You may also use the `--dirty` option to only run the automation against files which have changed since your last commit. For example, to run the `anonymous-migrations` task against only the uncommitted PHP files, you may run:

```sh
shift-cli run --dirty anonymous-migrations
```


## Automated Tasks
To see a list of all available tasks, you may run: `shift-cli --tasks`

Below is a list of the free tasks included with this package:

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


## Advanced Usage
The Shift CLI is meant to be integrated into your development workflow. Its focus is refactoring your code and ensuring consistency across your projects. As such, it pairs well with a code formatter. Shift recommends using [Laravel Pint](https://laravel.com/docs/pint) as it is a first-party package which applies the Laravel code style by default. It also uses [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) underneath, so you may easily configure it with all the same options. You may, of course, use PHP CS Fixer directly, or another code formatter like [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

For example, to run the Shift CLI and Pint together, you may run:

```sh
shift-cli && pint
```

Taking this farther, you may automate this by setting up your own Composer script. For example, to run the Shift CLI and Pint together, you may add the following to your `composer.json` file:

```json
{
    "scripts": {
        "lint": [
            "shift-cli",
            "pint"
        ]
    }
}
```

You may optimize this script by passing the `--dirty` option to both the Shift CLI and Pint. Once you have added this script, you may run: `composer lint`

Additionally, you may add the `shift-cli` command to a pre-commit hook to ensure the automation is always run before making a commit.

Finally, you are encouraged to add the `shift-cli` to your CI workflows. For example, you may run the `shift-cli` as part of every Pull Request to ensure all merged code consistently follows Laravel conventions.

Examples of setting up Composer scripts and pre-commit hooks may be found in the [Shift CreatorSeries on Laracasts](https://laracasts.com/series/automated-laravel-upgrades/episodes/4).


## Additional Commands
The Shift CLI comes with two additional commands: `publish` and `discover`.

The `publish` command generates a Shift CLI configuration file - `shift-cli.json`. The generated configuration file includes all of the defaults. You may customize the configuration file to specify which tasks to run by default, additional paths to ignore, and options for individual tasks.

The `discover` command regenerates the Shift CLI task manifest. This is done automatically anytime the Shift CLI is updated. However, you may need to run this command if you have included other packages which provide Shift CLI tasks.


## Support Policy
The automated tasks within the Shift CLI prioritize the latest stable version of Laravel (currently Laravel 10). While there will be a grace period when new versions of Laravel are released, you are encouraged to keep your application upgraded (try using [Shift](https://laravelshift.com)).


## Contributing
Contributions are welcome in the form of opening an issue or submitting a pull request. For issues to be considered, they should follow one of the templates. For PRs to be considered, they should have tests and all checks should pass.
