<?php

namespace Kamlesh\MultipleWishlist\ViewModel;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Module's view model class
 */
class MultipleWishlist implements ArgumentInterface
{
    /**
     * MultipleWishlist constructor.
     *
     * Constructor property promotion used for injected services.
     */
    public function __construct(
        protected Session $customerSession,
        protected Data $moduleHelper,
        protected UrlInterface $urlBuilder,
        protected RequestInterface $request
    ) {
    }

    /**
     * Checks if customer is logged in
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->getCustomerGroupId() !== Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checks if modal can be shown
     *
     * @return bool
     */
    public function canShowModal()
    {
        return $this->moduleHelper->canShowModal();
    }

    /**
     * Returns form url for multiple wishlist create|edit
     *
     * @return string
     */
    public function getEditPostUrl()
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        if ($multipleWishlist) {
            return $this->urlBuilder->getUrl('multiplewishlist/manage/editpost');
        }

        return $this->urlBuilder->getUrl('multiplewishlist/manage/create');
    }

    /**
     * Returns form url for wishlist items move functionality
     *
     * @return string
     */
    public function getMovePostUrl()
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $params = [];

        if ($multipleWishlist) {
            $params['prev'] = $multipleWishlist;
        }

        return $this->urlBuilder->getUrl('multiplewishlist/manage/move', $params);
    }

    /**
     * Returns wishlist name for the multiple wishlist manage form
     *
     * @return array
     */
    public function getManageFormInputValues()
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $wishlist = $this->moduleHelper->getMultipleWishlist($multipleWishlist);

        if (!$wishlist) {
            return [];
        }

        return [
            'id' => $wishlist->getId(),
            'name' => $wishlist->getName()
        ];
    }
}
