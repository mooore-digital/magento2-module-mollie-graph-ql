<?php

declare(strict_types=1);

namespace Mooore\MollieGraphQl\Model\MollieResolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Payment\Model\Mollie;

class ProvideOrderRedirect
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var OrderInterface
     */
    private $order;
    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        OrderRepositoryInterface $order,
        PaymentHelper $paymentHelper,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->order = $order;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @param string $orderId
     * @return string|bool|null
     * @throws LocalizedException
     * @throws ApiException
     */
    public function getRedirectUrl(string $orderId)
    {
        $searchCriteria = $this->criteriaBuilder
            ->addFilter('increment_id', $orderId)
            ->create();

        $orders = $this->order->getList($searchCriteria);

        $order = array_first($orders->getItems());

        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->paymentHelper->getMethodInstance($method);

        if (!$methodInstance instanceof Mollie) {
            return null;
        }

        return $methodInstance->startTransaction($order);
    }
}
