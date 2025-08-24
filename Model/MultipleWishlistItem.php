<?php
declare(strict_types=1);
namespace Kamlesh\MultipleWishlist\Model;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Model class for Multiple Wishlist Item
 */
class MultipleWishlistItem extends AbstractExtensibleModel implements IdentityInterface, MultipleWishlistItemInterface
{
    const CACHE_TAG = 'multiple_wishlist_item';

    /**
     * @var string
     */
    protected $_idFieldName = self::PRIMARY_ID;

    /**
     * @var string
     */
    protected $_cacheTag = 'multiple_wishlist_item';

    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_item';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\MultipleWishlistItem::class);
    }

    /**
     * @inheridoc
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getMultipleWishlistId(): ?int
    {
        $val = $this->getData(self::MULTIPLE_WISHLIST_ID);
        return $val === null ? null : (int)$val;
    }

    /**
     * @inheritDoc
     */
    public function setMultipleWishlistId(?int $id): self
    {
        return $this->setData(self::MULTIPLE_WISHLIST_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getWishlistId(): int
    {
        return (int)$this->getData(self::WISHLIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWishlistId(int $id): self
    {
        return $this->setData(self::WISHLIST_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getWishlistItemId(): int
    {
        return (int)$this->getData(self::MULTIPLE_WISHLIST_ITEM);
    }

    /**
     * @inheritDoc
     */
    public function setWishlistItemId(int $id): self
    {
        return $this->setData(self::MULTIPLE_WISHLIST_ITEM, $id);
    }
}
