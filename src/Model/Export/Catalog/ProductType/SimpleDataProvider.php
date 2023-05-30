<?php

declare(strict_types=1);

namespace Factfinder\Export\Model\Export\Catalog\ProductType;

use Factfinder\Export\Api\Export\DataProviderInterface;
use Factfinder\Export\Api\Export\ExportEntityInterface;
use Factfinder\Export\Api\Export\FieldInterface;
use Factfinder\Export\Model\Formatter\NumberFormatter;
use Magento\Catalog\Model\Product;

class SimpleDataProvider implements DataProviderInterface, ExportEntityInterface
{
    public function __construct(
        protected Product $product,
        protected NumberFormatter $numberFormatter,
        protected array $productFields = [],
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getEntities(): iterable
    {
        return [$this];
    }

    public function getId(): int
    {
        return (int) $this->product->getId();
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $data = [
            'ProductNumber' => (string) $this->product->getSku(),
            'Master'        => (string) $this->product->getData('sku'),
            'Name'          => (string) $this->product->getName(),
            'Description'   => (string) $this->product->getData('description'),
            'Short'         => (string) $this->product->getData('short_description'),
            'Deeplink'      => (string) $this->product->getUrlInStore(),
            'Price'         => $this->numberFormatter->format((float) $this->product->getFinalPrice()),
            'Availability'  => (int) $this->product->isAvailable(),
            'HasVariants'   => 0,
            'MagentoId'     => $this->getId(),
        ];

        return array_reduce(
            $this->productFields,
            fn (array $result, FieldInterface $field): array  => [$field->getName() => $field->getValue($this->product)] + $result,
            $data
        );
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
