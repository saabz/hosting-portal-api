## About Application

This is an api service implemented in Laravel. The data source for this application is an excel file.
When a user hits the api end point "api/servers/list", the service will read data from the excel file and based on the filters requested in the API, returns the data back to the client.

Data repository is used to extract data from the Excel file, which inturn uses PhpSpreadsheet library.

Another repository used in the application is response repository, so that the API response structure is consistent across application.

## Running the application

The application can be run in the local environment using the below command

```bash
php artisan serve
```

## Running the tests

The application tests can be initiated using the below command.

```bash
php artisan test
```
