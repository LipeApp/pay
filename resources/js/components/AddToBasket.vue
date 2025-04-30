<template>
  <div class="add-to-basket">
    <button 
      @click="addToBasket" 
      class="btn btn-primary"
      :disabled="loading"
    >
      <span v-if="loading">Добавление...</span>
      <span v-else>Добавить в корзину</span>
    </button>
    
    <div v-if="showQuantity" class="quantity-controls mt-2">
      <button @click="decreaseQuantity" class="btn btn-sm btn-outline-secondary">-</button>
      <span class="mx-2">{{ quantity }}</span>
      <button @click="increaseQuantity" class="btn btn-sm btn-outline-secondary">+</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddToBasket',
  props: {
    productId: {
      type: Number,
      required: true
    },
    price: {
      type: Number,
      required: true
    },
    showQuantity: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      quantity: 1,
      loading: false
    }
  },
  methods: {
    async addToBasket() {
      this.loading = true;
      try {
        const response = await axios.post('/api/basket/add', {
          product_id: this.productId,
          quantity: this.quantity,
          price: this.price
        });
        this.$emit('added', response.data);
      } catch (error) {
        console.error('Error adding to basket:', error);
      } finally {
        this.loading = false;
      }
    },
    increaseQuantity() {
      this.quantity++;
    },
    decreaseQuantity() {
      if (this.quantity > 1) {
        this.quantity--;
      }
    }
  }
}
</script>

<style scoped>
.add-to-basket {
  display: inline-block;
}

.quantity-controls {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style> 