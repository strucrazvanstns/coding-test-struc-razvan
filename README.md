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

### Running with Docker

1. Run in the cli the following command in order to build and start the container in detached mode:

```shell
docker-compose up --build --detach
```

2. Start the php cli script "<container_name>" should be replaced with the name of the newly created container:

```shell
docker run <container_name> php script.php input.csv
```

3. In order to find the container name use the following command:

```shell
docker ps
```
