<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller\Manage;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Wishlist\Controller\AbstractIndex;

/**
 * Multiple wishlist new controller
 */
class NewAction extends AbstractIndex implements HttpGetActionInterface
{
    /**
     * Forwards to edit route
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
