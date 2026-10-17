import { ArrowRight, Sparkles, ShieldCheck } from "lucide-react";
import { useCurrency } from "../context/CurrencyContext";

export default function RelatedProducts({ currentProduct, allProducts = [], onOpenProduct }) {
  const { formatPrice } = useCurrency();
  if (!allProducts || allProducts.length <= 1) return null;

  // Filter out the current product
  const candidates = allProducts.filter((p) => p.id !== currentProduct.id);

  // Pick 4 related products: prioritize special forms or adjacent mukhis
  const related = candidates
    .sort((a, b) => {
      // Prioritize 1 Mukhi, Gauri Shankar, Siddha Mala or similar
      const priorityA = a.mukhi === 1 || a.name.includes("Gauri") || a.name.includes("Siddha") ? 1 : 0;
      const priorityB = b.mukhi === 1 || b.name.includes("Gauri") || b.name.includes("Siddha") ? 1 : 0;
      return priorityB - priorityA;
    })
    .slice(0, 4);

  if (related.length === 0) return null;

  return (
    <section className="related-products-section">
      <div className="container">
        <div className="related-section-header">
          <div>
            <span className="related-kicker">COMPLEMENTARY SACRED BEADS</span>
            <h2>Often Worn & Paired Together</h2>
            <p>
              Traditional Vedic combinations that harmonize spiritual energies with {currentProduct.name}.
            </p>
          </div>
        </div>

        <div className="related-products-grid">
          {related.map((prod) => {
            const displayPrice = Number(prod.price || prod.unit_price || 0);
            const thumb =
              prod.images?.front ||
              prod.images?.top ||
              prod.photos?.[0] ||
              prod.thumbnail_url ||
              "/images/brand/shiva-rudraksha-logo.png";

            return (
              <div
                key={prod.id}
                className="related-product-card"
                onClick={() => onOpenProduct(prod)}
              >
                <div className="related-thumb-wrap">
                  <img
                    src={thumb}
                    alt={prod.name}
                    onError={(e) => {
                      e.currentTarget.src = "/images/brand/shiva-rudraksha-logo.png";
                    }}
                  />
                  <span className="related-badge-pill">{prod.origin || "Nepal"}</span>
                </div>

                <div className="related-card-content">
                  <div className="related-origin-row">
                    <ShieldCheck size={14} className="related-shield" />
                    <span>Government Certified</span>
                  </div>

                  <h3>{prod.name}</h3>

                  <div className="related-price-row">
                    <div>
                      <small>Starting from</small>
                      <strong>
                        {formatPrice(displayPrice, prod)}
                      </strong>
                    </div>

                    <button
                      type="button"
                      className="related-view-btn"
                      onClick={(e) => {
                        e.stopPropagation();
                        onOpenProduct(prod);
                      }}
                    >
                      <span>View</span>
                      <ArrowRight size={14} />
                    </button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
