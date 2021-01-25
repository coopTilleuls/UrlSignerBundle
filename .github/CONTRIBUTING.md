# Contributing to CoopTilleulsUrlSignerBundle

First of all, thank you for contributing, you're awesome!

To have your code integrated in the CoopTilleulsUrlSignerBundle project, there are some rules to follow, but don't
panic, it's easy!

## Reporting Bugs

If you happen to find a bug, we kindly request you to report it using GitHub by following these 3 points:

* Check if the bug is not already reported
* A clear title to resume the issue
* A description of the workflow needed to reproduce the bug

> _NOTE:_ Don't hesitate giving as much information as you can (OS, PHP version, extensions...)

## Pull Requests

### Matching Coding Standards

The CoopTilleulsUrlSignerBundle project follows [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).
But don't worry, you can fix CS issues automatically using the [PHP CS Fixer](https://cs.symfony.com/) tool. Run php-cs-fixer:

```console
vendor/bin/php-cs-fixer fix
```

Then, add fixed files to your commit before pushing. Be sure to add only **your modified files**. If another files are
fixed by CS tools, just revert them before committing.

### Sending a Pull Request

When you send a PR, just make sure that:

* You add valid test cases
* Tests are green
* You add some documentation (PHPDoc & user documentation in README)
* You make the PR on the same branch you based your changes on. If you see commits that you did not make in your PR,
  you're doing it wrong

All Pull Requests must include [this header](.github/PULL_REQUEST_TEMPLATE.md).

# License and Copyright Attribution

When you open a Pull Request to the CoopTilleulsUrlSignerBundle project, you agree to license your code under the
[MIT license](LICENSE) and to transfer the copyright on the submitted code to Les-Tilleuls.coop.

Be sure to you have the right to do that (if you are a professional, ask your company)!

If you include code from another project, please mention it in the Pull Request description and credit the original
author.
