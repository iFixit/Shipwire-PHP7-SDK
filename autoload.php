<?php

require 'vendor/autoload.php';

$classes = [
   'CharityRoot\\Shipwire' => '/src/shipwire.php',
   'CharityRoot\\ShipwireAddress' => '/src/shipwire/shipwire.address.php',
   'CharityRoot\\ShipwireCarrier' => '/src/shipwire/shipwire.carrier.php',
   'CharityRoot\\ShipwireCollection' => '/src/shipwire/shipwire.collection.php',
   'CharityRoot\\ShipwireErrors' => '/src/shipwire/shipwire.errors.php',
   'CharityRoot\\ShipwireException' => '/src/shipwire/shipwire.exception.php',
   'CharityRoot\\ShipwireInventory' => '/src/shipwire/shipwire.inventory.php',
   'CharityRoot\\ShipwireItem' => '/src/shipwire/shipwire.item.php',
   'CharityRoot\\ShipwireItems' => '/src/shipwire/shipwire.items.php',
   'CharityRoot\\ShipwireIterator' => '/src/shipwire/shipwire.iterator.php',
   'CharityRoot\\ShipwireMoney' => '/src/shipwire/shipwire.money.php',
   'CharityRoot\\ShipwireOptions' => '/src/shipwire/shipwire.options.php',
   'CharityRoot\\ShipwireOrder' => '/src/shipwire/shipwire.order.php',
   'CharityRoot\\ShipwireOrderCommericalInvoice' => '/src/shipwire/order/shipwire.order.commercialinvoice.php',
   'CharityRoot\\ShipwireOrderEvents' => '/src/shipwire/order/shipwire.order.events.php',
   'CharityRoot\\ShipwireOrderItem' => '/src/shipwire/order/shipwire.order.item.php',
   'CharityRoot\\ShipwireOrderPackingList' => '/src/shipwire/order/shipwire.order.packinglist.php',
   'CharityRoot\\ShipwireOrderPricing' => '/src/shipwire/order/shipwire.order.pricing.php',
   'CharityRoot\\ShipwireOrderRouting' => '/src/shipwire/order/shipwire.order.routing.php',
   'CharityRoot\\ShipwireProduct' => '/src/shipwire/shipwire.product.php',
   'CharityRoot\\ShipwireProductDimensions' => '/src/shipwire/product/shipwire.product.dimensions.php',
   'CharityRoot\\ShipwireProductFlags' => '/src/shipwire/product/shipwire.product.flags.php',
   'CharityRoot\\ShipwireProductValues' => '/src/shipwire/product/shipwire.product.values.php',
   'CharityRoot\\ShipwireQuote' => '/src/shipwire/shipwire.quote.php',
   'CharityRoot\\ShipwireQuoteItem' => '/src/shipwire/quote/shipwire.quote.item.php',
   'CharityRoot\\ShipwireRate' => '/src/shipwire/shipwire.rate.php',
   'CharityRoot\\ShipwireRateDimensions' => '/src/shipwire/rate/shipwire.rate.dimensions.php',
   'CharityRoot\\ShipwireRateItems' => '/src/shipwire/rate/shipwire.rate.items.php',
   'CharityRoot\\ShipwireRateShipment' => '/src/shipwire/rate/shipwire.rate.shipment.php',
   'CharityRoot\\ShipwireRequest' => '/src/shipwire/shipwire.request.php',
   'CharityRoot\\ShipwireResource' => '/src/shipwire/shipwire.resource.php',
   'CharityRoot\\ShipwireResponse' => '/src/shipwire/shipwire.response.php',
   'CharityRoot\\ShipwireTracking' => '/src/shipwire/shipwire.tracking.php',
   'CharityRoot\\ShipwireWebhook' => '/src/shipwire/shipwire.webhook.php',
   'CharityRoot\\ShipwireWebhookSecret' => '/src/shipwire/webhook/shipwire.webhook.secret.php',
];

spl_autoload_register(
    function($class) use ($classes) {
       if (isset($classes[$class])) {
            require __DIR__ . $classes[$class];
       }
    },
    true,
    false
);
