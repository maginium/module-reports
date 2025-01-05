<?php

declare(strict_types=1);

namespace Maginium\Reports\Factories;

use Maginium\Framework\Database\Factories\Factory;

/**
 * Factory for generating country-specific data for testing or database seeding.
 *
 * This factory creates Country model instances with predefined attributes
 * based on data from a fixture file.
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * This method loads data from a CSV file and returns the attributes for creating
     * Country instances. The returned data is used when generating boards
     * for seeding or testing purposes.
     *
     * @return array<string, mixed>|null
     *         An array of attributes for the Country model or null if no valid data is found.
     */
    public function definition(): array
    {
        // Define the path to the fixture CSV file for country data
        $fileName = 'fixtures/dashboard_board.csv';

        // Load the country data from the CSV file
        $boards = $this->loadFile($fileName);

        // Return an empty array if the file is empty or there's an error
        if (empty($boards)) {
            return [];
        }

        // Set the factory count based on the number of loaded boards
        $this->count = count($boards);

        // Return the loaded country data
        return $boards;
    }
}
