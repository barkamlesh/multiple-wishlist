<?php

namespace Kamlesh\MultipleWishlist\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MultipleWishlistSearchResultsInterface
 */
interface MultipleWishlistSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get multiple wishlist list.
     *
     * @return MultipleWishlistInterface[]
     */
    public function getItems();

    /**
     * Set multiple wishlist list.
     *
     * @param MultipleWishlistInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
