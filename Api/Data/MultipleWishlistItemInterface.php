<?php
declare(strict_types=1);
namespace Kamlesh\MultipleWishlist\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for MultipleWishlistItem Model
use Magento\Framework\Api\ExtensibleDataInterface;

 */
interface MultipleWishlistItemInterface
{
    const PRIMARY_ID = 'item_id';
    const MULTIPLE_WISHLIST_ID = 'multiple_wishlist_id';
    const MULTIPLE_WISHLIST_ITEM = 'wishlist_item_id';
    const WISHLIST_ID = 'wishlist_id';

    /**
     * Returns multiple wishlist item id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set multiple wishlist item id
     *
     * @param int|string $id
     * @return MultipleWishlistItemInterface
     */
    public function setId($id);

    /**
     * Returns main wishlist id
     *
     * @return int
     */
    public function getWishlistId(): int;

    /**
     * Set main wishlist id
     *
     * @param int $id
     * @return MultipleWishlistInterface
     */
    public function setWishlistId(int $id): self;

    /**
     * Returns multiple wishlist id
     *
     * @return int
     */
    public function getMultipleWishlistId(): ?int;

    /**
     * Set multiple wishlist id
     *
     * @param int|null $id
     * @return MultipleWishlistItemInterface
     */
    public function setMultipleWishlistId(?int $id): self;

    /**
     * Returns main wishlist item id
     *
     * @return int
     */
    public function getWishlistItemId(): int;

    /**
     * Set main wishlist item id
     *
     * @param int $id
     * @return MultipleWishlistItemInterface
     */
    public function setWishlistItemId(int $id): self;
}
