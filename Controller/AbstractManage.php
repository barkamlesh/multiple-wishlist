<?php

declare(strict_types=1);

namespace Kamlesh\MultipleWishlist\Controller;

use Kamlesh\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;

/**
 * Abstract class for multiple wishlist manage controllers
 */
abstract class AbstractManage extends Action
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * AbstractManage constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Validator $formKeyValidator
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Validator $formKeyValidator,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository
    ) {
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->formKeyValidator = $formKeyValidator;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
    }

    /**
     * Process request
     *
     * @param $message
     * @param bool $success
     * @param null|string $referer
     * @return Json|Redirect
     */
    protected function processReturn(Phrase|string $message, bool $success = true, ?string $referer = null): Json|Redirect
    {
        // AJAX branch
        if ($this->getRequest()->getParam('ajax')) {
            /** @var Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData([
                'success' => $success,
                'message' => (string)$message
            ]);
            return $resultJson;
        }

        // Non-AJAX branch
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$success) {
            $this->messageManager->addErrorMessage($message);
            $redirect->setPath($this->_redirect->getRefererUrl());
            return $redirect;
        }

        $this->messageManager->addSuccessMessage($message);
        if ($referer) {
            $redirect->setPath($referer);
            return $redirect;
        }

        $redirect->setPath($this->urlBuilder->getUrl('*/*/index'));
        return $redirect;
    }
}
