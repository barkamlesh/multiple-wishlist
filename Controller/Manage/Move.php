<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller\Manage;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Kamlesh\MultipleWishlist\Controller\AbstractManage;
use Kamlesh\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Psr\Log\LoggerInterface;

/**
 * Controller for moving items to different wishlists
 */
class Move extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $multipleWishlistItemRepository;

    /**
     * Move constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param LoggerInterface $logger
     * @param Validator $formKeyValidator
     * @param WishlistHelper $wishlistHelper
     * @param Data $moduleHelper
     * @param MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        LoggerInterface $logger,
        Validator $formKeyValidator,
        WishlistHelper $wishlistHelper,
        Data $moduleHelper,
        MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository
    ) {
        parent::__construct(
            $context,
            $urlBuilder,
            $formKeyValidator,
            $multipleWishlistRepository
        );
        $this->logger = $logger;
        $this->moduleHelper = $moduleHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->multipleWishlistItemRepository = $multipleWishlistItemRepository;
    }

    /**
     * Move all items from one wishlist to another
     *
     * @param int $prevWishlist
     * @param int $newWishlist
     * @return bool
     */
    protected function moveAllItems(int $prevWishlist, int $newWishlist): bool
    {
        $items = $this->moduleHelper->getMultipleWishlistItems($prevWishlist);
        foreach ($items as $item) {
            $item->setMultipleWishlistId($newWishlist);
            try {
                $this->multipleWishlistItemRepository->save($item);
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Move one item to the different wishlist
     *
     * @param int $newWishlist
     * @param int $itemId
     * @return bool
     */
    protected function moveItem(int $newWishlist, int $itemId): bool
    {
        try {
            $item = $this->multipleWishlistItemRepository->getByWishlistItemId($itemId);
            $item->setMultipleWishlistId($newWishlist);
            $this->multipleWishlistItemRepository->save($item);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Process multiple wishlist item(s) move
     *
     * @throws NoSuchEntityException
     * @return Json|Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $wishlistId = $this->wishlistHelper->getWishlist()->getId();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processReturn(
                __('Invalid Form Key. Please refresh the page.'),
                false
            );
        }

        if (!$wishlistId) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $multipleWishlist = isset($params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME])
            ? (int)$params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME]
            : null;
        $previousWishlist = isset($params['prev']) ? (int)$params['prev'] : null;
        if ($multipleWishlist === $previousWishlist) {
            return $this->processReturn(
                __('Can\'t move items to the same wishlist.'),
                false
            );
        }

        if (isset($params['item_id']) && is_numeric($params['item_id'])) {
            $result = $this->moveItem((int)$multipleWishlist, (int)$params['item_id']);
        } else {
            $result = $this->moveAllItems((int)$previousWishlist, (int)$multipleWishlist);
        }

        if (!$result) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $this->moduleHelper->recalculateWishlistItems($multipleWishlist);

        return $this->processReturn(
            __('Items have been successfully moved.'),
            true,
            $this->urlBuilder->getUrl('wishlist', [
                MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
            ])
        );
    }
}
