## Goal

This web app intends to facilitate event management, or more specifically training management. Both registered users as well as public users shall be able to register. Some statistic output can be generated based on database data e.g. for attendance and more.

## Installation

To install, ensure that you meet the requirements listed in https://symfony.com/doc/current/setup.html#installing-symfony. Clone this repository and run `composer install`. Run the app locally by typing `bin/console server:start`.

To load dummy data, run `bin/console hautelook:fixtures:load`. Your first user has to be created manually in the database. To set your first password, you can use `bin/console security:encode-password`.

## Tests

This web app features unit tests as well as some functional tests. Run the tests by running `bin/phpunit --coverage-text`. The goal is to keep 100% unit test coverage of entities and increase coverage of functional tests. Note that the tests currently assume that standard fixtures are loaded.
