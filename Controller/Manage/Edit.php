<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller\Manage;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\AbstractIndex;

/**
 * Multiple wishlist edit controller
 */
class Edit extends AbstractIndex implements HttpGetActionInterface
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        Data $moduleHelper
    ) {
        parent::__construct($context);
        $this->moduleHelper = $moduleHelper;
    }

    public function execute()
    {
        if (!$this->moduleHelper->isEnabled()) {
            $this->_forward('noroute');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $multipleWishlistId = $this->getRequest()->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        // Normalize param to nullable int to satisfy strict helper signature
        if ($multipleWishlistId === null || $multipleWishlistId === '') {
            $multipleWishlistId = null;
        } else {
            $multipleWishlistId = (int)$multipleWishlistId;
        }

        if ($multipleWishlistId) {
            $multipleWishlist = $this->moduleHelper->getMultipleWishlist($multipleWishlistId);
            $resultPage->getConfig()->getTitle()->set(
                __('Edit Wishlist: %1', $multipleWishlist ? $multipleWishlist->getName() : '')
            );
        } else {
            $resultPage->getConfig()->getTitle()->set(__('Create New Wishlist'));
        }

        return $resultPage;
    }
}
