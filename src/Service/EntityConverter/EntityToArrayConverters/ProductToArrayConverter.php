<?php

namespace App\Service\EntityConverter\EntityToArrayConverters;

use App\Service\EntityConverter\IEntityToArrayConverter;

class ProductToArrayConverter implements IEntityToArrayConverter
{
    public function convertEntityToArray(object $entity): array
    {
        $item = [];
        $item['product_code'] = $entity->getProductCode();
        $item['product_name'] = $entity->getProductName();
        $item['product_description'] = $entity->getProductDesc();
        $item['product_stock'] = $entity->getProductStock();
        $item['product_cost'] = $entity->getProductCost();
        if (!is_null($entity->getDiscontinuedDate())) {
            $item['product_discontinued'] = 'yes';
        } else {
            $item['product_discontinued'] = null;
        }
        $entity = null;

        return $item;
    }
}
