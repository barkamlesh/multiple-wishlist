<?php

namespace Kamlesh\MultipleWishlist\CustomerData;

use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Add new customer section
 */
class MultipleWishlist implements SectionSourceInterface
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * MultipleWishlist constructor.
     * @param WishlistHelper $wishlistHelper
     * @param UrlInterface $urlBuilder
     * @param Data $moduleHelper
     */
    public function __construct(
        WishlistHelper $wishlistHelper,
        UrlInterface $urlBuilder,
        Data $moduleHelper
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->urlBuilder = $urlBuilder;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        try {
            if (!$this->moduleHelper->isEnabled()) {
                return [];
            }

            return [
                'createUrl' => $this->urlBuilder->getUrl('multiplewishlist/manage/create', []),
                'items' => $this->getItems(),
            ];
        } catch (\Exception $e) {
            // Fail silently for customer-data sections to avoid breaking front-end JS when
            // wishlist data cannot be loaded (for example when customer is not logged in).
            return [];
        }
    }

    /**
     * Returns multiple wishlists for the current main wishlist id
     * @return array
     */
    protected function getItems()
    {
        try {
            $collection = $this->moduleHelper->getAllMultipleWishlists($this->wishlistHelper->getWishlist()->getId());
            $items = [];

            foreach ($collection as $multipleWishlist) {
                $items[] = [
                    'id' => $multipleWishlist->getId(),
                    'name' => $multipleWishlist->getName()
                ];
            }

            return $items;
        } catch (\Exception $e) {
            // If any error occurs while loading multiple wishlists, return empty array
            // so the customer-data section remains valid JSON and frontend code won't fail.
            return [];
        }
    }
}
