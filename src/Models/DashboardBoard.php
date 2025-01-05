<?php

declare(strict_types=1);

namespace Maginium\Reports\Models;

use Exception;
use Magento\Framework\File\Csv;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Mirasvit\Dashboard\Model\Board;
use Mirasvit\Dashboard\Model\BoardFactory;
use Mirasvit\Dashboard\Repository\BoardRepository;
use Maginium\Foundation\Exceptions\LocalizedException;
use Maginium\Framework\Support\Facades\Filesystem;
use Maginium\Framework\Support\Facades\Log;

/**
 * Class DashboardBoard.
 */
class DashboardBoard
{
    /**
     * @var Csv
     */
    protected $csvReader;

    /**
     * @var BoardFactory
     */
    protected $boardFactory;

    /**
     * @var BoardRepository
     */
    protected $boardRepository;

    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * DashboardBoard constructor.
     *
     * @param BoardFactory $boardFactory
     * @param BoardRepository $boardRepository
     * @param SampleDataContext $sampleDataContext
     */
    public function __construct(
        BoardFactory $boardFactory,
        BoardRepository $boardRepository,
        SampleDataContext $sampleDataContext,
    ) {
        $this->boardFactory = $boardFactory;
        $this->boardRepository = $boardRepository;
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->fixtureManager = $sampleDataContext->getFixtureManager();

        // Set Logger class name
        Log::setClassName(static::class);
    }

    /**
     * Install dashboard board data from CSV files.
     *
     * @param array $fixtures
     *
     * @throws Exception
     */
    public function install(array $fixtures): void
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);

            // Check if the file exists
            if (! Filesystem::exists($fileName)) {
                continue;
            }

            // Read CSV data
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows); // Extract header row

            // Process each row of data
            foreach ($rows as $row) {
                $data = array_combine($header, $row);

                // If user_id is empty, remove it from data
                if (empty($data['user_id'])) {
                    unset($data['user_id']);
                }

                try {
                    // Create board instance
                    /** @var Board $board */
                    $board = $this->boardRepository->create();
                    $board->setData($data);

                    // Save the board using repository
                    $this->boardRepository->save($board);
                } catch (LocalizedException $e) {
                    // Log the exception
                    Log::error($e->getMessage());
                } catch (Exception $e) {
                    // Log the exception
                    Log::critical($e);
                }
            }
        }
    }
}
