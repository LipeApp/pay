<template>
  <div class="checkout-button">
    <button 
      @click="createOrder" 
      class="btn btn-success"
      :disabled="loading"
    >
      <span v-if="loading">Оформление заказа...</span>
      <span v-else>Оформить заказ</span>
    </button>
  </div>
</template>

<script>
export default {
  name: 'CheckoutButton',
  data() {
    return {
      loading: false
    }
  },
  methods: {
    async createOrder() {
      this.loading = true;
      try {
        const response = await axios.post('/api/orders/create', {
          gateway: 'click'
        });
        
        // Перенаправляем на страницу оплаты
        window.location.href = response.data.payment_url;
      } catch (error) {
        console.error('Error creating order:', error);
        alert(error.response?.data?.message || 'Произошла ошибка при оформлении заказа');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.checkout-button {
  display: inline-block;
}
</style> 