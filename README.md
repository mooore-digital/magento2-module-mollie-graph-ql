# Mollie GraphQl

GraphQl support for Mollie Magento 2 module.

## Installation
```shell script
composer require mooore/magento2-module-mollie-graph-ql
bin/magento setup:upgrade
```

## Usage
```graphql
mutation($cartId: String!) {
  placeOrder(input: { cart_id: $cartId }) {
    order {
      order_number
      payment_url
    }
  }
}
```

The `payment_url` attribute is added to the `CreateOrder` mutation. Using the `payment_url` attribute, the customer can be redirected to iDeal after placing an order.

This modules does not support any payment methods other than Mollie. 
