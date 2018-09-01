<?php

namespace App\Service\ArrayToEntitySaver;

class ArrayToEntitySaver
{
    private $entitySaver;

    public function __construct()
    {
        $this->entitySaver = null;
    }

    public function saveItemsArrayIntoEntity(array $items, IEntitySaver $entitySaver): void
    {
        $this->entitySaver = $entitySaver;
        $this->entitySaver->saveItemsArrayIntoEntity($items);
    }

    public function getFailedRecords(): array
    {
        return $this->entitySaver->getFailedRecords();
    }

    public function getAmountFailedInserts(): int
    {
        return $this->entitySaver->getAmountFailedInserts();
    }

    public function getAmountSuccessfulInserts(): int
    {
        return $this->entitySaver->getAmountSuccessfulInserts();
    }
}
