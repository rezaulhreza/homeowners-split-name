# What Approach Did I Take?

I decided to use a TDD approach to this problem. I started by writing a test for the simplest scenarios, and then worked my way up to the more complex ones. 

`CsvParserTest` contains the tests for the `CsvParser` class.
`App\Actions\CsvParser` contains the logic for parsing the CSV file and Processing it to database.

`App\Models\HomeOwner` is the Data Model.

`App\Https\Controller\CsvController` is the controller that handles the upload of the CSV file.

Tests:
`tests/Feature/Http/CsvController` - Contains the tests for the `CsvController` class.
`tests/Unit/Actions/CsvParser` - Contains the tests for the `CsvParser` class.

# How to Run the Application

1. Clone the repository
2. Run `composer install`
3. Run `php artisan key:generate`
4. Run `php artisan migrate`
5. Run `php artisan serve`

# How to Run the Tests
`./vendor/bin/phpunit` or `php artisan test` or use your alias for phpunit.

## Some things I would have done differently if I had more time

- Parsing the names such as `Mr & Mrs Smith`. I believe handling split names (couple names let's say).
- Remove some of the duplication in the code.
- Take a more innovative approach to the problem.

Thanks for the opportunity to do this test. I enjoyed it. It was a challenge, but I learnt a lot.

# Homeowner Names - Technical Test

> Please do not spend too long on this test, 2 hours should be more than sufficient. You may
choose to create a full application with a basic front-end to upload the CSV, or a simple class
that loads the CSV from the filesystem.

You have been provided with a CSV from an estate agent containing an export of their
homeowner data. If there are multiple homeowners, the estate agent has been entering both
people into one field, often in different formats.

Our system stores person data as individual person records with the following schema:

### Person

- title - required
- first_name - optional
- initial - optional
- last_name - required

Write a program that can accept the CSV and output an array of people, splitting the name into
the correct fields, and splitting multiple people from one string where appropriate.

For example, the string “Mr & Mrs Smith” would be split into 2 people.

## Example Outputs

Input
`“Mr John Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => “John”,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
```

Input
`“Mr and Mrs Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => null,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
$person[‘title’] => ‘Mrs’,
$person[‘first_name’] => null,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
```

Input
`“Mr J. Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => null,
$person[‘initial’] => “J”,
$person[‘last_name’] => “Smith”
```
