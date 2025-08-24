<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Model;

use Kamlesh\MultipleWishlist\Model\MultipleWishlistSearchResultsFactory;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistSearchResultsInterface;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist as MultipleWishlistResource;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory as MultipleWishlistCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Kamlesh\MultipleWishlist\Model\MultipleWishlistFactory;

/**
 * Class MultipleWishlistRepository
 */
class MultipleWishlistRepository implements MultipleWishlistRepositoryInterface
{
    /**
     * MultipleWishlistRepository constructor.
     *
     * Using constructor property promotion to reduce boilerplate.
     *
     * @param MultipleWishlistSearchResultsFactory $searchResultsFactory
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistCollection $collectionFactory
     * @param MultipleWishlistResource $resource
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        protected MultipleWishlistSearchResultsFactory $searchResultsFactory,
        protected MultipleWishlistFactory $multipleWishlistFactory,
        protected MultipleWishlistCollection $collectionFactory,
        protected MultipleWishlistResource $resource,
        protected CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * Save multiple wishlist data
     *
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotSaveException
     * @return MultipleWishlist
     */
    public function save(MultipleWishlistInterface $multipleWishlist)
    {
        try {
            if (!$multipleWishlist instanceof MultipleWishlist) {
                throw new CouldNotSaveException(__('Unexpected model instance provided.'));
            }
            $this->resource->save($multipleWishlist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $multipleWishlist;
    }

    /**
     * Load multiple wishlist by id
     *
     * @param int $multipleWishlistId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlist
     */
    public function get(int $multipleWishlistId)
    {
        $multipleWishlist = $this->multipleWishlistFactory->create();
        $this->resource->load($multipleWishlist, $multipleWishlistId);

        if (!$multipleWishlist->getId()) {
            throw new NoSuchEntityException(__('The multiple wishlist record with the "%1" ID doesn\'t exist.', $multipleWishlistId));
        }

        return $multipleWishlist;
    }

    /**
     * Load multiple wishlist by sharing code
     *
     * @param string $code
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlist
     */
    public function getByCode(string $code)
    {
        $multipleWishlist = $this->multipleWishlistFactory->create();
        $this->resource->load($multipleWishlist, $code, MultipleWishlistInterface::MULTIPLE_WISHLIST_SHARING_CODE);

        if (!$multipleWishlist->getId()) {
            throw new NoSuchEntityException(
                __('The multiple wishlist record with the "%1" code doesn\'t exist.', $code)
            );
        }

        return $multipleWishlist;
    }

    /**
     * Load multiple wishlist data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistSearchResultsInterface
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
     * Delete multiple wishlist
     *
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistInterface $multipleWishlist)
    {
        try {
            if (!$multipleWishlist instanceof MultipleWishlist) {
                throw new CouldNotDeleteException(__('Unexpected model instance provided.'));
            }
            $this->resource->delete($multipleWishlist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete multiple wishlist by ID
     *
     * @param int $multipleWishlistId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function deleteById(int $multipleWishlistId)
    {
        return $this->delete($this->get($multipleWishlistId));
    }
}
