import { MapPin, MessageCircle, ShieldCheck } from "lucide-react";
import Gallery from "./Gallery";
import { useCurrency } from "../context/CurrencyContext";

export default function ProductCard({ product, onOpen }) {
  const { formatPrice } = useCurrency();
  const availablePrices = (product.variants || [])
    .map((variant) => Number(variant.price))
    .filter((price) => Number.isFinite(price) && price > 0);

  const startingPrice =
    availablePrices.length > 0
      ? Math.min(...availablePrices)
      : Number(product.price || product.unit_price) > 0
      ? Number(product.price || product.unit_price)
      : null;

  const isPriceDisabled = Boolean(
    product.disable_price ||
    product.coming_soon ||
    !startingPrice ||
    product.stockStatus === "Coming Soon"
  );

  const isComingSoon = Boolean(
    product.coming_soon ||
    product.stockStatus === "Coming Soon" ||
    product.badge === "Coming Soon"
  );

  const isAvailable = startingPrice !== null && !isComingSoon;

  const message = encodeURIComponent(
    `Hello Shiva Rudraksha Inc., I am interested in ${product.name}. ${
      isPriceDisabled
        ? isComingSoon
          ? "Since it is Coming Soon, please share advance allocation and dispatch details."
          : "Please share pricing, certificate and size availability."
        : "Please share details, size availability, certificate and price."
    }`
  );

  return (
    <article className="product-card">
      <div className="card-open">
        <Gallery product={product} compact onOpen={() => onOpen(product)} />
      </div>

      <div className="card-content">
        <div className="card-badges">
          <span>{product.badge}</span>
          <span>{isComingSoon ? "Coming Soon" : isAvailable ? product.stockStatus : "Available"}</span>
        </div>

        <h3 className="product-title-wrap">
          <button
            type="button"
            className="product-name"
            onClick={() => onOpen(product)}
            aria-label={`View details for ${product.name}`}
          >
            {product.name}
          </button>
        </h3>

        <p>{product.description}</p>

        <div className="card-meta">
          <span>
            <MapPin />
            {product.origin}
          </span>

          <span>
            <ShieldCheck />
            {product.certificateAvailable
              ? "Certificate included"
              : "No Certificate"}
          </span>
        </div>

        <div className="card-bottom">
          <div>
            <small
              style={{
                textTransform: "uppercase",
                fontSize: "11px",
                color: "var(--muted)",
              }}
            >
              {isPriceDisabled ? "Status" : "Price"}
            </small>
            {isPriceDisabled ? (
              <strong
                style={{
                  fontSize: "13.5px",
                  color: isComingSoon ? "#b45309" : "var(--maroon)",
                  fontFamily: "Georgia, serif",
                  lineHeight: 1.2,
                  fontWeight: 700,
                }}
              >
                {isComingSoon ? "Coming Soon" : "On Request"}
              </strong>
            ) : (
              <strong
                style={{
                  fontSize: "14px",
                  color: "var(--maroon)",
                  fontFamily: "Georgia, serif",
                  lineHeight: 1.2,
                  fontWeight: 800,
                }}
              >
                {formatPrice(startingPrice, product)}
              </strong>
            )}
          </div>

          <button
            type="button"
            className="details-button"
            onClick={() => onOpen(product)}
            aria-label={`View details for ${product.name}`}
          >
            View details
          </button>

          <a
            href={`https://wa.me/14372671257?text=${message}`}
            target="_blank"
            rel="noreferrer"
            title={`Enquire about ${product.name} on WhatsApp`}
            aria-label={`Enquire about ${product.name} on WhatsApp`}
          >
            <MessageCircle aria-hidden="true" />
            Enquire
          </a>
        </div>
      </div>
    </article>
  );
}