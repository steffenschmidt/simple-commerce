---
title: Upgrade Guide
---

## Overview

When updating, read through this guide to see if there's anything you might need to change. A good chunk of updates will be done automatically for you but there will still be some manual steps you will need to take.

In your `composer.json` file, change the `doublethreedigital/simple-commerce` version constraint:

```json
"doublethreedigital/simple-commerce": "2.4.*"
```

Then run:

```
composer update doublethreedigital/simple-commerce --with-dependencies
```

## New Features

### Tax

Simple Commerce v2.4 includes a brand new Tax System!

When you're upgraded to v2.4, Simple Commerce will update your configuration file to use a new 'basic tax engine'. Essentially, the 'basic tax engine' is the same thing you had previously - with a single tax rate.

If you wish, you may enable the 'standard tax engine'. The Standard Tax Engine is more complicated but comes with super powers (compared to a single tax rate 😅). It allows you to set tax rates based on the type of products purchased and the customer's billing address.

A full explanation of both engines is available in [the documentation](./tax.md).

## Changes

### Low Impact: Dropped Laravel 6 support

We've dropped support for sites using Laravel 6. If you're unsure as to the Laravel version you're using, run `php artisan --version` which will tell you.

Simple Commerce supports Laravel 7 onwards. If you're on Laravel 6, you may upgrade by following the official [Laravel Upgrade Guide](https://laravel.com/docs/7.x/upgrade#upgrade-7.0).

### Low Impact: Dropped Statamic 3.1 support

We've dropped support for Statamic 3.1. Now, only 3.2 and above is supported. Ideally, you should be using Statamic 3.3 (when it's been fully released).

### Low Impact: 'Line Items Tax' field added to Orders blueprint

During the upgrade process, Simple Commerce will automatically add a 'Line Items Tax' field to your Orders blueprint. This field is used to let you display tax information (like amount, rate, etc) on a per line item basis via Antlers.

### Low Impact: One cart per site

In v2.3, if you had multiple sites on the same domain, they would all share a single cart. This meant you could add one product on one site and another product on another site. This would mean currencies would be mixed up, shipping methods would get mixed up, etc.

Now in v2.4, each cart will have it's own cart. Simple Commerce will append the site handle to the cart key in your cookies/session.

Any multi-sites migrated to v2.4 will continue to use the v2.3 behaviour. To opt-out, and use a 'cart per site', remove the `cart.single_cart` config value:

```php
'cart' => [
    'driver' => \DoubleThreeDigital\SimpleCommerce\Orders\Cart\Drivers\CookieDriver::class,
    'key' => 'simple-commerce-cart',
    'single_cart' => true, // [tl! --]
],
```

### Medium Impact: Changes to how 'gateway data' is stored

Previously, the data from a gateway would look something like this in your order entry:

```yaml
gateway: DoubleThreeDigital\SimpleCommerce\Gateways\Builtin\StripeGateway
stripe:
  intent: pi_whatever
  client_secret: pi_whateveragain_secret_something
gateway_data:
  id: pm_whatever
```

We've improved this, so the `gateway` and `gateway_data` values are under a single key, like so:

```yaml
gateway:
  use: DoubleThreeDigital\SimpleCommerce\Gateways\Builtin\StripeGateway
  data:
    id: pm_whatever
stripe:
  intent: pi_whatever
  client_secret: pi_whateveragain_secret_something
```

And for new orders, we'll automatically get rid of the 'temporary gateway data' added to order entries before checking out.

These changes should hopefully help to tidy up your order entry. It also lets us do cool things in the future, like a Gateway fieldtype.

> You don't need to worry about anything - Simple Commerce will automatically re-format your order entries for you during the upgrade process.

### Medium Impact: Updated signature of `checkAvailability` method on shipping methods

The signature of the `checkAvailability` method on shipping methods has changed. We now pass in the order.

**Previously:**

```php
use DoubleThreeDigital\SimpleCommerce\Orders\Address;

public function checkAvailability(Address $address): bool;
```

**Now:**

```php
use DoubleThreeDigital\SimpleCommerce\Contracts\Order;
use DoubleThreeDigital\SimpleCommerce\Orders\Address;

public function checkAvailability(Order $order, Address $address): bool;
```

## Previous upgrade guides

- [v2.2 to v2.3](https://github.com/doublethreedigital/simple-commerce/blob/2.3/docs/upgrade-guide.md)

---

[You may also view a diff of changes between v2.3 and v3.4](https://github.com/doublethreedigital/simple-commerce/compare/2.3...2.4)
