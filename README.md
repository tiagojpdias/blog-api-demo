# Blog API demo
[![StyleCI](https://styleci.io/repos/131721642/shield?branch=master)](https://styleci.io/repos/131721642) [![Build Status](https://travis-ci.org/quetzyg/blog-api-demo.svg?branch=master)](https://travis-ci.org/quetzyg/blog-api-demo) [![codecov](https://codecov.io/gh/quetzyg/blog-api-demo/branch/master/graph/badge.svg)](https://codecov.io/gh/quetzyg/blog-api-demo)

This is a Laravel 5.6 application for demoing a Blog API (JSON API spec), with a simple data structure, consisting of solely two tables (`users` and `posts`).

## Project setup
Install PHP dependencies:
```sh
composer install
```

Copy the `.env` file:
```sh
cp .env.example .env
```

Generate encryption key:
```sh
php artisan key:generate
```

Run migrations (refresh) and seed database:
```sh
php artisan migrate:refresh --seed
```

## Testing
To run the tests, execute:
```sh
vendor/bin/phpunit --verbose
```

## Coding Style
The PSR-2 coding style is followed as much as possible.

### Check
On each commit, the `composer cs-check` script will run to check that the coding style is in accordance to PSR-2 and some other rules.

### Fix
If the commit fails because of the coding style, run the following command to fix the issue:

```sh
composer cs-fix
```

### Pre-Commit Hook installation
To run the coding style check before each commit, install the bundled script in the project root with the following command:

```sh
cp pre-commit.sh .git/hooks/pre-commit
```

You won't be able to commit code if the check fails.

## Committing to git
Each commit should have a proper message describing the work that was done.
This is called [Semantic Commit Messages](https://seesparkbox.com/foundry/semantic_commit_messages).

Here's what a commit message should look like:

```txt
feat(Users): add password recovery
^--^ ^---^   ^-------------------^
|    |       |
|    |       +-> Description of the work in the present tense.
|    |
|    +---------> Scope of the work.
|
+--------------> Type: chore, docs, feat, fix, hack, refactor, style, or test.
```
