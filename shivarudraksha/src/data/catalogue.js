export const IMAGE_SLOTS = [
  { key: "front", label: "Front" },
  { key: "back", label: "Back" },
  { key: "top", label: "Top" },
  { key: "bottom", label: "Bottom" },
  { key: "certificate", label: "Certificate" },
  { key: "xray", label: "X-ray" },
];

export const emptyImages = () => ({
  front: "",
  back: "",
  top: "",
  bottom: "",
  certificate: "",
  xray: "",
});

export function assetUrl(path) {
  if (!path) return "";
  if (/^(https?:|data:|blob:)/.test(path)) return path;
  const clean = path.replace(/^\/+/, "").replace(/\.jpe?g$/i, ".avif");
  return `${import.meta.env.BASE_URL}${clean}`;
}

const mapToAvif = (imgObj) => {
  if (!imgObj || typeof imgObj !== "object") return imgObj;
  const res = {};
  for (const [k, v] of Object.entries(imgObj)) {
    res[k] = typeof v === "string" ? v.replace(/\.jpe?g$/i, ".avif") : v;
  }
  return res;
};

export function normalizeProduct(product) {
  const rawImages = mapToAvif({
    ...emptyImages(),
    ...(product.images || {}),
    front: product.images?.front || product.image || "",
  });

  const variants =
    Array.isArray(product.variants) && product.variants.length
      ? product.variants.map((variant) => ({
          ...variant,
          images: mapToAvif({
            ...emptyImages(),
            ...(variant.images || {}),
          }),
        }))
      : [];

  return {
    ...product,
    rating: typeof product.rating === "number" ? product.rating : parseFloat(product.rating || 0),
    rating_count: typeof product.rating_count === "number" ? product.rating_count : parseInt(product.rating_count || 0, 10),
    reviews: Array.isArray(product.reviews) ? product.reviews : [],
    variants,
    images: rawImages,
  };
}

let inMemoryProductsCache = null;
const CACHE_KEY = "shiva_catalogue_v2";

export async function loadProducts() {
  // 1. Instant Cache: Return in-memory or sessionStorage cached catalogue in 0ms
  if (inMemoryProductsCache && inMemoryProductsCache.length > 0) {
    return inMemoryProductsCache;
  }

  try {
    const sessionCached = sessionStorage.getItem(CACHE_KEY);
    if (sessionCached) {
      const parsed = JSON.parse(sessionCached);
      if (Array.isArray(parsed) && parsed.length > 0) {
        inMemoryProductsCache = parsed;
        // Trigger background refresh silently
        refreshProductsInBackground();
        return parsed;
      }
    }
  } catch {
    // Non-blocking fallback
  }

  return await fetchAndMergeProducts();
}

async function refreshProductsInBackground() {
  try {
    const fresh = await fetchAndMergeProducts();
    if (fresh && fresh.length > 0) {
      inMemoryProductsCache = fresh;
      sessionStorage.setItem(CACHE_KEY, JSON.stringify(fresh));
    }
  } catch {
    // Silently continue with cached
  }
}

async function fetchAndMergeProducts() {
  let backendProducts = [];
  let staticProducts = [];

  // Parallel fetch: fetch live API with 2.5s timeout & static products.json concurrently
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 2500);

  const apiEndpoint = `${import.meta.env.BASE_URL.replace(/\/$/, "")}/api/v3/products?per_page=100`;
  const staticEndpoint = `${import.meta.env.BASE_URL}data/products.json`;

  const [apiResult, staticResult] = await Promise.allSettled([
    fetch(apiEndpoint, { signal: controller.signal })
      .then(async (res) => (res.ok ? res.json() : null))
      .catch(() => null),
    fetch(staticEndpoint)
      .then(async (res) => (res.ok ? res.json() : []))
      .catch(() => []),
  ]);

  clearTimeout(timeoutId);

  // Parse backend API products
  if (apiResult.status === "fulfilled" && apiResult.value?.data && Array.isArray(apiResult.value.data)) {
    backendProducts = apiResult.value.data.map((item) =>
      normalizeProduct({
        id: item.slug || String(item.id),
        db_id: item.id,
        name: item.name,
        mukhi: item.mukhi || (item.name.match(/(\d+)\s*Mukhi/i)?.[1] ? parseInt(item.name.match(/(\d+)\s*Mukhi/i)[1], 10) : null),
        category: item.category?.name || "Nepal Rudraksha",
        origin: item.category?.name?.toLowerCase().includes("indonesia") ? "Indonesia" : "Nepal",
        description: item.description || "",
        certificateAvailable: true,
        stockStatus: item.stockStatus || "Available",
        badge: item.badge || (item.featured ? "Featured" : "Certified"),
        price: item.unit_price,
        rating: typeof item.rating === "number" ? item.rating : parseFloat(item.rating || 0),
        rating_count: typeof item.rating_count === "number" ? item.rating_count : parseInt(item.rating_count || 0, 10),
        reviews: Array.isArray(item.reviews) ? item.reviews : [],
        disable_price: Boolean(item.disable_price || item.unit_price <= 0),
        coming_soon: Boolean(item.coming_soon || item.stockStatus === "Coming Soon"),
        variants: item.variants || [],
        attributes: item.attributes || [],
        choice_options: item.choice_options || [],
        images: item.images || {
          front: item.photos?.[1] || item.thumbnail_url || item.photos?.[0] || "",
          back: item.photos?.[2] || "",
          top: item.photos?.[0] || item.thumbnail_url || "",
          bottom: item.photos?.[3] || "",
          certificate: item.photos?.[4] || "",
          xray: item.photos?.[5] || "",
        },
      })
    );
  }

  // Parse static catalogue products
  if (staticResult.status === "fulfilled" && Array.isArray(staticResult.value) && staticResult.value.length > 0) {
    staticProducts = staticResult.value.map(normalizeProduct);
  }

  let finalProducts = staticProducts;

  if (backendProducts.length > 0) {
    const merged = [...backendProducts];
    for (const sp of staticProducts) {
      const alreadyInBackend = backendProducts.some(
        (bp) => bp.id === sp.id || (bp.mukhi && bp.mukhi === sp.mukhi)
      );
      if (!alreadyInBackend) {
        merged.push(sp);
      }
    }
    finalProducts = merged;
  }

  if (finalProducts.length > 0) {
    inMemoryProductsCache = finalProducts;
    try {
      sessionStorage.setItem(CACHE_KEY, JSON.stringify(finalProducts));
    } catch {
      // quota or private mode ignore
    }
  }

  return finalProducts;
}