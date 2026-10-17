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

export async function loadProducts() {
  let backendProducts = [];

  try {
    // 1. Fetch live products from the Laravel backend engine API
    const apiEndpoint = `${import.meta.env.BASE_URL.replace(/\/$/, "")}/api/v3/products?per_page=100&t=${Date.now()}`;
    const apiResponse = await fetch(apiEndpoint, { cache: "no-store" });
    if (apiResponse.ok) {
      const result = await apiResponse.json();
      if (Array.isArray(result.data) && result.data.length > 0) {
        backendProducts = result.data.map((item) => {
          return normalizeProduct({
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
          });
        });
      }
    }
  } catch (err) {
    console.warn("Backend API not reachable; falling back to static catalogue:", err);
  }

  // 2. Also load static products.json to ensure full 1–21 Mukhi catalogue is always present
  try {
    const jsonResponse = await fetch(
      `${import.meta.env.BASE_URL}data/products.json?t=${Date.now()}`,
      { cache: "no-store" }
    );
    if (jsonResponse.ok) {
      const staticProducts = (await jsonResponse.json()).map(normalizeProduct);

      if (backendProducts.length > 0) {
        // Merge: Live backend products take priority so any edits in /admin immediately override static data
        const merged = [...backendProducts];
        for (const sp of staticProducts) {
          const alreadyInBackend = backendProducts.some(
            (bp) => bp.id === sp.id || (bp.mukhi && bp.mukhi === sp.mukhi)
          );
          if (!alreadyInBackend) {
            merged.push(sp);
          }
        }
        return merged;
      }

      return staticProducts;
    }
  } catch (jsonErr) {
    console.error("Static products.json error:", jsonErr);
  }

  return backendProducts;
}