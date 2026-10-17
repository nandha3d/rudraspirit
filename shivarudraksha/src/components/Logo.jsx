import { assetUrl } from "../data/catalogue";

export default function Logo({ compact = false }) {
  return (
    <a className={compact ? "brand-logo compact" : "brand-logo"} href="./" aria-label="Shiva Rudraksha Inc. Home">
      <picture>
        <source srcSet={assetUrl("images/brand/shiva-rudraksha-logo.avif")} type="image/avif" />
        <source srcSet={assetUrl("images/brand/shiva-rudraksha-logo.webp")} type="image/webp" />
        <img
          src={assetUrl("images/brand/shiva-rudraksha-logo.png")}
          alt="Shiva Rudraksha Inc."
          width={compact ? "50" : "45"}
          height={compact ? "50" : "45"}
          fetchPriority="high"
          decoding="async"
        />
      </picture>
      {!compact && (
        <span>
          <strong>SHIVA RUDRAKSHA INC.</strong>
          <small>Authentic • Certified • Canada</small>
        </span>
      )}
    </a>
  );
}
