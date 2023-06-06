<?php

declare(strict_types=1);

namespace Factfinder\Export\Model\Export\Catalog;

use Factfinder\Export\Api\Export\DataProviderInterface;
use Factfinder\Export\Api\Export\ExportEntityInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\ObjectManagerInterface;

class DataProvider implements DataProviderInterface
{
    public function __construct(
        private readonly Products $products,
        private readonly ObjectManagerInterface $objectManager,
        private readonly array $fields,
        private readonly array $entityTypes,
    ) {
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        yield from []; // init generator: Prevent errors in case of an empty product collection

        foreach ($this->products as $product) {
            yield from $this->entitiesFrom($product)->getEntities();
        }
    }

    private function entitiesFrom(ProductInterface $product): DataProviderInterface
    {
        $type = $this->entityTypes[$product->getTypeId()] ?? $this->entityTypes[ProductType::DEFAULT_TYPE];

        return $this->objectManager->create($type, ['product' => $product, 'productFields' => $this->fields]); // phpcs:ignore
    }
}
