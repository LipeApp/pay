# UzPaymentGateways

Пакет для интеграции с платежными системами Узбекистана (Payme, Click, Ipak).

## Установка

```bash
composer require lipeapp/pay
```

## Настройка

1. Опубликуйте конфигурационный файл:
```bash
php artisan vendor:publish --provider="UzPaymentGateways\PaymentServiceProvider" --tag="payment-config"
```

2. Добавьте в `.env` файл необходимые параметры:
```env
PAYMENT_GATEWAY=payme

# Payme
PAYME_MERCHANT_ID=your_merchant_id
PAYME_MERCHANT_KEY=your_merchant_key
PAYME_TEST_MODE=true

# Click
CLICK_MERCHANT_ID=your_merchant_id
CLICK_MERCHANT_KEY=your_merchant_key
CLICK_SERVICE_ID=your_service_id
CLICK_TEST_MODE=true

# Ipak
IPAK_MERCHANT_ID=your_merchant_id
IPAK_MERCHANT_KEY=your_merchant_key
IPAK_TEST_MODE=true
```

## Использование

### Создание платежа

```php
use gateways\src\Gateways\PaymentGatewayFactory;

// Создание экземпляра платежного шлюза
$gateway = PaymentGatewayFactory::create('payme', [
    'merchant_id' => env('PAYME_MERCHANT_ID'),
    'merchant_key' => env('PAYME_MERCHANT_KEY'),
]);

// Создание транзакции
$result = $gateway->createTransaction('order_123', 100000, [
    'description' => 'Payment for order #123',
    'callback_url' => 'https://your-site.com/payment/callback',
]);

// Получение URL для оплаты
$paymentUrl = $result['payment_url'];
```

### Проверка статуса платежа

```php
$status = $gateway->checkTransaction('transaction_123');
```

### Отмена платежа

```php
$result = $gateway->cancelTransaction('transaction_123');
```

### Проверка подписи

```php
$isValid = $gateway->verifySignature($requestData);
```

## Поддерживаемые платежные системы

### Payme
- Создание платежа
- Проверка статуса
- Отмена платежа
- Проверка подписи

### Click
- Создание платежа
- Проверка статуса
- Отмена платежа
- Проверка подписи

### Ipak
- Создание платежа
- Проверка статуса
- Отмена платежа
- Проверка подписи

## Обработка ошибок

```php
try {
    $result = $gateway->createTransaction('order_123', 100000);
} catch (gateways\src\Exceptions\PaymentException $e) {
    // Обработка ошибки
    echo $e->getMessage();
}
```

## Тестирование

```bash
composer test
```

## Лицензия

MIT 
