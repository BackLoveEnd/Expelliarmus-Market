<script setup>
import { useCartStore } from '@/stores/useCartStore.js'
import { computed, ref, watch } from 'vue'
import { useTruncator } from '@/composables/useTruncator.js'
import { useScrolling } from '@/composables/useScrolling.js'

const cartStore = useCartStore()

const emit = defineEmits(['total-price', 'updated-product'])

const truncator = useTruncator()

const isInitialLoad = ref(true)

const totalPrice = computed(() => {
  return cartStore.cartItems.reduce((total, product) => {
    const price = product.discount ? product.discount.new_price : product.unitPrice
    return total + price * product.quantity
  }, 0).toFixed(2)
})

async function getCart () {
  await cartStore.fetchCart()
      .then(() => (isInitialLoad.value = true))
      .catch((e) => {
        if (e.status === 404) {
        }
      })
}

watch(
    () => cartStore.cartItems.map((item) => item.quantity),
    (newQuantities, oldQuantities) => {
      if (isInitialLoad.value) {
        isInitialLoad.value = false
        return
      }

      newQuantities.forEach((newQty, index) => {
        const oldQty = oldQuantities[index]
        if (newQty !== oldQty) {
          emit('updated-product', cartStore.cartItems[index])
        }
      })
    },
    { deep: true }
)

watch(
    () => cartStore.cartItems,
    () => {
      emit('total-price', totalPrice.value)
    },
    { deep: true }
)

watch(totalPrice, (newTotal) => {
  emit('total-price', newTotal)
})

await getCart()
</script>

<template>
  <article class="flex flex-col gap-y-10">
    <table class="w-full border-separate border-spacing-y-10">
      <thead>
      <tr class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white">
        <th class="py-6 px-12 font-normal text-start">Product</th>
        <th class="py-6 px-12 font-normal">Option</th>
        <th class="py-6 px-12 font-normal">Price</th>
        <th class="py-6 px-12 font-normal">Quantity</th>
        <th class="py-6 px-12 font-normal">Subtotal</th>
      </tr>
      </thead>
      <tbody class="text-center">
      <tr
          v-for="(product, index) in cartStore.cartItems"
          :key="product.productId"
          class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white relative"
      >
        <td class="py-6 px-12 font-normal text-start max-w-xs">
          <router-link
              @click.prevent="useScrolling().scrollToTop()"
              :to="{ name: 'product-page', params: { productId: product.productId, productSlug: product.productSlug}}"
              class="flex items-center gap-x-4 underline text-blue-400 underline-offset-2">
            <img
                :src="product.productImage"
                :alt="product.productTitle"
                class="max-w-14 max-h-14"
            />
            <span class="text-base font-normal">{{ truncator.truncateString(product.productTitle, 50) }}</span>
          </router-link>
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex flex-col items-center" v-if="product.variation !== null">
            <div class="flex gap-x-2 text-sm" v-for="variation in product.variation.data">
              <span>{{ variation.attribute_name }} - </span>
              <span>{{ variation.value }}</span>
            </div>
          </div>
          <i class="pi pi-minus" v-else></i>
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex flex-col justify-center" v-if="product.discount">
            <div class="space-x-2">
              <span class="line-through decoration-red-500">${{ product.unitPrice.toFixed(2) }}</span>
              <span class="font-semibold">${{ product.discount.new_price.toFixed(2) }}</span>
            </div>
            <span class="text-red-500">Sale -{{ product.discount.percentage }}</span>
          </div>
          <span v-else>${{ product.unitPrice.toFixed(2) }}</span>
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex justify-center items-center">
            <input
                type="number"
                min="1"
                class="w-14 h-12 border-2 border-gray-400 text-center rounded-md"
                v-model.number="product.quantity"
            />
          </div>
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex gap-x-2 justify-center" v-if="product.discount">
            <span class="line-through decoration-red-500">${{
                (product.unitPrice * product.quantity).toFixed(2)
              }}</span>
            <span class="font-semibold">${{ (product.discount.new_price * product.quantity).toFixed(2) }}</span>
          </div>
          <span v-else>${{ (product.unitPrice * product.quantity).toFixed(2) }}</span>
        </td>
        <td>
          <button
              @click="cartStore.removeFromCart(product.id)"
              class="absolute -top-3 -right-3 text-white"
          >
            <i class="pi pi-times-circle text-red-500"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>
  </article>
</template>

<style scoped>
</style>