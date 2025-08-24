<?php

namespace Kamlesh\MultipleWishlist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model class for Multiple Wishlist Item
 */
class MultipleWishlistItem extends AbstractDb
{
    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init('multiple_wishlist_item', 'item_id');
    }
}
