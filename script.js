// Placeholder for custom JavaScript

document.getElementById('addListingForm')?.addEventListener('submit', function(event) {
    // Example client-side validation or enhancements can be added here
});

// Price Estimator Functionality
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const totalPriceDisplay = document.createElement('div');

    totalPriceDisplay.id = 'total_price_display';
    totalPriceDisplay.style.marginTop = '10px';
    totalPriceDisplay.style.fontWeight = 'bold';

    priceInput?.parentNode?.appendChild(totalPriceDisplay);

    priceInput.addEventListener('input', function() {
        const pricePerDay = parseFloat(priceInput.value) || 0;
        const totalPrice = pricePerDay;
        totalPriceDisplay.textContent = `Total Price: $${totalPrice.toFixed(2)}`;
    });
});
