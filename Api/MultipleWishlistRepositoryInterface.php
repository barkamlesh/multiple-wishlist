<?php

namespace Kamlesh\MultipleWishlist\Api;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistSearchResultsInterface;
use Kamlesh\MultipleWishlist\Model\MultipleWishlist;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface for MultipleWishlistRepository
 */
interface MultipleWishlistRepositoryInterface
{
    /**
     * Save multiple wishlist data
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotSaveException
     * @return MultipleWishlist
     */
    public function save(MultipleWishlistInterface $multipleWishlist);

    /**
     * Load multiple wishlist by id
     * @param int $multipleWishlistId
     * @throws NoSuchEntityException
     * @return MultipleWishlist
     */
    public function get(int $multipleWishlistId);

    /**
     * Load multiple wishlist by sharing code
     * @param string $code
     * @throws NoSuchEntityException
     * @return MultipleWishlist
     */
    public function getByCode(string $code);

    /**
     * Load multiple wishlist data collection by given search criteria
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete multiple wishlist
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistInterface $multipleWishlist);

    /**
     * Delete multiple wishlist by id
     * @param int $multipleWishlistId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return bool
     */
    public function deleteById(int $multipleWishlistId);
}
