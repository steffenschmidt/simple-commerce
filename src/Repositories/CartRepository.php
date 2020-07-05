<?php

namespace DoubleThreeDigital\SimpleCommerce\Repositories;

use DoubleThreeDigital\SimpleCommerce\Models\Order;
use Exception;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Stache;

class CartRepository
{
    // TODO: create the big boi interface for the repo

    public string $id;
    public array $items = [];

    public int $grandTotal = 0000;
    public int $itemsTotal = 0000;
    public int $taxTotal = 0000;
    public int $shippingTotal = 0000;
    public int $couponTotal = 0000;

    public function make()
    {
        $this->id = (string) Stache::generateId();

        return $this;
    }

    public function find(string $id)
    {
        $cart = Entry::find($id);

        $this->id = $cart->id();
        $this->items = $cart->data()->get('items') ?? [];
        $this->grandTotal = $cart->data()->get('grand_total') ?? 0;
        $this->itemsTotal = $cart->data()->get('items_total') ?? 0;
        $this->taxTotal = $cart->data()->get('tax_total') ?? 0;
        $this->shippingTotal = $cart->data()->get('shipping_total') ?? 0;
        $this->couponTotal = $cart->data()->get('coupon_total') ?? 0;

        return $this;
    }

    public function save()
    {
        $entry = Entry::find($this->id);

        if ($entry === null) {
            $entry = Entry::make()
                ->collection('orders')
                ->blueprint('order')
                ->locale(Site::current()->handle())
                ->published(false)
                ->slug($this->id)
                ->id($this->id);
        }

        $entry
            ->data([
                'title' => 'Order #'.uniqid(),
                'items' => $this->items,
            ])
            ->save();

        return $this;    
    }

    public function update(array $data, bool $mergeData = true)
    {
        $entry = Entry::find($this->id);  

        if (! $entry) {
            throw new Exception('Cart not found');
        }

        if ($mergeData) {
            $data = array_merge($entry->data()->toArray(), $data);
        }

        $entry
            ->data($data)
            ->save();

        return $this;    
    }

    public function items(array $items = [])
    {
        if ($items === []) {
            return $this->items;
        }

        $this->items = $items;

        return $this;
    }

    public function count()
    {
        return collect($this->items)->count();
    }

    public function entry()
    {
        return Entry::find($this->id);
    }

    public function attachCustomer($user)
    {
        $this
            ->entry()
            ->set('customer', $user->id())
            ->save();

        return $this;    
    }

    public function markAsCompleted()
    {
        $this
            ->entry()
            ->published(true)
            ->data(array_merge($this->entry()->data()->toArray(), [
                'is_paid' => true,
            ]))
            ->save();

        return $this;    
    }

    public function calculateTotals()
    {
        $this->find($this->id);

        $data = [
            'grand_total'       => 0000,
            'items_total'       => 0000,
            'shipping_total'    => 0000,
            'tax_total'         => 0000,
            'coupon_total'      => 0000,
        ];

        $data['items'] = collect($this->items)
            ->map(function ($item) use (&$data) {
                $product = Entry::find($item['product']);

                $itemTotal = ($product->data()->get('price') * $item['quantity']);

                // TODO: shipping
                // TODO: tax
                // TODO: coupon

                $data['grand_total'] += $itemTotal;

                return array_merge($item, [
                    'total' => $itemTotal,
                ]);
            })
            ->toArray();
        
        $this->update($data);
        $this->find($this->id);

        return $this;
    }
}