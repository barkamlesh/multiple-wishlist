<?php
declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller\Manage;

use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Wishlist\Controller\AbstractIndex;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Multiple wishlist index controller
 */
class Index extends AbstractIndex implements HttpGetActionInterface
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
        $resultPage->getConfig()->getTitle()->set(__('Manage Wishlists'));
        return $resultPage;
    }
}
