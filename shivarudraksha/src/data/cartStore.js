// ==========================================================================
// CART & COMMERCE STORE
// Shiva Rudraksha Inc.
// Syncs with localStorage and integrates with Laravel backend session/API
// ==========================================================================

const CART_STORAGE_KEY = "rudraspirit_cart";
const RECENT_STORAGE_KEY = "rudraspirit_recent_views";

export function getCart() {
  try {
    const raw = localStorage.getItem(CART_STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    console.error("Error reading cart from localStorage", e);
    return [];
  }
}

export function saveCart(cart) {
  try {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    window.dispatchEvent(new CustomEvent("cart-updated", { detail: cart }));
  } catch (e) {
    console.error("Error saving cart to localStorage", e);
  }
}

export function addToCart(product, selectedVariant, sizeName, certName, quantity = 1) {
  const cart = getCart();
  const variantName = selectedVariant?.name || "Standard";
  const itemId = `${product.id}_${variantName}`;
  const price = Number(selectedVariant?.price || product.price || 0);
  const image =
    selectedVariant?.images?.front ||
    selectedVariant?.images?.top ||
    product.images?.front ||
    product.images?.top ||
    "";

  const existingIndex = cart.findIndex((item) => item.id === itemId);

  if (existingIndex > -1) {
    cart[existingIndex].quantity += quantity;
  } else {
    cart.push({
      id: itemId,
      productId: product.id,
      dbId: product.db_id || product.id,
      name: product.name,
      variantName,
      sizeName: sizeName || "Standard Size",
      certName: certName || "Government Lab Certificate",
      price,
      image,
      quantity,
      addedAt: new Date().toISOString(),
    });
  }

  saveCart(cart);

  // Background sync attempt with Laravel web cart endpoint
  trySyncWithBackend(product.db_id || product.id, variantName, quantity);

  return cart;
}

export function updateQuantity(itemId, quantity) {
  const cart = getCart();
  if (quantity <= 0) {
    return removeFromCart(itemId);
  }
  const item = cart.find((i) => i.id === itemId);
  if (item) {
    item.quantity = quantity;
    saveCart(cart);
  }
  return cart;
}

export function removeFromCart(itemId) {
  let cart = getCart();
  cart = cart.filter((i) => i.id !== itemId);
  saveCart(cart);
  return cart;
}

export function clearCart() {
  saveCart([]);
}

export function getCartTotal() {
  const cart = getCart();
  return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

export function getCartCount() {
  const cart = getCart();
  return cart.reduce((count, item) => count + item.quantity, 0);
}

// Background sync with Laravel /cart/add
async function trySyncWithBackend(productId, variantName, quantity) {
  try {
    const formData = new FormData();
    formData.append("id", productId);
    formData.append("quantity", quantity);
    formData.append("variant", variantName);

    // Call Laravel cart add endpoint (cookie session will automatically attach)
    await fetch("/cart/add", {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
    }).catch(() => {
      // Backend may require CSRF token for web routes; fallback safely
    });
  } catch (err) {
    // Non-blocking sync error
  }
}

// ==========================================================================
// RECENTLY VIEWED PRODUCTS
// ==========================================================================

export function recordRecentProduct(product) {
  if (!product || !product.id) return;
  try {
    let recent = getRecentProducts();
    recent = recent.filter((p) => p.id !== product.id);
    recent.unshift({
      id: product.id,
      name: product.name,
      price: product.price || 0,
      origin: product.origin || "Nepal",
      mukhi: product.mukhi || null,
      badge: product.badge || "Authentic",
      image: product.images?.front || product.images?.top || "",
      viewedAt: Date.now(),
    });
    // Keep last 10 products
    recent = recent.slice(0, 10);
    localStorage.setItem(RECENT_STORAGE_KEY, JSON.stringify(recent));
  } catch (e) {
    console.warn("Unable to record recent product", e);
  }
}

export function getRecentProducts() {
  try {
    const raw = localStorage.getItem(RECENT_STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    return [];
  }
}
