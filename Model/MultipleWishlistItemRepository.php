<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Model;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemSearchResultsInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use Kamlesh\MultipleWishlist\Model\MultipleWishlistItem;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem as MultipleWishlistItemResource;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem\CollectionFactory as MultipleWishlistItemCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Kamlesh\MultipleWishlist\Model\MultipleWishlistItemFactory;

/**
 * Class MultipleWishlistItemRepository
 */
class MultipleWishlistItemRepository implements MultipleWishlistItemRepositoryInterface
{
    /**
     * @var MultipleWishlistItemResource
     */
    protected $resource;

    /**
     * @var MultipleWishlistItemFactory
     */
    protected $multipleWishlistItemFactory;

    /**
     * @var MultipleWishlistItemCollection
     */
    protected $collectionFactory;

    /**
     * @var MultipleWishlistItemSearchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * MultipleWishlistItemRepository constructor.
     * @param MultipleWishlistItemSearchResultsFactory $searchResultsFactory
     * @param MultipleWishlistItemFactory $multipleWishlistItemFactory
     * @param MultipleWishlistItemCollection $collectionFactory
     * @param MultipleWishlistItemResource $resource
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        MultipleWishlistItemSearchResultsFactory $searchResultsFactory,
        MultipleWishlistItemFactory $multipleWishlistItemFactory,
        MultipleWishlistItemCollection $collectionFactory,
        MultipleWishlistItemResource $resource,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->multipleWishlistItemFactory = $multipleWishlistItemFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save multiple wishlist item data
     *
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotSaveException
     * @return MultipleWishlistItem
     */
    public function save(MultipleWishlistItemInterface $multipleWishlistItem)
    {
        try {
            // Ensure we operate on a concrete model instance for the resource model
            if (!$multipleWishlistItem instanceof MultipleWishlistItem) {
                throw new CouldNotSaveException(__('Unexpected model instance provided.'));
            }
            $this->resource->save($multipleWishlistItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $multipleWishlistItem;
    }

    /**
     * Load multiple wishlist item by id
     *
     * @param int $multipleWishlistItemId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlistItem
     */
    public function get(int $multipleWishlistItemId)
    {
        $multipleWishlistItem = $this->multipleWishlistItemFactory->create();
        $this->resource->load($multipleWishlistItem, $multipleWishlistItemId);

        if (!$multipleWishlistItem->getId()) {
            throw new NoSuchEntityException(__(
                'The multiple wishlist item record with the "%1" ID doesn\'t exist.',
                $multipleWishlistItemId
            ));
        }

        return $multipleWishlistItem;
    }

    /**
     * Load multiple wishlist item by main wishlist item id
     *
     * @param int $wishlistItemId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlistItem
     */
    public function getByWishlistItemId(int $wishlistItemId)
    {
        $multipleWishlistItem = $this->multipleWishlistItemFactory->create();
        $this->resource->load($multipleWishlistItem, $wishlistItemId, 'wishlist_item_id');

        if (!$multipleWishlistItem->getId()) {
            throw new NoSuchEntityException(__(
                'The multiple wishlist item record with doesn\'t exist.'
            ));
        }

        return $multipleWishlistItem;
    }

    /**
     * Load multiple wishlist item data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete multiple wishlist item
     *
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistItemInterface $multipleWishlistItem)
    {
        try {
            if (!$multipleWishlistItem instanceof MultipleWishlistItem) {
                throw new CouldNotDeleteException(__('Unexpected model instance provided.'));
            }
            $this->resource->delete($multipleWishlistItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete multiple wishlist item by ID
     *
     * @param int $multipleWishlistItemId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function deleteById(int $multipleWishlistItemId)
    {
        return $this->delete($this->get($multipleWishlistItemId));
    }
}
