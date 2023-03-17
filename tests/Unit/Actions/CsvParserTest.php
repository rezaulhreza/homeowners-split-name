<?php

namespace Actions;

use App\Actions\CsvParser;
use Exception;
use Tests\TestCase;

class CsvParserTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider nameProvider
     */
    public function it_can_process_name_and_split_them($name, $expected)
    {
        $processCsvAction = new CsvParser();
        $result = $processCsvAction->parseName($name);
        $this->assertEquals($expected, $result);
    }

    public static function nameProvider(): array
    {
        return [
            'Single Name with initial' => [
                'Mr A. Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'initial' => 'A',
                        'last_name' => 'Smith',
                    ],
                ],
            ],

            'A single name with an inital without dot' => [
                'Mr A Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'initial' => 'A',
                        'last_name' => 'Smith',
                    ],
                ],
            ],
            'Two Different Names' => [
                'Mr John Doe and Mr Paul Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'initial' => null,
                        'last_name' => 'Doe',
                    ],
                    [
                        'title' => 'Mr',
                        'first_name' => 'Paul',
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                ],
            ],
            'A single name with a title and a last name' => [
                'Mr Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                ],
            ],

            'A couple name with only last name' => [
                'Mr and Mrs Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => null,
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                ],
            ],

            'A couple name with with first name and last name' => [
                'Mr and Mrs John Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'John',
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                ],
            ],

            'A couple name with with first name and last name and ampersand in the middle' => [
                'Dr & Mrs John Smith',
                [
                    [
                        'title' => 'Dr',
                        'first_name' => 'John',
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'John',
                        'initial' => null,
                        'last_name' => 'Smith',
                    ],
                ],
            ],
        ];
    }

    /** @test */
    public function it_throws_when_file_cannot_be_parsed_or_opened(): void
    {
        $parser = new CsvParser();
        $filename = 'invalid-file.csv';

        $this->expectException(Exception::class);

        $parser->parseCsv($filename);
    }

    /**
     * @test
     */
    public function it_can_consider_a_couple_name(): void
    {
        $parser = new CsvParser();
        $name = 'Mr and Mrs John Smith';

        $result = $parser->consideredAsCouple($name);

        $this->assertTrue($result);
    }
}
