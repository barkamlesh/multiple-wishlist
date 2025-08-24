<?php

namespace Kamlesh\MultipleWishlist\Plugin\Block;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Wishlist\Block\Customer\Wishlist as MagentoWishlistBlock;

/**
 * Plugin class for changing add all to cart route
 */
class Wishlist
{
    /**
     * Wishlist Block Plugin constructor.
     * Constructor property promotion used for injected services.
     */
    public function __construct(
        protected RequestInterface $request,
        protected Data $moduleHelper,
        protected Json $json
    ) {
    }

    /**
     * Changes add all to cart route
     *
     * @param MagentoWishlistBlock $subject
     * @param string $result
     * @return string
     */
    public function afterGetAddAllToCartParams(MagentoWishlistBlock $subject, string $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $paramsArray = $this->json->unserialize($result);

        if ($multipleWishlist) {
            $paramsArray['data'][MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        return $this->json->serialize($paramsArray);
    }
}
