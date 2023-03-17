<?php

namespace App\Actions;

use App\Models\HomeOwner;
use Exception;
use Illuminate\Support\Str;

class CsvParser
{
    const SEPARATORS = ['and', '&'];

    /**
     * Parses a CSV file containing a list of people's names
     * and returns an array of person objects.
     *
     * @param  string  $filename The path to the CSV file.
     *
     * @throws Exception If the CSV file could not be parsed.
     */
    public function parseCsv(string $filename): array
    {
        $file = fopen($filename, 'r');
        if (! $file) {
            throw new Exception("Could not open CSV file: $filename");
        }

        $people = [];

        while (($data = fgetcsv($file)) !== false) {
            foreach ($this->parseName($data[0]) as $person) {
                $people[] = $person;
            }
        }

        fclose($file);

        return $people;
    }

    /**
     * Returns an array of salutations. Such as Mr, Mrs, Miss, Ms, Dr, Prof.
     *
     * @return array An array of salutations.
     */
    public function salutations(): array
    {
        return  ['Mr', 'Mrs', 'Miss', 'Ms', 'Mister', 'Dr', 'Prof'];
    }

    /**
     * Checks if a name is in the format "Mr and Mrs X". Name may contain & instead of and.
     * E.g. "Mr and Mrs X" or "Mr & Mrs X". We assume that they are a couple.
     *
     * @param  string  $name The name to check.
     * @return bool True if the name is in the format "Mr and Mrs X", false otherwise.
     */
    public function consideredAsCouple($name): bool
    {
        $name = Str::of($name)->trim();
        $nameParts = $name->explode(' ');

        //lower the titles/salutations
        $salutations = collect($this->salutations())->map(fn ($salutation) => Str::lower($salutation))->toArray();

        // Check for "Mr and Mrs" or "Mr & Mrs" format
        if (
            count($nameParts) >= 3
            && in_array(Str::lower($nameParts[0]), $salutations)
            && in_array(Str::lower($nameParts[1]), self::SEPARATORS) && in_array(
                Str::lower($nameParts[2]),
                $salutations
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Parses a person's name and returns an array containing the person's title, first name, initial and last name.
     * Handles all types of names except for couple names. Couple names are handled in the `extractCoupleName` method.
     *
     * @param  string  $name The name to parse.
     * @return array An array containing the person's title, first name, initial and last name.
     */
    public function parseName(string $name): array
    {
        if ($this->consideredAsCouple($name)) {
            // Split the name into two separate names using the `extractCoupleName` method
            $names = collect($this->extractCoupleName($name));
        } else {
            // Handle other types of names as before
            $names = collect(Str::of($name)->split('/\s+(and|&)\s+/i'));
        }

        return $names
            ->map(fn ($name) => $this->parsePerson($name))
            ->reject(fn ($person) => empty($person['title']) || empty($person['last_name']))
            ->toArray();
    }

    /**
     * Splits a couple name into two separate names. E.g. "Mr and Mrs X" will be split into "Mr X" and "Mrs X".
     *
     * @param  string  $name The name to split.
     * @return array An array containing two names.
     */
    public function extractCoupleName(string $name): array
    {
        $nameSegments = Str::of($name)->explode(' ');

        // Extract the last name.
        if ($nameSegments->count() > 4) {
            // "Mr and Mrs X Y" will be split into "Mr X Y" and "Mrs X Y"
            $lastName = $nameSegments[$nameSegments->count() - 2].' '.$nameSegments[$nameSegments->count() - 1];
        } else {
            //Mr and Mrs X" will be split into "Mr X" and "Mrs X". X is the last name.
            $lastName = $nameSegments->pop();
        }
        [$salutation, , $secondTitle] = $nameSegments->toArray();

        // Create two separate names: one for each person. "Mr and Mrs X" will be split into "Mr X" and "Mrs X".
        $nameBeforeSeparator = Str::of("$salutation $lastName")->trim()->__toString();
        $nameAfterSeparator = Str::of("$secondTitle $lastName")->trim()->__toString();

        return [$nameBeforeSeparator, $nameAfterSeparator];
    }

    /**
     * Parses a person's name into its constituent parts.
     *
     * @param  string  $name The name to parse.
     * @return array An array containing the person's title, first name, initial and last name.
     */
    protected function parsePerson(string $name): array
    {
        $nameSegments = Str::of(trim($name))->split('/\s+(and|&\s+)?/i');
        $person = collect([
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null,
        ]);

        if (in_array($nameSegments->first(), $this->salutations())) {
            $person->put('title', $nameSegments->shift());
        }

        if ($nameSegments->count() == 1) {
            $person->put('last_name', $nameSegments->first());
        } else {
            $lastName = $nameSegments->pop();

            if (Str::of($nameSegments->first())->length() == 1) {
                // If the first part is a single character, assume it is an initial.
                $person->put('initial', $nameSegments->shift());
                $person->put('first_name', $nameSegments->first());
            } else {
                $person->put('first_name', $nameSegments->first());
            }

            $firstName = Str::of($person['first_name']);
            if ($firstName->endsWith('.') && $firstName->length() <= 2) {
                $person->put('initial', $firstName->before('.')->__toString());
                $person->put('first_name', null);
            }

            $person->put('last_name', $lastName);
        }

        return $person->toArray();
    }

    /**
     * Process the parsed data to store in the database.
     *
     * @param  array  $people The parsed data wihich will be stored in the database.
     */
    public function process($people): void
    {
        collect($people)->map(fn ($person) => HomeOwner::create($person));
    }
}
