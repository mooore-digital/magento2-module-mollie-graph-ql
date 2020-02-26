<?php

declare(strict_types=1);

namespace Mooore\MollieGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Tests\NamingConvention\true\mixed;
use Mollie\Payment\Model\Mollie;

class PaymentUrl implements ResolverInterface
{

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var Order
     */
    private $order;

    public function __construct(
        OrderInterface $order,
        PaymentHelper $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->order = $order;
    }

    /**
     * Load the order and create a mollie payment url
     *
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): mixed
    {
        $order = $this->order->loadByIncrementId($value['order_number']);

        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->paymentHelper->getMethodInstance($method);

        if (!$methodInstance instanceof Mollie) {
            return null;
        }

        return $methodInstance->startTransaction($order);
    }
}
