<?php
declare(strict_types=1);
namespace Kamlesh\MultipleWishlist\Model;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Model class for Multiple Wishlist
 */
class MultipleWishlist extends AbstractExtensibleModel implements MultipleWishlistInterface, IdentityInterface
{
    const CACHE_TAG = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_idFieldName = self::MULTIPLE_WISHLIST_ID;

    /**
     * @var string
     */
    protected $_cacheTag = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\MultipleWishlist::class);
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
    public function getName(): string
    {
        return (string)$this->getData(self::MULTIPLE_WISHLIST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        return $this->setData(self::MULTIPLE_WISHLIST_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getSharingCode(): ?string
    {
        return $this->getData(self::MULTIPLE_WISHLIST_SHARING_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSharingCode(string $code): self
    {
        return $this->setData(self::MULTIPLE_WISHLIST_SHARING_CODE, $code);
    }
}
