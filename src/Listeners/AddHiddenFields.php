<?php

namespace DoubleThreeDigital\SimpleCommerce\Listeners;

use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Statamic\Events\EntryBlueprintFound;

class AddHiddenFields
{
    public function handle(EntryBlueprintFound $event)
    {
        if (! $event->entry) {
            return $event->blueprint;
        }

        if (
            isset(SimpleCommerce::couponDriver()['collection'])
            && SimpleCommerce::couponDriver()['collection'] === $event->entry->collectionHandle()
        ) {
            return $this->addCouponFields($event);
        }

        if (
            isset(SimpleCommerce::customerDriver()['collection'])
            && SimpleCommerce::customerDriver()['collection'] === $event->entry->collectionHandle()
        ) {
            return $this->addCustomerFields($event);
        }

        if (
            isset(SimpleCommerce::orderDriver()['collection'])
            && SimpleCommerce::orderDriver()['collection'] === $event->entry->collectionHandle()
        ) {
            return $this->addOrderFields($event);
        }

        if (
            isset(SimpleCommerce::productDriver()['collection'])
            && SimpleCommerce::productDriver()['collection'] === $event->entry->collectionHandle()
        ) {
            return $this->addProductFields($event);
        }

        return $event->blueprint;
    }

    protected function addCouponFields(EntryBlueprintFound $event)
    {
        return $event->blueprint;
    }

    protected function addCustomerFields(EntryBlueprintFound $event)
    {
        return $event->blueprint;
    }

    protected function addOrderFields(EntryBlueprintFound $event)
    {
        $event->blueprint->ensureField('receipt_url', [
            'type'    => 'receipt_url',
            'display' => 'SC Receipt URL',
        ], 'sidebar');

        return $event->blueprint;
    }

    protected function addProductFields(EntryBlueprintFound $event)
    {
        return $event->blueprint;
    }
}
