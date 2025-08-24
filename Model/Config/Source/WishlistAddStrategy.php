<?php

namespace Kamlesh\MultipleWishlist\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provides options for the system configuration field
 */
class WishlistAddStrategy implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Add to default wishlist')],
            ['value' => 1, 'label' => __('Show modal and choose wishlist')]
        ];
    }
}
