<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services\Cart;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Dto\UserCartInfoDto;
use Modules\Order\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;
use Ramsey\Uuid\Uuid;

class CartStorageService
{
    protected string $cartSessionKey = 'user.cart';

    public function __construct(protected Session $session) {}

    public function getCart(?User $user): array
    {
        if ($this->session->has($this->cartSessionKey)) {
            return $this->session->get($this->cartSessionKey);
        }

        if ($user) {
            $carts = $user->cart()->with('product:id,preview_image,title,slug')->get();

            if ($carts->isNotEmpty()) {
                $this->session->put($this->cartSessionKey, UserCartInfoDto::fromModels($carts)->toArray());
            }
        }

        return $this->session->get($this->cartSessionKey, []);
    }

    public function saveCart(?User $user, UserCartInfoDto $cart): void
    {
        if (! $user) {
            $this->session->push($this->cartSessionKey, $cart->setId(Uuid::uuid7()->toString())->toBase());

            return;
        }

        DB::transaction(function () use ($user, $cart) {
            $cartDb = $user->cart()->create($cart->toArray());

            $this->session->push($this->cartSessionKey, $cart->setId($cartDb->id)->toBase());
        });
    }

    public function updateCart(?User $user, Collection $cartItems): void
    {
        $this->session->put($this->cartSessionKey, $cartItems->toArray());

        if (! $user) {
            return;
        }

        DB::transaction(function () use ($user, $cartItems) {
            $cartData = $cartItems->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_per_unit' => $item->price_per_unit,
                'final_price' => $item->final_price,
                'discount' => $item->discount,
                'variation' => $item->variation,
                'user_id' => $user->id,
            ]);

            Cart::query()->where('user_id', $user->id)
                ->upsert($cartData->toArray(), 'id', ['quantity', 'final_price']);
        });
    }

    public function syncSessionCartAfterLogin(User $user): void
    {
        if (! $this->session->has($this->cartSessionKey)) {
            return;
        }

        DB::transaction(function () use ($user) {
            $userCart = Cart::query()->where('user_id', $user->id)->get();

            $sessionCart = collect($this->session->get($this->cartSessionKey, []));

            if ($sessionCart->isEmpty()) {
                return;
            }

            $newCartItems = $sessionCart->filter(function (object $item) use ($userCart) {
                $cartItem = $userCart->where('product_id', $item->product_id);

                if ($cartItem->isEmpty()) {
                    return true;
                }

                return $this->hasDifferentVariation($cartItem, $item);
            });

            if ($newCartItems->isNotEmpty()) {
                $data = $newCartItems->map(fn (object $item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'user_id' => $user->id,
                    'quantity' => $item->quantity,
                    'price_per_unit' => $item->price_per_unit,
                    'final_price' => $item->final_price,
                    'variation' => $item->variation ? json_encode($item->variation) : null,
                    'discount' => $item->discount ? json_encode($item->discount) : null,
                ])->toArray();

                Cart::query()->insert($data);
            }
        });
    }

    public function removeFromCart(?User $user, string $id): void
    {
        $user?->cart()->delete($id);

        $userCart = $this->session->get($this->cartSessionKey);
        if ($userCart) {
            $userCart = array_values(array_filter($userCart, static fn ($cartInfo) => $cartInfo->id !== $id));

            $this->session->put($this->cartSessionKey, $userCart);
        }
    }

    public function productExistsInCart(?User $user, Product $product, ?int $variationId = null): bool
    {
        if ($user) {
            $query = $user
                ->cart()
                ->where('product_id', $product->id);

            if ($variationId) {
                $query->whereJsonContains('variation->id', $variationId);
            } else {
                $query->whereNull('variation');
            }

            return $query->exists();
        }

        return collect($this->session->get($this->cartSessionKey, []))
            ->contains(function ($item) use ($product, $variationId) {
                if ($item->variation === null) {
                    return false;
                }

                return $item->product_id === $product->id &&
                    (! $variationId || ($item->variation['id'] ?? null) === $variationId);
            });
    }

    public function addQuantityToExistProduct(?User $user, ProductCartDto $dto): void
    {
        if ($user) {
            $cartItem = $user->cart()->where('product_id', $dto->product->id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $dto->quantity);
            }
        }

        $cart = collect($this->session->get($this->cartSessionKey, []));

        $cart->each(function ($item) use ($dto) {
            if ($item->product_id === $dto->product->id && $item->variation['id'] === $dto->variationId) {
                $item->quantity += $dto->quantity;
            }
        });

        $this->session->put($this->cartSessionKey, $cart->toArray());
    }

    public function clearCart(?User $user): void
    {
        $user?->cart()->delete();

        $this->clearSessionCart();
    }

    public function clearSessionCart(): void
    {
        $this->session->forget($this->cartSessionKey);
    }

    public function isCartEmpty(?User $user): bool
    {
        $sessionHasCart = $this->session->has($this->cartSessionKey);
        $userHasCart = $user?->cart()->exists() ?? false;

        return ! $sessionHasCart && ! $userHasCart;
    }

    private function hasDifferentVariation(Collection $cart, object $item): bool
    {
        if ($cart->count() > 1) {
            return $cart->contains(function (Cart $cartItem) use ($item) {
                if ($cartItem->variation === null) {
                    return false;
                }

                return $cartItem->variation['id'] !== $item->variation['id'];
            });
        }

        $cartItem = $cart->first();

        $dbCartVariation = $cartItem->variation['id'] ?? null;

        $sessionCartVariation = $item->variation['id'] ?? null;

        if ($dbCartVariation === null && $sessionCartVariation === null) {
            return false;
        }

        return $dbCartVariation !== $sessionCartVariation;
    }
}
