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
  return `${import.meta.env.BASE_URL}${path.replace(/^\/+/, "")}`;
}

export function normalizeProduct(product) {
  const legacyImages = {
    ...emptyImages(),
    ...(product.images || {}),
    front: product.images?.front || product.image || "",
  };

  const variants =
    Array.isArray(product.variants) && product.variants.length
      ? product.variants.map((variant) => ({
          ...variant,
          images: {
            ...emptyImages(),
            ...(variant.images || {}),
          },
        }))
      : [];

  return {
    ...product,
    variants,
    images: legacyImages,
  };
}

export async function loadProducts() {
  const response = await fetch(
    `${import.meta.env.BASE_URL}data/products.json?t=${Date.now()}`,
    { cache: "no-store" }
  );

  if (!response.ok) {
    throw new Error("Unable to load product catalogue.");
  }

  return (await response.json()).map(normalizeProduct);
}