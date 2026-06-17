const productRoot = document.getElementById('products');
const categoryRoot = document.getElementById('categories');
const productCount = document.getElementById('product-count');
const heroImage = document.getElementById('hero-image');
let allProducts = [];
let allCategories = [];
let activeCategoryId = 0;

function safeText(value) {
  return String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
}

function renderCategories() {
  if (!categoryRoot) return;

  if (!allCategories.length) {
    categoryRoot.innerHTML = '<p class="empty">No categories available yet.</p>';
    return;
  }

  const allButton = `<button class="category-chip ${activeCategoryId === 0 ? 'active' : ''}" type="button" data-category-id="0">All Products</button>`;
  const categoryButtons = allCategories.map(category => `
    <button class="category-chip ${activeCategoryId === Number(category.id) ? 'active' : ''}" type="button" data-category-id="${Number(category.id)}">
      ${safeText(category.category_name)}
    </button>
  `).join('');

  categoryRoot.innerHTML = allButton + categoryButtons;

  categoryRoot.querySelectorAll('.category-chip').forEach(button => {
    button.addEventListener('click', () => {
      activeCategoryId = Number(button.dataset.categoryId || 0);
      renderCategories();
      renderProducts();
      document.getElementById('products-section')?.scrollIntoView({ behavior: 'smooth' });
    });
  });
}

function renderProducts() {
  if (!productRoot) return;

  const products = activeCategoryId === 0
    ? allProducts
    : allProducts.filter(product => Number(product.category_id) === activeCategoryId);

  if (productCount) productCount.textContent = products.length;
  if (heroImage && products[0]) heroImage.src = `images/${safeText(products[0].product_image)}`;

  if (!products.length) {
    productRoot.innerHTML = '<p class="empty">No products available in this category yet.</p>';
    return;
  }

  productRoot.innerHTML = products.map(product => `
    <article class="card">
      <img src="images/${safeText(product.product_image)}" alt="${safeText(product.product_name)}">
      <div class="card-body">
        <span class="product-category">${safeText(product.category_name || 'Uncategorized')}</span>
        <h3>${safeText(product.product_name)}</h3>
        <p>${safeText(product.product_description)}</p>
        <div class="price">
          <strong>$${safeText(product.product_price)}</strong>
          <button class="btn ghost" type="button">Add to Cart</button>
        </div>
      </div>
    </article>
  `).join('');
}

fetch('APIs/products.php')
  .then(response => response.json())
  .then(data => {
    allProducts = data.products || [];
    allCategories = data.categories || [];
    renderCategories();
    renderProducts();
  })
  .catch(() => {
    if (productRoot) productRoot.innerHTML = '<p class="empty">Products could not be loaded.</p>';
    if (categoryRoot) categoryRoot.innerHTML = '<p class="empty">Categories could not be loaded.</p>';
  });
