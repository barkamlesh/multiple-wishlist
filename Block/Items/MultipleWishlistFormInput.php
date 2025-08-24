<?php

namespace Kamlesh\MultipleWishlist\Block\Items;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Magento\Wishlist\Block\Customer\Wishlist\Item\Column;

class MultipleWishlistFormInput extends Column
{
    /**
     * Returns multiple wishlist input for the wishlist form
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        $paramName = MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME;
        $multipleWishlistId = $this->getRequest()->getParam($paramName);

        return '<input type="hidden" name="' . $paramName . '" value="' . $multipleWishlistId . '">';
    }
}
