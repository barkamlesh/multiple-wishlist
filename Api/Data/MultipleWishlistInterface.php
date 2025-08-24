<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for MultipleWishlist Model
 */
interface MultipleWishlistInterface
{
    public const MULTIPLE_WISHLIST_PARAM_NAME = 'multiple_wishlist';
    public const MULTIPLE_WISHLIST_ID = 'multiple_wishlist_id';
    public const WISHLIST_ID = 'wishlist_id';
    public const MULTIPLE_WISHLIST_NAME = 'name';
    public const MULTIPLE_WISHLIST_SHARING_CODE = 'sharing_code';

    /**
     * Returns multiple wishlist id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set multiple wishlist id
     *
     * @param int|string $id
     * @return MultipleWishlistInterface
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
     * Returns multiple wishlist name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set multiple wishlist name
     *
     * @param string $name
     * @return MultipleWishlistInterface
     */
    public function setName(string $name): self;

    /**
     * Returns multiple sharing code
     *
     * @return string
     */
    public function getSharingCode(): ?string;

    /**
     * Set multiple wishlist sharing code
     *
     * @param string $code
     * @return MultipleWishlistInterface
     */
    public function setSharingCode(string $code): self;
}
