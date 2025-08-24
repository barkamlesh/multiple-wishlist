<?php

namespace Kamlesh\MultipleWishlist\Plugin\Block;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Block\Customer\Sharing as MagentoSharingBlock;

/**
 * Plugin class for adding multiple wishlist id to the send url
 */
class Sharing
{
    /**
     * Sharing Block Plugin constructor.
     * Constructor property promotion used for injected services.
     */
    public function __construct(
        protected RequestInterface $request,
        protected UrlInterface $urlBuilder,
        protected Data $moduleHelper
    ) {
    }

    /**
     * Add multiple wishlist param to the send url
     *
     * @param MagentoSharingBlock $subject
     * @param $result
     * @return string
     */
    public function afterGetSendUrl(MagentoSharingBlock $subject, $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        return $this->urlBuilder->getUrl('wishlist/index/send', [
            MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
        ]);
    }
}
