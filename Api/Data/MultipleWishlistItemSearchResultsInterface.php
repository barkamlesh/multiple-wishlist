<?php

namespace Kamlesh\MultipleWishlist\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MultipleWishlistItemSearchResultsInterface
 */
interface MultipleWishlistItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get multiple wishlist item list.
     *
     * @return MultipleWishlistItemInterface[]
     */
    public function getItems();

    /**
     * Set multiple wishlist item list.
     *
     * @param MultipleWishlistItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
