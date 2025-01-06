<?php

declare(strict_types=1);

namespace Maginium\Reports\Setup\Patch\Data;

use Maginium\Framework\Database\Model;
use Maginium\Framework\Database\Setup\Seeder\Context;
use Maginium\Framework\Database\Setup\Seeder\Seeder;
use Maginium\Framework\Support\DataObject;
use Maginium\Framework\Support\Debug\ConsoleOutput;
use Maginium\Framework\Support\Facades\Log;
use Maginium\Reports\Factories\ReportFactory;
use Mirasvit\Dashboard\Model\Board;

/**
 * Seeder for creating board records in the database.
 *
 * This seeder creates initial board entries to ensure they're available
 * for the application, such as 'customer' and other predefined board records.
 */
class ReportSeeder extends Seeder
{
    /**
     * The number of sample records to create.
     *
     * @var int
     */
    protected int $count = 1;

    /**
     * The name of the factory's corresponding model.
     *
     * This property indicates which model this factory is responsible for generating.
     *
     * @var class-string<Model>
     */
    protected string $model = Board::class;

    /**
     * @var ReportFactory The factory instance for creating default board.
     */
    private ReportFactory $reportFactory;

    /**
     * Seeder constructor.
     *
     * @param Seeder\Context $context The context object that provides necessary services.
     */
    public function __construct(
        Context $context,
        ReportFactory $reportFactory,
    ) {
        $this->reportFactory = $reportFactory;

        parent::__construct($context);

        // Set the logger class name for better logging context
        Log::setClassName(static::class);
    }

    /**
     * Seed the database with board data.
     *
     * This method orchestrates the seeding process by generating board records
     * and inserting them into the database. If no records are generated, a warning
     * is logged.
     */
    protected function seed(): void
    {
        // Generate the board records using the factory method
        $boards = $this->make();

        // If no boards were generated, log a message and return early
        if ($boards->isEmpty()) {
            ConsoleOutput::info('No boards were seeded.');

            return;
        }

        // Insert the generated data into the database
        $this->insertData($boards);
    }

    /**
     * Log the successful seeding of a record.
     *
     * This method is invoked after a record has been successfully seeded into the database.
     * It provides feedback to the console about the seeding process.
     *
     * @param DataObject $record The data record to log.
     * @param string $modelName The name of the model being logged.
     */
    protected function log(DataObject $record, string $modelName): void
    {
        /** @var Board $record */
        ConsoleOutput::success("🌱 Seeded {$modelName}: {$record->getTitle()}", addEmoji: false);
    }

    /**
     * Generate board records using the ReportFactory.
     *
     * This method uses the factory to create the board records and returns them
     * as a collection.
     *
     * @return DataObject The collection of generated board records.
     */
    private function make(): DataObject
    {
        // Use the factory to generate default board (based on $this->count) and return the collection.
        return $this->reportFactory->count($this->count)->create();
    }
}
