<?php
declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Helper;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Module's helper class
 */
class Data extends AbstractHelper
{
    /**
     * Helper constants
     */
    const XML_PATH_ENABLED = 'wishlist/multiple_wishlist_general/enabled';
    const XML_PATH_STRATEGY = 'wishlist/multiple_wishlist_general/wishlist_strategy';
    const XML_PATH_LIMIT = 'wishlist/multiple_wishlist_general/wishlist_limit';
    const DEFAULT_LIMIT = 20;
    const MAX_LIMIT = 100;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Item
     */
    protected $mainItemResource;

    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var CollectionFactory
     */
    protected $wishlistCollectionFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param LoggerInterface $logger
     * @param WishlistHelper $wishlistHelper
     * @param CollectionFactory $wishlistCollectionFactory
     * @param Item $mainItemResource
     */
    /**
     * @param Context $context
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param LoggerInterface $logger
     * @param WishlistHelper $wishlistHelper
     * @param CollectionFactory $wishlistCollectionFactory Generated via DI
     * @param Item $mainItemResource
     */
    public function __construct(
        Context $context,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        LoggerInterface $logger,
        WishlistHelper $wishlistHelper,
        CollectionFactory $wishlistCollectionFactory,
        Item $mainItemResource
    ) {
        parent::__construct($context);
        $this->itemRepository = $itemRepository;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->wishlistHelper = $wishlistHelper;
        /** @var CollectionFactory $wishlistCollectionFactory */
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->mainItemResource = $mainItemResource;
    }

    /**
     * Checks if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks if wishlist modal can be shown or not
     *
     * @return bool
     */
    public function canShowModal(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_STRATEGY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns wishlist limit number
     *
     * @return int
     */
    public function getWishlistLimit(): int
    {
        $limit = $this->scopeConfig->getValue(self::XML_PATH_LIMIT, ScopeInterface::SCOPE_STORE);
        if (!is_numeric($limit) || $limit < 0) {
            return self::DEFAULT_LIMIT;
        }

        if ($limit > self::MAX_LIMIT) {
            return self::MAX_LIMIT;
        }

        return (int)$limit;
    }

    /**
     * Returns filtered multiple wishlist item collection
     *
     * @param int|null $wishlist
     * @param null|int $itemId
     * @return array
     */
    public function getMultipleWishlistItems(?int $wishlist = null, ?int $itemId = null): array
    {
        // If $wishlist is null, do not filter by multiple wishlist id.
        if ($wishlist !== null) {
            $this->searchCriteriaBuilder->addFilter(
                MultipleWishlistItemInterface::WISHLIST_ID,
                $this->wishlistHelper->getWishlist()->getId()
            );

            $this->searchCriteriaBuilder->addFilter(
                MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ID,
                $wishlist,
                $wishlist ? 'eq' : 'null'
            );
        }

        if ($itemId) {
            $this->searchCriteriaBuilder->addFilter(
                MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ITEM,
                $itemId
            );
        }

        return $this->itemRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * Returns number of items in the current wishlist
     *
     * @param int|null $multipleWishlistId
     * @throws NoSuchEntityException
     * @return int
     */
    public function countMultipleWishlistItems(?int $multipleWishlistId = null): int
    {
        $items = $this->getMultipleWishlistItems($multipleWishlistId);

        if (!$this->scopeConfig->getValue(
            WishlistHelper::XML_PATH_WISHLIST_LINK_USE_QTY,
            ScopeInterface::SCOPE_STORE
        )) {
            return count($this->getMultipleWishlistItems($multipleWishlistId));
        }

        return $this->getItemCollectionByItemIds($items)->getItemsQty();
    }

    /**
     * Return wishlist item collection filtered by given ids
     *
     * @param $multipleWishlistItems
     * @throws NoSuchEntityException
     * @return Collection
     */
    public function getItemCollectionByItemIds(?array $multipleWishlistItems = null): Collection
    {
        $wishlist = $this->wishlistHelper->getWishlist();
        $collection = $this->wishlistCollectionFactory->create()->addWishlistFilter(
            $wishlist
        )->addStoreFilter(
            $wishlist->getSharedStoreIds()
        )->setVisibilityFilter()->setInStockFilter(
            true
        );

        if (is_array($multipleWishlistItems)) {
            $ids = [];
            foreach ($multipleWishlistItems as $item) {
                $ids[] = $item->getWishlistItemId();
            }

            $collection->addFieldToFilter('wishlist_item_id', ['in' => $ids]);
        }

        return $collection;
    }

    /**
     * Compares options of 2 wishlist items to check if they can be merged
     *
     * @return bool|int
     */
    protected function compareOptions(array $existingOptions, array $optionsToCompare): bool|int
    {
        $compareOptions = [];
        foreach ($optionsToCompare as $option) {
            if ($option->getCode() !== 'info_buyRequest') {
                $compareOptions[$option->getCode()] = $option->getValue();
            }
        }

        foreach ($existingOptions as $item => $options) {
            if (!count(array_diff($options, $compareOptions))) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Updates wishlist item qty
     *
     * @param $itemCollection
     * @param $itemQtyMapper
     * @return void
     */
    protected function updateItems(Collection $itemCollection, array $itemQtyMapper): void
    {
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($itemCollection as $item) {
            if (isset($itemQtyMapper[$item->getId()])) {
                $item->setQty($item->getQty() + array_sum($itemQtyMapper[$item->getId()]));

                try {
                    $this->mainItemResource->save($item);
                } catch (AlreadyExistsException $e) {
                    $this->logger->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    /**
     * Merges duplicate products in the default wishlist
     *
     * @param int $wishlistId
     * @throws NoSuchEntityException
     * @return void
     */
    public function recalculateWishlistItems(int $wishlistId): void
    {
        $defaultWishlistItems = $this->getMultipleWishlistItems($wishlistId);
        $itemCollection = $this->getItemCollectionByItemIds($defaultWishlistItems);
        $options = [];
        $itemQtyMapper = [];

        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($itemCollection as $item) {
            $itemOptions = $item->getOptionsByCode();
            $productId = $item->getProductId();

            if (isset($options[$productId]) &&
                $existingItem = $this->compareOptions($options[$productId], $itemOptions)) {
                $itemQtyMapper[$existingItem][] = $item->getQty();

                try {
                    $this->mainItemResource->delete($item);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }

                continue;
            }

            if (count($itemOptions) === 1 &&
                array_shift($itemOptions)->getCode() === 'info_buyRequest') {
                $options[$productId][$item->getId()] = [];
                continue;
            }

            foreach ($itemOptions as $option) {
                if ($option->getCode() !== 'info_buyRequest') {
                    $options[$productId][$item->getId()][$option->getCode()] = $option->getValue();
                }
            }
        }

        $this->updateItems($itemCollection, $itemQtyMapper);
    }

    /**
     * Returns multiple wishlists for a given wishlist id
     *
     * @param int $wishlistId
     * @return MultipleWishlistInterface[]
     */
    public function getAllMultipleWishlists(int $wishlistId): array
    {
        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistInterface::WISHLIST_ID,
            $wishlistId
        );

        return $this->multipleWishlistRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();
    }

    /**
     * Returns multiple wishlist for a given id
     *
     * @param int $wishlistId
     * @return MultipleWishlistInterface
     */
    public function getMultipleWishlist(?int $wishlistId): ?MultipleWishlistInterface
    {
        $multipleWishlist = null;

        if ($wishlistId !== null) {
            try {
                $multipleWishlist = $this->multipleWishlistRepository->get($wishlistId);
            } catch (NoSuchEntityException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $multipleWishlist instanceof MultipleWishlistInterface ? $multipleWishlist : null;
    }
}
