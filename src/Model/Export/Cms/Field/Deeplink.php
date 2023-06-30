<?php

declare(strict_types=1);

namespace Factfinder\Export\Model\Export\Cms\Field;

use Factfinder\Export\Api\Export\FieldInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\AbstractModel;

class Deeplink implements FieldInterface
{
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    /**
     * @param PageInterface $page
     *
     * @return string
     */
    public function getValue(AbstractModel $page): string
    {
        $this->urlBuilder->setScope($this->storeManager->getStore()->getId());

        return $this->urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }
}
