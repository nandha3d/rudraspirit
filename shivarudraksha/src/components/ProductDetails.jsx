import {
  ArrowLeft,
  CheckCircle2,
  MapPin,
  MessageCircle,
  Ruler,
  ShieldCheck,
  ShoppingBag,
  Zap,
  Sparkles,
  Phone,
  Mail,
  Clock,
  Star,
} from "lucide-react";
import { useEffect, useMemo, useState } from "react";
import Gallery from "./Gallery";
import Logo from "./Logo";
import PromotionalBanner from "./PromotionalBanner";

import ProductInteractiveTabs from "./ProductInteractiveTabs";
import ProductReviews from "./ProductReviews";
import RelatedProducts from "./RelatedProducts";
import RecentlyViewed from "./RecentlyViewed";
import CurrencySwitcher from "./CurrencySwitcher";
import { emptyImages } from "../data/catalogue";
import { addToCart, recordRecentProduct } from "../data/cartStore";
import { useCurrency } from "../context/CurrencyContext";

export default function ProductDetails({
  product,
  allProducts = [],
  onOpenProduct,
  onBack,
  onOpenCart,
}) {
  const { formatPrice, getTenantStockStatus } = useCurrency();
  // Record in recently viewed history on mount
  useEffect(() => {
    if (product) {
      recordRecentProduct(product);
    }
  }, [product?.id]);

  // Normalized variants
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
        name: "Medium Regular",
        price: Number(product.price || product.unit_price || 0),
        images: {
          ...emptyImages(),
          ...(product.images || {}),
        },
      },
    ];
  }, [product]);

  // Derive attribute groups from backend product.attributes or variants
  const attributes = useMemo(() => {
    // 1. Live backend attributes (Size, Certificate, etc.)
    if (product?.attributes && product.attributes.length > 0) {
      return product.attributes.filter((a) => a.values && a.values.length > 0);
    }

    // 2. Fallback static product or compound variants list
    if (variants && variants.length > 0) {
      const isCompound = variants.some((v) => v.name && v.name.includes("-"));
      if (isCompound) {
        const sizeSet = new Set();
        const certSet = new Set();
        const certKeyToLabel = {
          FreeCertificate: "Free Certificate",
          NoCertificate: "No Certificate",
          "X-RayCertificate": "X-Ray Certificate",
          "X-Ray": "X-Ray",
        };

        variants.forEach((v) => {
          const matchedSuffix = Object.keys(certKeyToLabel).find((k) =>
            v.name.endsWith(k)
          );
          if (matchedSuffix) {
            certSet.add(certKeyToLabel[matchedSuffix]);
            const sizePart = v.name
              .slice(0, -matchedSuffix.length)
              .replace(/-$/, "");
            if (sizePart === "Medium-Regular") sizeSet.add("Medium - Regular");
            else if (sizePart) sizeSet.add(sizePart.replace(/-/g, " "));
          } else {
            const parts = v.name.split("-");
            if (parts.length > 1) {
              certSet.add(parts[parts.length - 1]);
              sizeSet.add(parts.slice(0, -1).join(" "));
            } else if (v.name) {
              sizeSet.add(v.name);
            }
          }
        });

        const list = [];
        if (sizeSet.size > 0) {
          list.push({ id: 1, name: "Size", values: Array.from(sizeSet) });
        }
        if (certSet.size > 0) {
          list.push({ id: 3, name: "Certificate", values: Array.from(certSet) });
        }
        return list;
      }

      return [
        {
          id: 0,
          name: "Option",
          values: variants.map((v) => v.name).filter(Boolean),
        },
      ];
    }

    return [];
  }, [product?.attributes, variants]);

  // Selected Options State: { [attributeId]: value }
  const [selectedOptions, setSelectedOptions] = useState({});
  const [addedToast, setAddedToast] = useState(false);

  // Sync default selections when product or attributes change
  useEffect(() => {
    if (attributes && attributes.length > 0) {
      setSelectedOptions((prev) => {
        const next = {};
        attributes.forEach((attr) => {
          const key = attr.id ?? attr.name;
          if (prev[key] && attr.values.includes(prev[key])) {
            next[key] = prev[key];
          } else {
            const preferred =
              attr.values.find(
                (v) =>
                  v === "Medium - Regular" ||
                  v === "Medium Regular" ||
                  v === "Free Certificate" ||
                  v === "FreeCertificate"
              ) || attr.values[0];
            next[key] = preferred;
          }
        });
        return next;
      });
    }
  }, [product?.id, attributes]);

  const handleSelectOption = (attrKey, value) => {
    setSelectedOptions((prev) => ({
      ...prev,
      [attrKey]: value,
    }));
  };

  // Find matching variant based on selections
  const selectedVariant = useMemo(() => {
    if (!variants || variants.length === 0) return null;
    if (!attributes || attributes.length === 0) return variants[0];

    if (attributes.length === 1) {
      const key = attributes[0].id ?? attributes[0].name;
      const chosen = selectedOptions[key] || attributes[0].values[0];
      if (!chosen) return variants[0];
      const slugVal = chosen.replace(/\s+/g, "");
      return (
        variants.find((v) => v.name === chosen) ||
        variants.find((v) => v.name.replace(/\s+/g, "") === slugVal) ||
        variants[0]
      );
    }

    // Multiple attributes: Laravel removes all spaces and joins with '-'
    const slugParts = attributes.map((attr) => {
      const key = attr.id ?? attr.name;
      const val = selectedOptions[key] || attr.values[0] || "";
      return val.replace(/\s+/g, "");
    });
    const compoundSlug = slugParts.join("-");

    const matched =
      variants.find((v) => v.name === compoundSlug) ||
      variants.find(
        (v) => v.name.replace(/\s+/g, "") === compoundSlug.replace(/\s+/g, "")
      ) ||
      variants.find((v) => v.name.startsWith(slugParts[0])) ||
      variants[0];

    return matched;
  }, [variants, attributes, selectedOptions]);

  const galleryProduct = {
    ...product,
    images: selectedVariant?.images || product.images,
    fallbackImages: product.images || {},
  };

  const price = Number(
    selectedVariant?.price || product.price || product.unit_price || 0
  );

  const sizeAttribute = attributes.find((a) =>
    a.name.toLowerCase().includes("size")
  );
  const certAttribute = attributes.find((a) =>
    a.name.toLowerCase().includes("cert")
  );

  const selectedSizeLabel = sizeAttribute
    ? selectedOptions[sizeAttribute.id ?? sizeAttribute.name] ||
      sizeAttribute.values[0]
    : Object.values(selectedOptions)[0] || "Standard";

  const selectedCertLabel = certAttribute
    ? selectedOptions[certAttribute.id ?? certAttribute.name] ||
      certAttribute.values[0]
    : Object.values(selectedOptions)[1] || "Standard";

  const isPriceDisabled = Boolean(
    product.disable_price ||
    product.coming_soon ||
    price <= 0 ||
    product.stockStatus === "Coming Soon" ||
    product.stockStatus === "Out of Stock"
  );

  const isComingSoon = Boolean(
    product.coming_soon ||
    product.stockStatus === "Coming Soon" ||
    product.badge === "Coming Soon"
  );

  const handleAddToCart = () => {
    addToCart(
      product,
      selectedVariant,
      selectedSizeLabel,
      selectedCertLabel,
      1
    );

    setAddedToast(true);
    setTimeout(() => setAddedToast(false), 2400);

    if (onOpenCart) {
      onOpenCart();
    }
  };

  const handleBuyNow = () => {
    handleAddToCart();
    window.location.href = "/checkout";
  };

  const inquiryMessage = encodeURIComponent(
    `Hello Shiva Rudraksha Inc., I am interested in ${product.name} (${Object.values(
      selectedOptions
    ).join(", ")}). ${
      isPriceDisabled
        ? isComingSoon
          ? "Since this sacred bead is Coming Soon, please share advance allocation and dispatch details."
          : "Since the price is on request, please connect with me with current owner pricing and certification."
        : "Please share availability and dispatch schedule."
    }`
  );

  return (
    <div className="details-page">
      <PromotionalBanner />

      {/* Header Bar */}
      <header className="detail-header">
        <div className="container detail-header-inner">
          <button type="button" onClick={onBack} className="detail-back-button">
            <ArrowLeft size={18} />
            <span>Back to catalogue</span>
          </button>
          <div style={{ display: "flex", alignItems: "center", gap: "14px" }}>
            <CurrencySwitcher />
            <Logo compact />
          </div>
        </div>
      </header>

      {/* Main Two-Column Layout */}
      <main className="container details-layout">
        {/* Left Column: Gallery */}
        <Gallery product={galleryProduct} resetKey={selectedVariant?.name} />

        {/* Right Column: Product Information & Variant Selector */}
        <section className="product-information">
          <span className="premium-label">{product.badge || "Certified"} • {product.origin || "Nepal"} Rudraksha</span>
          <h1>{product.name}</h1>

          <p className="description">{product.description}</p>

          {/* Star Ratings & Verified Devotee Feedback Under Text (100% Dynamic from Database) */}
          {(() => {
            const currentReviews = Array.isArray(product.reviews) ? product.reviews : [];
            const reviewCount = typeof product.rating_count === "number" && product.rating_count > 0
              ? product.rating_count
              : currentReviews.length;
            const ratingValue = product.rating && Number(product.rating) > 0
              ? Number(product.rating)
              : (currentReviews.length > 0
                  ? (currentReviews.reduce((sum, r) => sum + Number(r.rating || 5), 0) / currentReviews.length)
                  : 0);
            const formattedRating = ratingValue > 0 ? ratingValue.toFixed(1) : "0.0";

            return (
              <div className="product-rating-under-text">
                <div
                  className="stars-cluster"
                  aria-label={reviewCount > 0 ? `Rating: ${formattedRating} out of 5 stars` : "No reviews yet"}
                >
                  {[1, 2, 3, 4, 5].map((s) => (
                    <Star
                      key={s}
                      size={15}
                      className="rating-star-icon"
                      fill={reviewCount > 0 && s <= Math.round(ratingValue) ? "#f59e0b" : "none"}
                      color={reviewCount > 0 && s <= Math.round(ratingValue) ? "#d97706" : "#cbd5e1"}
                    />
                  ))}
                </div>
                {reviewCount > 0 ? (
                  <>
                    <strong className="rating-score">{formattedRating}</strong>
                    <span className="rating-scale">/ 5.0</span>
                    <span className="rating-dot">•</span>
                    <button
                      type="button"
                      className="rating-reviews-link"
                      onClick={() => {
                        document.getElementById("reviews-section")?.scrollIntoView({ behavior: "smooth" });
                      }}
                    >
                      {reviewCount} {reviewCount === 1 ? "Verified Review" : "Verified Reviews"}
                    </button>
                  </>
                ) : (
                  <>
                    <span className="rating-scale" style={{ color: "#64748b" }}>No reviews yet</span>
                    <span className="rating-dot">•</span>
                    <button
                      type="button"
                      className="rating-reviews-link"
                      onClick={() => {
                        document.getElementById("reviews-section")?.scrollIntoView({ behavior: "smooth" });
                      }}
                    >
                      Write the first review
                    </button>
                  </>
                )}
                <span className="rating-dot">•</span>
                <span className="rating-trust-tag">
                  <ShieldCheck size={13} /> 100% Lab Verified
                </span>
              </div>
            );
          })()}

          {/* ================================================================
              NEAT ATTRIBUTE PILL SELECTOR (MATCHING ORIGINAL RUDRASPIRIT STOREFRONT)
              ================================================================ */}
          {attributes.length > 0 && (
            <div className="rs-variants-container">
              {attributes.map((attr) => {
                const attrKey = attr.id ?? attr.name;
                const currentVal = selectedOptions[attrKey] || attr.values[0];

                return (
                  <div key={attrKey} className="rs-variant-group">
                    <p className="rs-variant-label">{attr.name}</p>
                    <div
                      className="rs-variant-pills"
                      role="radiogroup"
                      aria-label={`Select ${attr.name}`}
                    >
                      {attr.values.map((val) => {
                        const isSelected = currentVal === val;

                        return (
                          <button
                            key={val}
                            type="button"
                            role="radio"
                            aria-checked={isSelected}
                            className={`rs-variant-pill ${
                              isSelected ? "selected" : ""
                            }`}
                            onClick={() => handleSelectOption(attrKey, val)}
                          >
                            <span>{val}</span>
                          </button>
                        );
                      })}
                    </div>
                  </div>
                );
              })}

              {/* Clean Configured Summary */}
              <div className="rs-selected-summary">
                <Sparkles size={14} className="config-sparkle" />
                <span>
                  Configured: <strong>{selectedSizeLabel}</strong>
                  {certAttribute && (
                    <>
                      {" "}with <strong>{selectedCertLabel}</strong>
                    </>
                  )}
                </span>
              </div>
            </div>
          )}

          {/* Dynamic Price Box OR Coming Soon Box */}
          {isPriceDisabled ? (
            <div className="price-box-enhanced coming-soon-box">
              <div className="price-info-left">
                <small>
                  {isComingSoon
                    ? "Sacred Harvest • Consecration in Progress"
                    : "Exclusive Sacred Heritage"}
                </small>
                <div className="price-headline-row">
                  <strong className="coming-soon-headline">
                    {isComingSoon ? "Coming Soon" : "Price on Request"}
                  </strong>
                  <span className="advance-allocation-tag">
                    {isComingSoon
                      ? "Advance Allocation Open"
                      : "Direct Owner Consultation"}
                  </span>
                </div>
                <p className="coming-soon-note">
                  {isComingSoon
                    ? "Authentic Himalayan Nepal bead currently undergoing Prana Pratishtha. Inquire below to connect directly with the owner for early allocation & certified dispatch."
                    : "Individual pricing is tailored to collector grade, dimensions, and certification. Please connect with the owner below for current quotation."}
                </p>
              </div>
              <div className="stock-status-pill coming-soon">
                <span className="stock-dot coming-soon-dot" />
                <span>
                  {isComingSoon ? "Coming Soon" : "Available on Request"}
                </span>
              </div>
            </div>
          ) : (
            <div className="price-box-enhanced">
              <div className="price-info-left">
                <small>Invest in Sacred Consecrated Heritage</small>
                <div className="price-headline-row">
                  <strong className="price-amount">
                    {formatPrice(price, selectedVariant || product)}
                  </strong>
                  <span className="tax-inclusive-tag">
                    Inclusive of all taxes
                  </span>
                </div>
              </div>
              <div className="stock-status-pill in-stock">
                <span className="stock-dot" />
                <span>{getTenantStockStatus(selectedVariant || product)} • Consecrated on Order</span>
              </div>
            </div>
          )}

          {/* What's Included Quick Visual Checklist */}
          <div className="included-list-enhanced">
            <span>
              <CheckCircle2 size={15} /> 100% Genuine Himalayan Nepal Origin
            </span>
            <span>
              <CheckCircle2 size={15} /> Free Prana Pratishtha Energization
              Vidhi
            </span>
            <span>
              <CheckCircle2 size={15} /> Physical Lab Certificate & Online QR
              Code
            </span>
            <span>
              <CheckCircle2 size={15} /> Insured Worldwide Express Delivery
            </span>
          </div>

          {/* Action Buttons: If price disabled, show Enquire / Contact Owner; otherwise Cart & Buy Now */}
          {isPriceDisabled ? (
            <div className="product-enquire-actions">
              <a
                className="enquire-owner-primary-btn"
                href={`https://wa.me/14372671257?text=${inquiryMessage}`}
                target="_blank"
                rel="noreferrer"
              >
                <MessageCircle size={22} />
                <div className="btn-text-block">
                  <span className="btn-main-title">Enquire & Contact Owner</span>
                  <small className="btn-sub-title">
                    Instant WhatsApp response • Personal guidance & Reservation
                  </small>
                </div>
              </a>

              <div className="secondary-enquiry-row">
                <a
                  className="enquire-secondary-btn"
                  href="tel:+14372671257"
                  title="Direct Phone Call"
                >
                  <Phone size={17} />
                  <span>Call 437-267-1257</span>
                </a>
                <a
                  className="enquire-secondary-btn"
                  href={`mailto:shivarudrakshain@gmail.com?subject=Enquiry for ${encodeURIComponent(
                    product.name
                  )}&body=${inquiryMessage}`}
                  title="Direct Email Enquiry"
                >
                  <Mail size={17} />
                  <span>Email Owner</span>
                </a>
              </div>
            </div>
          ) : (
            <>
              {/* E-Commerce Action Buttons: Add to Cart, Buy Now */}
              <div className="product-commerce-actions">
                <button
                  type="button"
                  className="add-to-cart-btn"
                  onClick={handleAddToCart}
                >
                  <ShoppingBag size={20} />
                  <span>Add to Sacred Cart</span>
                </button>

                <button
                  type="button"
                  className="buy-now-btn"
                  onClick={handleBuyNow}
                >
                  <Zap size={20} />
                  <span>Buy Now</span>
                </button>
              </div>

              <div className="product-whatsapp-action">
                <a
                  className="whatsapp-button-full"
                  href={`https://wa.me/14372671257?text=${inquiryMessage}`}
                  target="_blank"
                  rel="noreferrer"
                >
                  <MessageCircle size={20} />
                  <span>Consult Pandit / WhatsApp Inquiry</span>
                </a>
              </div>
            </>
          )}

          <p className="disclaimer">
            Traditional spiritual attributes are documented from sacred Puranic texts.
            Every bead is laboratory verified prior to dispatch.
          </p>
        </section>
      </main>

      {/* Floating Add to Cart Confirmation Toast */}
      {addedToast && (
        <div className="cart-toast-banner">
          <CheckCircle2 size={20} className="toast-check" />
          <div>
            <strong>Added to Sacred Cart</strong>
            <p>
              {product.name} ({selectedSizeLabel})
            </p>
          </div>
        </div>
      )}

      {/* Animated & Interactive Tabs (Spiritual Guide, Energization, Lab, FAQs) */}
      <ProductInteractiveTabs product={product} />

      {/* Devotee Customer Reviews Module */}
      <ProductReviews product={product} />

      {/* Related Complementary Beads Grid */}
      <RelatedProducts
        currentProduct={product}
        allProducts={allProducts}
        onOpenProduct={onOpenProduct}
      />

      {/* Recently Viewed Products */}
      <RecentlyViewed
        currentProductId={product.id}
        allProducts={allProducts}
        onOpenProduct={onOpenProduct}
      />
    </div>
  );
}
