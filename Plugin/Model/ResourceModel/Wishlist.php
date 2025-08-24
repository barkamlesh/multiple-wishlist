<?php

namespace Kamlesh\MultipleWishlist\Plugin\Model\ResourceModel;

use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Wishlist\Model\ResourceModel\Wishlist as MagentoWishlistResource;

/**
 * Class for changing sharing code before save
 */
class Wishlist
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Wishlist Resource Model Plugin constructor.
     *
     * @param Data $moduleHelper
     */
    public function __construct(Data $moduleHelper)
    {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Handle saving of the sharing code
     *
     * @param MagentoWishlistResource $subject
     * @param AbstractModel $object
     * @return array
     */
    public function beforeSave(MagentoWishlistResource $subject, AbstractModel $object)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return [$object];
        }

        if ($code = $object->getTempSharingCode()) {
            $object->setSharingCode($code);
        }

        return [$object];
    }
}
