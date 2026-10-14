import { assetUrl } from "../data/catalogue";

export default function Logo({ compact = false }) {
  return (
    <a className={compact ? "brand-logo compact" : "brand-logo"} href="./">
      <img
        src={assetUrl("images/brand/shiva-rudraksha-logo.png")}
        alt="Shiva Rudraksha Inc."
      />
      {!compact && (
        <span>
          <strong>SHIVA RUDRAKSHA INC.</strong>
          <small>Authentic • Certified • Canada</small>
        </span>
      )}
    </a>
  );
}
