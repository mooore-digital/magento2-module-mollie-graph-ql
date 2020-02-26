<?php

declare(strict_types=1);

namespace Mooore\MollieGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mooore\MollieGraphQl\Model\MollieResolver\ProvideOrderRedirect;

class PaymentUrl implements ResolverInterface
{
    /**
     * @var ProvideOrderRedirect
     */
    private $orderRedirect;

    public function __construct(
        ProvideOrderRedirect $orderRedirect
    ) {
        $this->orderRedirect = $orderRedirect;
    }

    /**
     * Load the order and create a mollie payment url
     *
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): ?string
    {
        return $this->orderRedirect->getRedirectUrl($value['order_number']);
    }
}
