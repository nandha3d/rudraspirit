import { useState } from "react";
import {
  Sparkles,
  Maximize2,
  Copy,
  Check,
  Download,
  BookOpen,
  Image as ImageIcon,
  X,
  Share2,
} from "lucide-react";
import { getSpiritualGuideForProduct } from "../data/spiritualGuideCatalogue";
import "../spiritualGuide.css";

// Decorative SVG Corner Filigree
function OrnateCorner({ position = "top-left" }) {
  const transforms = {
    "top-left": "",
    "top-right": "scaleX(-1)",
    "bottom-left": "scaleY(-1)",
    "bottom-right": "scale(-1, -1)",
  };

  return (
    <svg
      className={`sg-corner sg-corner-${position}`}
      viewBox="0 0 60 60"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      style={{ transform: transforms[position] }}
      aria-hidden="true"
    >
      <path
        d="M3 57 C3 25, 25 3, 57 3"
        stroke="url(#sg-gold-grad)"
        strokeWidth="2"
      />
      <path
        d="M7 57 C7 29, 29 7, 57 7"
        stroke="url(#sg-gold-grad)"
        strokeWidth="1"
        strokeDasharray="2 3"
      />
      <path
        d="M3 15 C10 10, 15 15, 12 25 C10 32, 22 28, 26 20 C30 12, 20 8, 15 12"
        stroke="url(#sg-gold-grad)"
        strokeWidth="1.2"
      />
      <circle cx="8" cy="8" r="3" fill="url(#sg-gold-grad)" />
      <circle cx="20" cy="5" r="1.5" fill="url(#sg-gold-grad)" />
      <circle cx="5" cy="20" r="1.5" fill="url(#sg-gold-grad)" />
      <path
        d="M20 20 L25 15 L30 20 L25 25 Z"
        fill="url(#sg-gold-grad)"
      />
    </svg>
  );
}

// Gold Medallion Icons for Vedic Specs & Benefits
function MedallionIcon({ type }) {
  switch (type) {
    case "trishul":
      return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
          <path d="M12 2v20M8 5c0 4 8 4 8 0M8 5V3M16 5V3M6 9a6 6 0 0 0 12 0" />
        </svg>
      );
    case "sun":
    case "leadership_sun":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="5" />
          <path d="M12 1v3M12 20v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M1 12h3M20 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
        </svg>
      );
    case "star":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
      );
    case "lotus":
    case "deep_meditation":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3c-1.5 3-4 6-4 9 0 2.5 1.8 4 4 4s4-1.5 4-4c0-3-2.5-6-4-9z" />
          <path d="M7.5 10c-2 2-3.5 4.5-3.5 7 0 2 1.5 3.5 3.5 3.5 2 0 3.5-1.5 4.5-3.5-2-.5-3.5-2.5-4.5-7z" opacity="0.85" />
          <path d="M16.5 10c2 2 3.5 4.5 3.5 7 0 2-1.5 3.5-3.5 3.5-2 0-3.5-1.5-4.5-3.5 2-.5 3.5-2.5 4.5-7z" opacity="0.85" />
        </svg>
      );
    case "people":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <circle cx="9" cy="7" r="3" />
          <path d="M2 18c0-3 3-5 7-5s7 2 7 5v1H2v-1z" />
          <circle cx="17" cy="8" r="2.5" />
          <path d="M16 14.2c1.5.5 3 1.5 3 3.8v1h3v-1c0-2.5-2.5-4-6-3.8z" />
        </svg>
      );
    case "calendar":
      return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
          <line x1="16" y1="2" x2="16" y2="6" />
          <line x1="8" y1="2" x2="8" y2="6" />
          <line x1="3" y1="10" x2="21" y2="10" />
          <circle cx="8" cy="15" r="1.2" fill="currentColor" />
          <circle cx="12" cy="15" r="1.2" fill="currentColor" />
          <circle cx="16" cy="15" r="1.2" fill="currentColor" />
        </svg>
      );
    case "om":
      return (
        <span style={{ fontSize: "16px", fontWeight: "bold", fontFamily: "serif" }}>ॐ</span>
      );
    case "shiva_dhyana":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="5" r="2.5" />
          <path d="M12 8c-3 0-5 2-5 5 0 2 1 3 2 4l-3 3c-.5.5-.2 1.5.5 1.5h15c.7 0 1-.9.5-1.5l-3-3c1-1 2-2 2-4 0-3-2-5-5-5zm0 2.5c1.8 0 3 1.2 3 3 0 1.5-.8 2.5-2 3l1 2h-4l1-2c-1.2-.5-2-1.5-2-3 0-1.8 1.2-3 3-3z" />
        </svg>
      );
    case "mental_focus":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C7 2 4 5 4 10c0 3 1.5 5 2.5 6.5.5.8.5 1.5.5 2.5v1h6v-1c0-1 .5-2 1-2.5 1.5-1.5 3-3.5 3-6.5 0-5-3-8-8-8z" />
          <circle cx="12" cy="9" r="1.8" fill="#fdf7ea" />
          <line x1="12" y1="5" x2="12" y2="7" stroke="#fdf7ea" strokeWidth="1.5" />
          <line x1="12" y1="11" x2="12" y2="13" stroke="#fdf7ea" strokeWidth="1.5" />
          <line x1="8" y1="9" x2="10" y2="9" stroke="#fdf7ea" strokeWidth="1.5" />
          <line x1="14" y1="9" x2="16" y2="9" stroke="#fdf7ea" strokeWidth="1.5" />
        </svg>
      );
    case "detachment_mountain":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 4l7 15H5l7-15z" />
          <path d="M12 4v7l3 2-3-4z" opacity="0.6" />
          <line x1="12" y1="2" x2="12" y2="6" stroke="#fdf7ea" strokeWidth="1.5" />
          <polygon points="12 2 15 3.5 12 5" fill="#fdf7ea" />
        </svg>
      );
    case "inner_peace_tree":
      return (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3c-3 0-5 2-5 4.5 0 1 .3 2 .8 2.8C6.3 11 5 12.8 5 15c0 2.5 2 4.5 4.5 4.8V22h5v-2.2c2.5-.3 4.5-2.3 4.5-4.8 0-2.2-1.3-4-2.8-4.7.5-.8.8-1.8.8-2.8 0-2.5-2-4.5-5-4.5z" />
          <line x1="12" y1="14" x2="12" y2="20" stroke="#fdf7ea" strokeWidth="2" />
        </svg>
      );
    default:
      return <Sparkles style={{ width: 16, height: 16 }} />;
  }
}

export default function ProductSpiritualGuide({ product }) {
  const guide = getSpiritualGuideForProduct(product);
  const [activeTab, setActiveTab] = useState("interactive"); // 'interactive' | 'brochure'
  const [copiedMantra, setCopiedMantra] = useState(false);
  const [lightboxImage, setLightboxImage] = useState(null);

  if (!guide) return null;

  const handleCopyMantra = () => {
    const mantraText = `${guide.deityMantraSanskrit} (${guide.deityMantraEnglish})`;
    navigator.clipboard?.writeText(mantraText);
    setCopiedMantra(true);
    setTimeout(() => setCopiedMantra(false), 2500);
  };

  const openInLightbox = (imgSrc, title) => {
    setLightboxImage({ src: imgSrc, title });
  };

  const closeLightbox = () => {
    setLightboxImage(null);
  };

  const baseUrl = import.meta.env.BASE_URL;

  return (
    <section className="spiritual-guide-section" aria-label="Spiritual Guide & Vedic Benefits">
      {/* SVG Definitions for rich metallic gold gradients */}
      <svg width="0" height="0" style={{ position: "absolute" }} aria-hidden="true">
        <defs>
          <linearGradient id="sg-gold-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#e5be59" />
            <stop offset="35%" stopColor="#fae7a5" />
            <stop offset="70%" stopColor="#d4af37" />
            <stop offset="100%" stopColor="#9c751e" />
          </linearGradient>
          <radialGradient id="sg-medallion-grad" cx="40%" cy="35%" r="60%">
            <stop offset="0%" stopColor="#fae5a0" />
            <stop offset="60%" stopColor="#c59b27" />
            <stop offset="100%" stopColor="#875c0c" />
          </radialGradient>
        </defs>
      </svg>

      <div className="container">
        {/* Section Header */}
        <div className="sg-section-header">
          <div className="sg-trident-crest">
            <span className="sg-crest-line" />
            <span className="sg-trident-symbol">🔱</span>
            <span className="sg-crest-line" />
          </div>
          <span className="sg-badge-kicker">SACRED VEDIC SPECIFICATIONS & BENEFITS</span>
          <h2 className="sg-main-title">{guide.title}</h2>
          <p className="sg-subtitle">
            Authentic divine guidance extracted directly from the sacred Shiva Rudraksha Inc. catalogue
          </p>

          {/* View Mode Switcher */}
          <div className="sg-view-switcher" role="tablist">
            <button
              type="button"
              role="tab"
              aria-selected={activeTab === "interactive"}
              className={`sg-switch-btn ${activeTab === "interactive" ? "active" : ""}`}
              onClick={() => setActiveTab("interactive")}
            >
              <BookOpen size={16} />
              Interactive Vedic Guide
            </button>
            <button
              type="button"
              role="tab"
              aria-selected={activeTab === "brochure"}
              className={`sg-switch-btn ${activeTab === "brochure" ? "active" : ""}`}
              onClick={() => setActiveTab("brochure")}
            >
              <ImageIcon size={16} />
              Authentic Reference Cards (from PDF)
            </button>
          </div>
        </div>

        {/* ============================================================ */}
        {/* TAB 1: INTERACTIVE SACRED VEDIC CARDS (Pure CSS/SVG/HTML)    */}
        {/* ============================================================ */}
        {activeTab === "interactive" && (
          <div className="sg-cards-spread">
            {/* ---------------- CARD 1: LEFT CARD (SPECS & DEITY) ---------------- */}
            <article className="sg-card sg-card-left">
              {/* Ornate Gold Filigree Corners */}
              <OrnateCorner position="top-left" />
              <OrnateCorner position="top-right" />
              <OrnateCorner position="bottom-left" />
              <OrnateCorner position="bottom-right" />

              {/* Card 1 Top Parchment Brand Header */}
              <div className="sg-card1-top-header">
                <div className="sg-brand-trident">
                  <span className="sg-small-filigree" />
                  <span className="sg-gold-trishul">🔱</span>
                  <span className="sg-small-filigree" />
                </div>
                <h3 className="sg-brand-name">{guide.brand}</h3>
                <div className="sg-brand-tagline">
                  {guide.tagline}
                </div>
              </div>

              {/* Lord Shiva / Mount Kailash Artwork Banner */}
              <div className="sg-deity-banner">
                <img
                  src={`${baseUrl}${guide.deityArt}`}
                  alt={`${guide.title} Deity Sacred Artwork`}
                  className="sg-deity-img"
                  loading="lazy"
                  decoding="async"
                  onError={(e) => {
                    e.currentTarget.src = `${baseUrl}${guide.brochureCards.specsCard}`;
                  }}
                />
              </div>

              {/* Card 1 Bottom Midnight Navy Panel */}
              <div className="sg-card1-bottom-panel">
                <div className="sg-product-lead">
                  <h4 className="sg-card-bead-title">{guide.title}</h4>
                  <div className="sg-card-bead-subtitle">{guide.subtitle}</div>
                  <div className="sg-gold-separator">
                    <span className="sg-sep-diamond">✦</span>
                  </div>
                </div>

                {/* Vedic Specifications Table */}
                <div className="sg-specs-table">
                  {guide.specs.map((item) => (
                    <div key={item.id} className="sg-spec-row">
                      <div className="sg-spec-label-col">
                        <span className="sg-gold-medallion-sm">
                          <MedallionIcon type={item.icon} />
                        </span>
                        <span className="sg-spec-label">{item.label}</span>
                      </div>
                      <div className="sg-spec-divider">|</div>
                      <div className="sg-spec-value-col">
                        <span className="sg-spec-value">{item.value}</span>
                        {item.id === "mantra" && (
                          <button
                            type="button"
                            className="sg-copy-mantra-btn"
                            onClick={handleCopyMantra}
                            title="Copy sacred mantra"
                          >
                            {copiedMantra ? <Check size={13} /> : <Copy size={13} />}
                            {copiedMantra ? "Copied" : "Copy"}
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>

                {/* Card 1 Footer */}
                <div className="sg-card1-footer">
                  <div className="sg-footer-motto">
                    <span className="sg-foot-diamond">✦</span>
                    <span>{guide.card1Footer}</span>
                    <span className="sg-foot-diamond">✦</span>
                  </div>
                </div>
              </div>
            </article>

            {/* ---------------- CARD 2: RIGHT CARD (TRADITIONAL BENEFITS) ---------------- */}
            <article className="sg-card sg-card-right">
              {/* Ornate Gold Filigree Corners */}
              <OrnateCorner position="top-left" />
              <OrnateCorner position="top-right" />
              <OrnateCorner position="bottom-left" />
              <OrnateCorner position="bottom-right" />

              {/* Card 2 Midnight Navy Header Banner */}
              <div className="sg-card2-header-banner">
                <h3 className="sg-card2-title">{guide.benefitsTitle}</h3>
                <div className="sg-card2-subtitle">
                  <em>{guide.benefitsSubtitle}</em>
                </div>
                <div className="sg-card2-header-divider">
                  <span className="sg-head-line" />
                  <span className="sg-head-diamond">✦</span>
                  <span className="sg-head-line" />
                </div>
              </div>

              {/* Card 2 Parchment Body with 6 Embossed Gold Medallions */}
              <div className="sg-card2-body">
                <div className="sg-benefits-list">
                  {guide.benefits.map((benefit, index) => (
                    <div key={benefit.id} className="sg-benefit-item">
                      <div className="sg-benefit-medallion-wrap">
                        <div className="sg-gold-medallion-lg">
                          <div className="sg-medallion-inner">
                            <MedallionIcon type={benefit.icon} />
                          </div>
                        </div>
                      </div>
                      <div className="sg-benefit-copy">
                        <h4 className="sg-benefit-heading">{benefit.title}</h4>
                        <p className="sg-benefit-desc">{benefit.text}</p>
                      </div>
                      {index < guide.benefits.length - 1 && (
                        <div className="sg-benefit-divider">
                          <span className="sg-div-diamond">✦</span>
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>

              {/* Card 2 Bottom Bar */}
              <div className="sg-card2-footer-bar">
                <div className="sg-card2-footer-content">
                  <span className="sg-foot-line" />
                  <span className="sg-foot-diamond">✦</span>
                  <strong className="sg-foot-mantra">{guide.card2Footer}</strong>
                  <span className="sg-foot-diamond">✦</span>
                  <span className="sg-foot-line" />
                </div>
              </div>
            </article>
          </div>
        )}

        {/* ============================================================ */}
        {/* TAB 2: AUTHENTIC REFERENCE CARDS (Direct from Master PDF)    */}
        {/* ============================================================ */}
        {activeTab === "brochure" && (
          <div className="sg-brochure-view">
            <div className="sg-brochure-intro">
              <p>
                Below is the exact 1:1 high-resolution page spread extracted directly from the official{" "}
                <strong>Shiva Rudraksha Inc. Reference Catalog</strong> (PDF). Click any card to expand in high-definition.
              </p>
            </div>

            <div className="sg-brochure-grid">
              <div
                className="sg-brochure-card-item"
                onClick={() =>
                  openInLightbox(
                    `${baseUrl}${guide.brochureCards.specsCard}`,
                    `${guide.title} — Vedic Specifications Card`
                  )
                }
              >
                <div className="sg-brochure-img-container">
                  <img
                    src={`${baseUrl}${guide.brochureCards.specsCard}`}
                    alt={`${guide.title} Vedic Specifications`}
                    loading="lazy"
                    decoding="async"
                  />
                  <div className="sg-brochure-overlay">
                    <Maximize2 size={24} />
                    <span>View Fullscreen</span>
                  </div>
                </div>
                <div className="sg-brochure-card-caption">
                  <strong>Page Left:</strong> Sacred Identity & Vedic Specifications
                </div>
              </div>

              <div
                className="sg-brochure-card-item"
                onClick={() =>
                  openInLightbox(
                    `${baseUrl}${guide.brochureCards.benefitsCard}`,
                    `${guide.title} — Traditional Benefits Card`
                  )
                }
              >
                <div className="sg-brochure-img-container">
                  <img
                    src={`${baseUrl}${guide.brochureCards.benefitsCard}`}
                    alt={`${guide.title} Traditional Benefits`}
                    loading="lazy"
                    decoding="async"
                  />
                  <div className="sg-brochure-overlay">
                    <Maximize2 size={24} />
                    <span>View Fullscreen</span>
                  </div>
                </div>
                <div className="sg-brochure-card-caption">
                  <strong>Page Right:</strong> Traditional Sacred Benefits
                </div>
              </div>
            </div>

            <div className="sg-brochure-actions">
              <a
                href={`${baseUrl}${guide.brochureCards.fullSpread}`}
                target="_blank"
                rel="noreferrer"
                className="sg-action-btn primary"
              >
                <Download size={16} />
                Open Full 2-Page Spread (Ultra-HD)
              </a>
              <button
                type="button"
                className="sg-action-btn secondary"
                onClick={() => {
                  const url = window.location.href;
                  if (navigator.share) {
                    navigator.share({ title: guide.title, url });
                  } else {
                    navigator.clipboard.writeText(url);
                    alert("Page link copied to clipboard!");
                  }
                }}
              >
                <Share2 size={16} />
                Share Guide
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Lightbox Modal for High-Def Inspection */}
      {lightboxImage && (
        <div className="sg-lightbox" onClick={closeLightbox} role="dialog" aria-modal="true">
          <div className="sg-lightbox-content" onClick={(e) => e.stopPropagation()}>
            <button
              type="button"
              className="sg-lightbox-close"
              onClick={closeLightbox}
              aria-label="Close high-definition viewer"
            >
              <X size={24} />
            </button>
            <img src={lightboxImage.src} alt={lightboxImage.title} className="sg-lightbox-img" />
            <div className="sg-lightbox-caption">{lightboxImage.title}</div>
          </div>
        </div>
      )}
    </section>
  );
}
