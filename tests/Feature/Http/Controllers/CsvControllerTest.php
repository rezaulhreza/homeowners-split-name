<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\HomeOwner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_process_csv_file()
    {
        Storage::fake('public');
        $filePath = storage_path('app/public/homeownersdata.csv');
        $file = new UploadedFile($filePath, 'homeownersdata.csv', 'text/csv', null, true);

        $this->postJson(route('csv.upload'), [
            'csv_file' => $file,
        ])
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'first_name',
                    'initial',
                    'last_name',
                ],
            ],
        ])
        ->assertJsonCount(18, 'data');

        //ensuring that file is not saved in storage
        Storage::disk('public')->assertMissing('homeownersdata.csv');

        $this->assertCount(18, HomeOwner::all());

        $this->assertDatabaseHas('home_owners', [
            'title' => 'Mr',
            'first_name' => 'John',
            'initial' => null,
            'last_name' => 'Doe',
        ]);
    }

    /** @test */
    public function it_does_not_process_a_file_if_it_is_not_csv()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('homeownersdata.txt', 1000);
        $this->postJson(route('csv.upload'), [
            'csv_file' => $file,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('csv_file');

        Storage::disk('public')->assertMissing('homeownersdata.csv');

        $this->assertCount(0, HomeOwner::all());
    }
}
