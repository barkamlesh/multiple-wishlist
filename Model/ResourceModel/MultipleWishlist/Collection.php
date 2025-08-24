<?php

namespace Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist;

use Kamlesh\MultipleWishlist\Model\MultipleWishlist as MultipleWishlistModel;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist as MultipleWishlistResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection class for Multiple Wishlist
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'multiple_wishlist_collection';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(MultipleWishlistModel::class, MultipleWishlistResource::class);
    }
}
