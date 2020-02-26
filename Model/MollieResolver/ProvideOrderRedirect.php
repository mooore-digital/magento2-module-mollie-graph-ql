<?php

declare(strict_types=1);

namespace Mooore\MollieGraphQl\Model\MollieResolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
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
     * Create transaction for order and get Mollie payment url.
     *
     * @param string $orderId
     * @return string|null
     * @throws LocalizedException
     * @throws ApiException
     */
    public function getRedirectUrl(string $orderId): ?string
    {
        /** @var Order $order */
        $order = $this->getOrderByIncrementId($orderId);

        if ($order === null) {
            return null;
        }

        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->paymentHelper->getMethodInstance($method);

        if (!$methodInstance instanceof Mollie) {
            return null;
        }

        $transactionResponse = $methodInstance->startTransaction($order);

        if (!$transactionResponse) {
            return null;
        }

        return (string) $transactionResponse;
    }

    /**
     * Get order by increment id.
     * @param string $incrementId
     * @return OrderInterface|null Returns resulting order, null if not found.
     */
    private function getOrderByIncrementId(string $incrementId): ?OrderInterface
    {
        $searchCriteria = $this->criteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orderList = $this->order->getList($searchCriteria)->getItems();

        if (!count($orderList)) {
            return null;
        }

        return array_values($orderList)[0];
    }
}
