# How to contribute

Whoops loves to welcome your contributions. There are several ways to help out:

* Create a ticket on [GitHub][github:issues], if you have found a bug.
* Write testcases for [open bug tickets][github:bugs].
* Write patches for open [bug][repo:bugs]/[feature][repo:features] tickets, preferably with testcases included.
* Contribute to the [documentation][repo:docs].

There are a few guidelines that we need contributors to follow so that we have a
chance of keeping on top of things.

## Getting Started

* Make sure you have a [GitHub account][github:signup].
* [Submit a ticket][repo:issue] for your issue, assuming one does not already exist.
	* Clearly describe the issue including steps to reproduce when it is a bug.
	* Make sure you fill in the earliest version that you know has the issue.
* [Fork][repo:fork] the repository on GitHub.

## Making Changes

* Create a topic branch from where you want to base your work.
	* This is usually the develop branch
	* To quickly create a topic branch based on master; `git branch
		master/my_contribution master` then checkout the new branch with `git
		checkout master/my_contribution`. Better avoid working directly on the
		`master` branch, to avoid conflicts if you pull in updates from origin.
* Make commits of logical units.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Use descriptive commit messages and reference the #ticket number
* Core testcases should continue to pass. You can run tests locally or enable
	[travis-ci][travis] for your fork, so all tests and codesniffs
	will be executed.
* Your work should apply the [CakePHP coding standards][cakephp:standards].

## Which branch to base the work

* Bugfix branches will be based on develop branch.
* New features that are backwards compatible will be based on develop branch.
* New features or other non-BC changes will go in the next major release branch.

## Submitting Changes

* Push your changes to a topic branch in your fork of the repository.
* [Submit a pull request][repo:pr] to the repository with the correct target branch.

## Testcases and codesniffer

Whoops tests requires [PHPUnit][phpunit] 3.5 or higher. To run the testcases
locally use the following command:

```
phpunit ./Plugin/Whoops
```

To run the sniffs for CakePHP coding standards:

```
phpcs -p --extensions=php --standard=CakePHP ./Plugin/Whoops
```

Check the [cakephp-codesniffer][cakephp:cs] repository to setup the CakePHP
standard. The README contains installation info for the sniff and phpcs.


# Additional Resources

* [General GitHub documentation][github:docs]
* [GitHub pull request documentation][github:pr]

[cakephp:cs]:https://github.com/cakephp/cakephp-codesniffer
[cakephp:standards]:http://book.cakephp.org/2.0/en/contributing/cakephp-coding-conventions.html
[github:signup]:https://github.com/signup/free
[github:docs]:https://help.github.com
[github:pr]:https://help.github.com/send-pull-requests
[phpunit]:http://phpunit.de
[repo:issue]:https://github.com/PLUGIN_USER/whoops/issues/new
[repo:bugs]:https://github.com/PLUGIN_USER/whoops/issues?label=bug
[repo:features]:https://github.com/PLUGIN_USER/whoops/issues?label=feature
[repo:docs]:https://github.com/PLUGIN_USER/whoops/tree/gh-pages
[repo:fork]:https://github.com/PLUGIN_USER/whoops/fork
[repo:pr]:https://github.com/PLUGIN_USER/whoops/compare
[travis]:https://travis-ci.org

