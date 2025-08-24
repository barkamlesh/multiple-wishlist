<?php
declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller\Manage;

use Kamlesh\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Kamlesh\MultipleWishlist\Controller\AbstractManage;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

/**
 * Controller for multiple wishlist deletion
 */
class Delete extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Delete constructor.
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Validator $formKeyValidator
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Validator $formKeyValidator,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        Data $moduleHelper
    ) {
        parent::__construct(
            $context,
            $urlBuilder,
            $formKeyValidator,
            $multipleWishlistRepository
        );
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Process multiple wishlist removal
     *
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processReturn(
                __('Invalid Form Key. Please refresh the page.'),
                false
            );
        }

        if (!isset($params['id']) || !is_numeric($params['id'])) {
            return $this->processReturn(
                __('Required data missing!'),
                false
            );
        }

        try {
            $this->multipleWishlistRepository->deleteById($params['id']);
        } catch (CouldNotDeleteException $e) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        } catch (NoSuchEntityException $e) {
            return $this->processReturn(
                __('Wishlist doesn\'t exist.'),
                false
            );
        } catch (LocalizedException $e) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $this->moduleHelper->recalculateWishlistItems((int)($params['id'] ?? 0)); // trigger merge on default wishlist

        return $this->processReturn(
            __('Wishlist has been successfully removed.'),
            true
        );
    }
}
