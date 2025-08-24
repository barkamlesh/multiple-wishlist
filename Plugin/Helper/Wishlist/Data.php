<?php

namespace Kamlesh\MultipleWishlist\Plugin\Helper\Wishlist;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data as ModuleHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Plugin class for changing add to cart params in the url
 */
class Data
{
    /**
     * Helper Plugin constructor.
     * Constructor property promotion used for injected services.
     */
    public function __construct(
        protected ModuleHelper $moduleHelper,
        protected RequestInterface $request,
        protected Json $json,
        protected UrlInterface $urlBuilder
    ) {
    }

    /**
     * Add multiple wishlist id to the add to cart params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @param false $addReferer
     * @return string
     */
    public function afterGetAddToCartParams(WishlistHelper $subject, $result, $item, $addReferer = false)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $params = $this->addWishlistParam($result);
        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the wishlist url
     *
     * @param WishlistHelper $subject
     * @param string $result
     * @param int $wishlistId
     * @return string
     */
    public function afterGetListUrl(WishlistHelper $subject, $result, $wishlistId = null)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        if (!$multipleWishlist) {
            return $result;
        }

        $params = [];
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }

        $params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        return $this->urlBuilder->getUrl('wishlist', $params);
    }

    /**
     * Add multiple wishlist id and qty to the update url params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @return string
     */
    public function afterGetUpdateParams(WishlistHelper $subject, $result, $item)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $params = $this->addWishlistParam($result);
        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the delete params and change url
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @param false $addReferer
     * @return mixed
     */
    public function afterGetRemoveParams(WishlistHelper $subject, $result, $item, $addReferer = false)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $params = $this->addWishlistParam($result);
        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the configure url params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @return string
     */
    public function afterGetConfigureUrl(WishlistHelper $subject, $result, $item)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        if ($multipleWishlist) {
            $result = $this->urlBuilder->getUrl(
                'wishlist/index/configure',
                [
                    'id' => $item->getWishlistItemId(),
                    'product_id' => $item->getProductId(),
                    MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
                ]
            );
        }

        return $result;
    }

    /**
     * Adds param to the array
     *
     * @param $params
     * @return array
     */
    protected function addWishlistParam($params)
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $paramsArray = $this->json->unserialize($params);
        if ($multipleWishlist) {
            $paramsArray['data'][MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        return $paramsArray;
    }
}
