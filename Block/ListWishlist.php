<?php
declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Block;

use Kamlesh\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Kamlesh\MultipleWishlist\Helper\Data as ModuleData;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection;
use Kamlesh\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Wishlist\Helper\Data;

//@TODO Check admin wishlist rendering

/**
 * Block class for multiple wishlist list rendering
 */
class ListWishlist extends Template
{
    /**
     * @var Data
     */
    protected $wishlistHelper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ModuleData
     */
    protected $moduleHelper;

    /**
     * @var PostHelper
     */
    protected $postHelper;

    /**
     * ListWishlist constructor.
     *
     * @param Template\Context $context
     * @param Data $wishlistHelper
     * @param CollectionFactory $collectionFactory
     * @param ModuleData $moduleHelper
     * @param PostHelper $postHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $wishlistHelper,
        CollectionFactory $collectionFactory,
        ModuleData $moduleHelper,
        PostHelper $postHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->wishlistHelper = $wishlistHelper;
        $this->collectionFactory = $collectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->postHelper = $postHelper;
    }

    /**
     * Prepares the multiple wishlist collection
     *
     * @return void
     */
    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();

        $block = $this->getChildBlock('multiple.wishlist.pager');

        if ($block instanceof DataObject) {
            $block->setCollection(
                $this->getCollection()
            );
        }
    }

    /**
     * Returns number of products in the multiple wishlist
     *
     * @param int|null $wishlistId If null, current wishlist will be used
     * @throws NoSuchEntityException
     * @return int
     */
    public function countItems(?int $wishlistId = null): int
    {
        if ($wishlistId === null) {
            $wishlist = $this->wishlistHelper->getWishlist();
            $wishlistId = $wishlist ? (int)$wishlist->getId() : 0;
        }

        return $this->moduleHelper->countMultipleWishlistItems($wishlistId);
    }

    /**
     * Returns post data for multiple wishlist removal
     *
     * @param int $id
     * @return string
     */
    public function getRemoveUrl(int $id): string
    {
        $data = [
            'id' => $id,
            'confirmation' => true,
            'confirmationMessage' => __(
                'Are you sure you want to remove wishlist? All items will be moved to the default wishlist.'
            )
        ];
        return $this->postHelper->getPostData($this->getUrl('multiplewishlist/manage/delete'), $data);
    }

    /**
     * Returns multiple wishlists
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        if ($this->collection === null) {
            $wishlistId = $this->wishlistHelper->getWishlist()->getId();
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter(MultipleWishlistInterface::WISHLIST_ID, $wishlistId)
                ->setOrder(MultipleWishlistInterface::MULTIPLE_WISHLIST_ID, 'desc');
        }

        return $this->collection;
    }
}
