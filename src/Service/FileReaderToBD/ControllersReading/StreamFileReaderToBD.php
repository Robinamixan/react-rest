<?php

namespace App\Service\FileReaderToBD\ControllersReading;

use App\Service\ArrayToEntitySaver\ArrayToEntitySaver;
use App\Service\ArrayToEntitySaver\EntitySavers\ProductSaver;
use App\Service\ArrayToEntitySaver\EntitySavers\ProductTestSaver;
use App\Service\ArrayToEntitySaver\IEntitySaver;
use App\Service\EntityConverter\ArrayToEntityConverters\ArrayToProductConverter;
use App\Service\EntityConverter\EntityConverter;
use App\Service\EntityConverter\EntityToArrayConverters\ProductToArrayConverter;
use App\Service\EntityValidator\ArrayToEntityValidators\ArrayToProductValidator;
use App\Service\EntityValidator\EntityValidator;
use App\Service\EntityValidator\IArrayToEntityValidator;
use App\Service\FileReader\FileReader;
use App\Service\FileReaderToBD\IControllerReading;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class StreamFileReaderToBD implements IControllerReading
{
    protected $fileReader;
    protected $arrayToEntitySaver;
    protected $entityManager;
    protected $entityConverter;
    protected $entityValidator;
    protected $validatorBuilder;
    protected $flagTestMode;
    protected $itemsBuffer;
    protected $fileReadingReport;
    protected $arrayToEntityConverter;
    protected $entityToArrayConverter;
    protected const BUFFER_SIZE = 1000;
    protected const ERRORS_INSERTS_FILE = 'files/logFailureItems.csv';

    public function __construct(
        FileReader $fileReader,
        ArrayToEntitySaver $arrayToEntitySaver,
        EntityManagerInterface $entityManager,
        EntityConverter $entityConverter,
        EntityValidator $entityValidator,
        ValidatorBuilder $validatorBuilder,
        bool $flagTestMode
    ) {
        if (file_exists($this::ERRORS_INSERTS_FILE)) {
            unlink($this::ERRORS_INSERTS_FILE);
        }
        $this->fileReadingReport = [
            'failedRecords' => [],
            'amountFailedItems' => 0,
            'amountProcessedItems' => 0,
            'amountSuccessesItems' => 0,
        ];
        $this->entityConverter = $entityConverter;
        $this->entityValidator = $entityValidator;
        $this->entityManager = $entityManager;
        $this->arrayToEntitySaver = $arrayToEntitySaver;
        $this->fileReader = $fileReader;
        $this->flagTestMode = $flagTestMode;
        $this->validatorBuilder = $validatorBuilder;
        $this->itemsBuffer = [];

    }

    /**
     * @param \SplFileObject $file
     * @return array
     */
    public function readFileToBD(\SplFileObject $file): array
    {
        $this->arrayToEntityConverter = new ArrayToProductConverter();
        $this->entityToArrayConverter = new ProductToArrayConverter();

        $productSaver = !$this->flagTestMode
            ? new ProductSaver(
                $this->entityManager,
                $this->entityConverter,
                $this->entityToArrayConverter,
                $this->arrayToEntityConverter,
                $this->validatorBuilder
            )
            : new ProductTestSaver(
                $this->entityManager,
                $this->entityConverter,
                $this->entityToArrayConverter,
                $this->arrayToEntityConverter,
                $this->validatorBuilder
            );

        $productValidator = new ArrayToProductValidator($this->entityConverter);
        $this->fileReader->setFileForRead($file);
        ini_set('max_execution_time', 1000);

        while ($item = $this->fileReader->getNextItem()) {
            $this->fileReadingReport['amountProcessedItems']++;
            $this->checkIsValidItemsAndSave($item, $productValidator, $productSaver);
        }

        if (!empty($this->itemsBuffer)) {
            $this->saveBufferInBD($productSaver);
        }

        $errorsInsertsFile = new \SplFileObject($this::ERRORS_INSERTS_FILE, 'r');
        $this->fileReader->setFileForRead($errorsInsertsFile);
        for ($number = 0; $number < 20; $number++) {
            if ($item = $this->fileReader->getNextItem()) {
                $this->fileReadingReport['failedRecords'][] = $item;
            }
        }

        return $this->fileReadingReport;
    }

    /**
     * @param array $item
     * @param IArrayToEntityValidator $productValidator
     * @param IEntitySaver $productSaver
     */
    public function checkIsValidItemsAndSave(
        array $item,
        IArrayToEntityValidator $productValidator,
        IEntitySaver $productSaver
    ): void {
        if ($this->entityValidator->isValidItemToEntityRules($item, $productValidator)) {
            $this->collectItemsAndSave($item, $productSaver);
        } else {
            $this->fileReadingReport['failedRecords'][] = $item;
            $this->fileReadingReport['amountFailedItems']++;
        }
    }

    /**
     * @param array $item
     * @param IEntitySaver $productSaver
     */
    public function collectItemsAndSave(array $item, IEntitySaver $productSaver): void
    {
        $this->itemsBuffer[] = $item;
        if (count($this->itemsBuffer) === $this::BUFFER_SIZE) {
            $this->saveBufferInBD($productSaver);
            $this->itemsBuffer = [];
            $this->putFailedRecordsInFile();
            gc_collect_cycles();
        }
    }

    /**
     * @param IEntitySaver $productSaver
     */
    public function saveBufferInBD(IEntitySaver $productSaver): void
    {
        $this->arrayToEntitySaver->saveItemsArrayIntoEntity($this->itemsBuffer, $productSaver);

        $this->fileReadingReport['amountFailedItems'] += $this->arrayToEntitySaver->getAmountFailedInserts();
        $this->fileReadingReport['amountSuccessesItems'] += $this->arrayToEntitySaver->getAmountSuccessfulInserts();

        $this->fileReadingReport['failedRecords'] = array_merge(
            $this->fileReadingReport['failedRecords'],
            $this->arrayToEntitySaver->getFailedRecords()
        );
    }

    protected function putFailedRecordsInFile()
    {
        $fileName = 'files/logFailureItems.csv';
        $file = fopen($fileName, 'a+');
        foreach ($this->fileReadingReport['failedRecords'] as $failedRecord) {
            fputcsv($file, $failedRecord);
            $failedRecord = null;
        }

        fclose($file);
        $this->fileReadingReport['failedRecords'] = [];
    }
}
