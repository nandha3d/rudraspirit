import Logo from "./Logo";
import { MessageCircle, Phone, Mail, MapPin, ShieldCheck, Sparkles } from "lucide-react";

export default function Footer({ onNavigate }) {
  const handleNav = (hash) => {
    if (onNavigate) {
      onNavigate(hash);
    } else {
      window.location.hash = hash;
    }
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <footer className="luxury-site-footer">
      <div className="container footer-layout">
        {/* Brand & Mission Column */}
        <div className="footer-brand-col">
          <Logo />
          <p className="footer-tagline">
            Carefully selected, authentic Rudraksha beads directly sourced from Nepal and Indonesia. Each bead individually inspected, photographed, lab certified, and ritually consecrated with Vedic Prana Pratishtha.
          </p>

          <div className="footer-wa-badge-row">
            <a
              href="https://wa.me/14372671257"
              target="_blank"
              rel="noreferrer"
              className="footer-wa-pill"
            >
              <MessageCircle size={15} />
              <span>WhatsApp Consultation</span>
            </a>
          </div>
        </div>

        {/* Vedic Guide & Sacred Heritage */}
        <div className="footer-links-col">
          <strong className="footer-col-title">Vedic Guide & Heritage</strong>
          <nav className="footer-links-list">
            <button type="button" onClick={() => handleNav("#guide")}>
              1–21 Mukhi Guide
            </button>
            <button type="button" onClick={() => handleNav("#recommendations")}>
              Birth Chart Recommendation
            </button>
            <button type="button" onClick={() => handleNav("#knowledge")}>
              Energizing & Vedic Knowledge
            </button>
            <button type="button" onClick={() => handleNav("#maintenance")}>
              Bead Care & Maintenance
            </button>
            <button type="button" onClick={() => handleNav("#lord-shiva")}>
              Who is Lord Shiva?
            </button>
            <button type="button" onClick={() => handleNav("#about")}>
              About Shiva Rudraksha
            </button>
          </nav>
        </div>

        {/* E-Commerce & Guarantees */}
        <div className="footer-links-col">
          <strong className="footer-col-title">Sacred Standards</strong>
          <div className="footer-standards-list">
            <div className="footer-standard-item">
              <ShieldCheck size={16} className="standard-icon" />
              <div>
                <strong>100% Genuine Nepal Origin</strong>
                <small>Natural mukhi lines with digital X-Ray verification</small>
              </div>
            </div>
            <div className="footer-standard-item">
              <Sparkles size={16} className="standard-icon" />
              <div>
                <strong>Prana Pratishtha Energization</strong>
                <small>Consecrated with sacred Panchamrit by Vedic pandits</small>
              </div>
            </div>
            <div className="footer-standard-item">
              <span className="standard-badge-pill">Worldwide</span>
              <div>
                <strong>Insured Express Delivery</strong>
                <small>Fast insured shipping across Canada, USA & worldwide</small>
              </div>
            </div>
          </div>
        </div>

        {/* Direct Contact & Sourcing */}
        <div className="footer-links-col">
          <strong className="footer-col-title">Personal Contact</strong>
          <div className="footer-contact-info">
            <a href="tel:+14372671257" className="footer-contact-link">
              <Phone size={15} />
              <span>437-267-1257</span>
            </a>
            <a href="mailto:shivarudrakshain@gmail.com" className="footer-contact-link">
              <Mail size={15} />
              <span>shivarudrakshain@gmail.com</span>
            </a>
            <div className="footer-contact-location">
              <MapPin size={15} />
              <span>Scarborough, Ontario, Canada</span>
            </div>
            <p className="footer-reply-pledge">
              We reply personally. Speak directly with knowledgeable advisors for bead selection & astrological alignment.
            </p>
          </div>
        </div>
      </div>

      {/* Bottom Legal / Copyright Bar */}
      <div className="footer-bottom-bar">
        <div className="container footer-bottom-inner">
          <span>&copy; {new Date().getFullYear()} Shiva Rudraksha Inc. All Rights Reserved.</span>
          <span className="footer-bottom-meta">
            Direct Nepal Sourcing • 100% Lab Verified • Worldwide Delivery
          </span>
        </div>
      </div>
    </footer>
  );
}
