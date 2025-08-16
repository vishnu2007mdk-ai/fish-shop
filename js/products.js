// Extended product data for the products page
const allProducts = [
    {
        id: 1,
        name: "Fresh Salmon",
        price: 24.99,
        description: "Wild-caught Alaskan salmon, perfect for grilling or baking",
        category: "salmon",
        image: "🐟",
        inStock: true
    },
    {
        id: 2,
        name: "Atlantic Cod",
        price: 18.99,
        description: "Fresh Atlantic cod fillets, mild and flaky texture",
        category: "cod",
        image: "🐟",
        inStock: true
    },
    {
        id: 3,
        name: "Tuna Steaks",
        price: 22.99,
        description: "Premium yellowfin tuna steaks, perfect for sushi or grilling",
        category: "tuna",
        image: "🐟",
        inStock: true
    },
    {
        id: 4,
        name: "Sea Bass",
        price: 28.99,
        description: "European sea bass, delicate flavor and firm texture",
        category: "bass",
        image: "🐟",
        inStock: true
    },
    {
        id: 5,
        name: "Rainbow Trout",
        price: 16.99,
        description: "Fresh rainbow trout, mild and delicate flavor",
        category: "trout",
        image: "🐟",
        inStock: true
    },
    {
        id: 6,
        name: "Mahi Mahi",
        price: 26.99,
        description: "Fresh mahi mahi fillets, firm texture and mild taste",
        category: "mahi",
        image: "🐟",
        inStock: true
    },
    {
        id: 7,
        name: "Red Snapper",
        price: 32.99,
        description: "Whole red snapper, perfect for roasting or grilling",
        category: "snapper",
        image: "🐟",
        inStock: false
    },
    {
        id: 8,
        name: "Halibut",
        price: 34.99,
        description: "Pacific halibut fillets, thick and meaty texture",
        category: "halibut",
        image: "🐟",
        inStock: true
    },
    {
        id: 9,
        name: "Swordfish",
        price: 29.99,
        description: "Fresh swordfish steaks, meaty and flavorful",
        category: "swordfish",
        image: "🐟",
        inStock: true
    },
    {
        id: 10,
        name: "Grouper",
        price: 27.99,
        description: "Fresh grouper fillets, mild and flaky",
        category: "grouper",
        image: "🐟",
        inStock: true
    }
];

let currentProducts = [...allProducts];
let displayedCount = 6;

// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Load products
function loadProducts(products = currentProducts) {
    const productsGrid = document.getElementById('products-grid');
    productsGrid.innerHTML = '';

    const productsToShow = products.slice(0, displayedCount);
    
    productsToShow.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <div class="product-image">
                ${product.image}
            </div>
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">$${product.price.toFixed(2)}</div>
                <p class="product-description">${product.description}</p>
                <div class="product-status ${product.inStock ? 'in-stock' : 'out-of-stock'}">
                    ${product.inStock ? 'In Stock' : 'Out of Stock'}
                </div>
                <button class="add-to-cart" onclick="addToCart(${product.id})" ${!product.inStock ? 'disabled' : ''}>
                    ${product.inStock ? 'Add to Cart' : 'Out of Stock'}
                </button>
            </div>
        `;
        productsGrid.appendChild(productCard);
    });

    // Show/hide load more button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (products.length <= displayedCount) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
    }
}

// Search functionality
function searchProducts(query) {
    if (!query) {
        currentProducts = [...allProducts];
    } else {
        currentProducts = allProducts.filter(product => 
            product.name.toLowerCase().includes(query.toLowerCase()) ||
            product.description.toLowerCase().includes(query.toLowerCase()) ||
            product.category.toLowerCase().includes(query.toLowerCase())
        );
    }
    displayedCount = 6;
    loadProducts();
}

// Filter functionality
function filterProducts() {
    const categoryFilter = document.getElementById('category-filter').value;
    const priceFilter = document.getElementById('price-filter').value;
    
    let filtered = [...allProducts];
    
    // Category filter
    if (categoryFilter) {
        filtered = filtered.filter(product => product.category === categoryFilter);
    }
    
    // Price filter
    if (priceFilter) {
        filtered = filtered.filter(product => {
            switch(priceFilter) {
                case '0-20':
                    return product.price < 20;
                case '20-30':
                    return product.price >= 20 && product.price <= 30;
                case '30+':
                    return product.price > 30;
                default:
                    return true;
            }
        });
    }
    
    currentProducts = filtered;
    displayedCount = 6;
    loadProducts();
}

// Clear filters
function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('price-filter').value = '';
    currentProducts = [...allProducts];
    displayedCount = 6;
    loadProducts();
}

// Load more products
function loadMore() {
    displayedCount += 6;
    loadProducts();
}

// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (product && product.inStock) {
        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                ...product,
                quantity: 1
            });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        showNotification('Product added to cart!');
    }
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 1rem 2rem;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    
    // Search input
    const searchInput = document.getElementById('search-input');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchProducts(this.value);
        }, 300);
    });
    
    // Category filter
    document.getElementById('category-filter').addEventListener('change', filterProducts);
    
    // Price filter
    document.getElementById('price-filter').addEventListener('change', filterProducts);
    
    // Clear filters
    document.getElementById('clear-filters').addEventListener('click', clearFilters);
    
    // Load more button
    document.getElementById('load-more-btn').addEventListener('click', loadMore);
});

// Add CSS for products page
const style = document.createElement('style');
style.textContent = `
    .products-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        text-align: center;
        padding: 120px 0 4rem;
        margin-top: 70px;
    }
    
    .products-header h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .products-header p {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .filters-section {
        background: #f8fafc;
        padding: 2rem 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .filters-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .search-box {
        position: relative;
        flex: 1;
        max-width: 400px;
    }
    
    .search-box input {
        width: 100%;
        padding: 12px 40px 12px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #3b82f6;
    }
    
    .search-box i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
    }
    
    .filter-options {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-options select {
        padding: 10px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
        cursor: pointer;
    }
    
    .filter-options select:focus {
        outline: none;
        border-color: #3b82f6;
    }
    
    .products-section {
        padding: 4rem 0;
    }
    
    .load-more {
        text-align: center;
        margin-top: 3rem;
    }
    
    .product-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .in-stock {
        background: #dcfce7;
        color: #166534;
    }
    
    .out-of-stock {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .add-to-cart:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @media (max-width: 768px) {
        .products-header h1 {
            font-size: 2rem;
        }
        
        .filters-container {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-box {
            max-width: none;
        }
        
        .filter-options {
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);

