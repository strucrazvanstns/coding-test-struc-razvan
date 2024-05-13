#Commission task

- In order to run the following code folow the steps mentioned below:

1. Run the composer install command

```shell
composer install
```

2. To run the code there has to be an input.csv file containing transaction data in the correct format. Use the following command.
3. Make sure that the env file contains a valid API KEY for the external currency rate endpoint.

```shell
php script.php input.csv
```

4. To run the tests use the following command:

```shell
vendor/phpunit/phpunit/phpunit tests/Service/TransactionServiceTest.php
```
