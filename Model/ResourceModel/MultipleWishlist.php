<?php

namespace Kamlesh\MultipleWishlist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model class for Multiple Wishlist
 */
class MultipleWishlist extends AbstractDb
{
    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init('multiple_wishlist', 'multiple_wishlist_id');
    }
}
