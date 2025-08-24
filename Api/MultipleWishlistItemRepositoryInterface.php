<?php

namespace Kamlesh\MultipleWishlist\Api;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistItemSearchResultsInterface;
use Kamlesh\MultipleWishlist\Model\MultipleWishlistItem;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface for MultipleWishlistItemRepository
 */
interface MultipleWishlistItemRepositoryInterface
{
    /**
     * Save multiple wishlist item data
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotSaveException
     * @return MultipleWishlistItem
     */
    public function save(MultipleWishlistItemInterface $multipleWishlistItem);

    /**
     * Load multiple wishlist item by id
     * @param int $multipleWishlistItemId
     * @throws NoSuchEntityException
     * @return MultipleWishlistItem
     */
    public function get(int $multipleWishlistItemId);

    /**
     * Load multiple wishlist item by main wishlist item id
     * @param int $wishlistItemId
     * @throws NoSuchEntityException
     * @return MultipleWishlistItem
     */
    public function getByWishlistItemId(int $wishlistItemId);

    /**
     * Load multiple wishlist item data collection by given search criteria
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete multiple wishlist item
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistItemInterface $multipleWishlistItem);

    /**
     * Delete multiple wishlist item by id
     * @param int $multipleWishlistItemId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return bool
     */
    public function deleteById(int $multipleWishlistItemId);
}
