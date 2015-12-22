__Issues__

If you have a question, something is not working or you want to make a suggestion, feel free to open a [new issue](https://github.com/clippings/phantom-pdf/issues/new).

Checkout existing issues before opening a new one.

__Pull Requests__

 * Use the master branch for your pull requests.
 * Before writing significant amount of code, open an issue and discuss it.
 * Run existing unit tests and write your own. Travis would take care of PRs automatically, but you could run the tests locally before that. Run `composer install` and then `phpunit`.

Do not commit system files, but don't clutter [.gitignore](.gitignore). Use a [useful global .gitignore](https://help.github.com/articles/ignoring-files#global-gitignore).

__Coding style__

Keep the same coding style and try to write lines under 80 characters.
Use [PSR-2 style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), the code is autmatically tested on it by scrutinizer.
