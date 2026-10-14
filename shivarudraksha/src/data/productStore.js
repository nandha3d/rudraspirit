import { products as fallbackProducts } from "./products";
import { publicAssetUrl } from "./githubStore";

export async function loadPublicProducts() {
  try {
    const response = await fetch(
      `${import.meta.env.BASE_URL}data/products.json?time=${Date.now()}`,
      { cache: "no-store" }
    );
    if (!response.ok) throw new Error("Unable to load product catalogue.");
    const products = await response.json();
    return products.map((product) => ({
      ...product,
      image: publicAssetUrl(product.image),
    }));
  } catch (error) {
    console.error(error);
    return fallbackProducts;
  }
}
