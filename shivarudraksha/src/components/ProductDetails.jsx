import {
  ArrowLeft,
  CheckCircle2,
  MapPin,
  MessageCircle,
  Ruler,
  ShieldCheck,
} from "lucide-react";
import { useEffect, useMemo, useState } from "react";
import Gallery from "./Gallery";
import Logo from "./Logo";
import PromotionalBanner from "./PromotionalBanner";
import { emptyImages } from "../data/catalogue";

export default function ProductDetails({ product, onBack }) {
  const variants = useMemo(() => {
    if (Array.isArray(product.variants) && product.variants.length) {
      return product.variants.map((variant) => ({
        ...variant,
        price: Number(variant.price || 0),
        images: Object.fromEntries(
          Object.keys(emptyImages()).map((key) => [
            key,
            variant.images?.[key] || product.images?.[key] || "",
          ])
        ),
      }));
    }

    return [
      {
        name: "Medium",
        price: Number(product.price || 0),
        images: {
          ...emptyImages(),
          ...(product.images || {}),
        },
      },
    ];
  }, [product]);

  const firstAvailableVariant = useMemo(
    () => variants.find((variant) => variant.price > 0) || variants[0],
    [variants]
  );

  const [selectedVariantName, setSelectedVariantName] = useState(
    firstAvailableVariant?.name || ""
  );

  useEffect(() => {
    setSelectedVariantName(firstAvailableVariant?.name || "");
  }, [product.id, firstAvailableVariant]);

  const selectedVariant =
    variants.find((variant) => variant.name === selectedVariantName) ||
    firstAvailableVariant ||
    variants[0];

  const galleryProduct = {
    ...product,
    images: selectedVariant?.images || product.images,
    fallbackImages: product.images || {},
  };

  const price = Number(selectedVariant?.price || 0);
  const isSelectedVariantAvailable = price >= 0;

  const message = encodeURIComponent(
    `Hello Shiva Rudraksha Inc., I am interested in ${
      product.name
    } (${selectedVariant?.name || "Standard"} size). Please share size availability, certificate details and price.`
  );

  return (
    <div className="details-page">
      <PromotionalBanner />

      <header className="detail-header">
        <div className="container detail-header-inner">
          <button type="button" onClick={onBack}>
            <ArrowLeft />
            Back to catalogue
          </button>
          <Logo compact />
        </div>
      </header>

      <main className="container details-layout">
        <Gallery product={galleryProduct} resetKey={selectedVariant?.name} />

        <section className="product-information">
          <span className="premium-label">{product.badge}</span>
          <h1>{product.name}</h1>

          <div className="information-grid">
            <span><MapPin />Origin<strong>{product.origin}</strong></span>
            <span><Ruler />Variants<strong>{variants.length} sizes</strong></span>
            <span>
              <ShieldCheck />Certificate
              <strong>{product.certificateAvailable ? "Included" : "Not Included"}</strong>
            </span>
          </div>

          <p className="description">{product.description}</p>

          <div className="variant-selector">
            <label>Select bead size</label>
            <div className="variant-options" role="group" aria-label="Select bead size">
              {variants.map((variant) => {
                const variantPrice = Number(variant.price || 0);
                const isAvailable = variantPrice >= 0;
                const isSelected = variant.name === selectedVariant?.name;

                return (
                  <button
                    key={variant.name}
                    type="button"
                    disabled={!isAvailable}
                    className={["variant-option", isSelected ? "selected" : "", !isAvailable ? "disabled" : ""]
                      .filter(Boolean)
                      .join(" ")}
                    aria-pressed={isSelected}
                    aria-disabled={!isAvailable}
                    onClick={() => isAvailable && setSelectedVariantName(variant.name)}
                  >
                    <span>{variant.name}</span>
                    <strong>Available</strong>
                  </button>
                );
              })}
            </div>
            <small>The gallery, certificate and X-ray details change with the selected size.</small>
          </div>

          <div className="price-box">
            <small>{selectedVariant?.name || "Authentic Nepal Bead"}</small>
            <strong style={{ color: "var(--maroon)", fontSize: "24px", fontFamily: "Georgia, serif" }}>Price on Request</strong>
          </div>

          <div className="included-list">
            <span><CheckCircle2 />Front view</span>
            <span><CheckCircle2 />Back view</span>
            <span><CheckCircle2 />Top view</span>
            <span><CheckCircle2 />Bottom view</span>
            <span><CheckCircle2 />{product.certificateAvailable ? "Certificate" : "No Certificate"}</span>
            <span><CheckCircle2 />{product.certificateAvailable ? "X-ray" : "No X-ray"}</span>
          </div>

          <div className="product-action-buttons">
            <a
              className="whatsapp-button"
              href={`https://wa.me/14372671257?text=${message}`}
              target="_blank"
              rel="noreferrer"
            >
              <MessageCircle />
              Contact for Details via WhatsApp
            </a>
          </div>

          <p className="disclaimer">
            Traditional spiritual descriptions are informational and are not medical or financial guarantees.
          </p>
        </section>
      </main>
    </div>
  );
}
