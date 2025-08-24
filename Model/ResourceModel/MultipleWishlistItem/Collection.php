<?php

namespace Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem;

use Kamlesh\MultipleWishlist\Model\MultipleWishlistItem as MultipleWishlistItemModel;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem as MultipleWishlistItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection class for Multiple Wishlist Item
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_item_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'multiple_wishlist_item_collection';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(MultipleWishlistItemModel::class, MultipleWishlistItemResource::class);
    }
}
