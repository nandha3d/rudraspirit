import { BadgeCheck, Gift, MessageCircle, Sparkles, Truck } from "lucide-react";

const promotions = [
  { icon: BadgeCheck, text: "Authentic Rudraksha with certificate and X-ray" },
  { icon: Truck, text: "Shipping available across Canada, USA and worldwide" },
  { icon: Gift, text: "Custom malas, bracelets and Rudraksha combinations available" },
  { icon: Sparkles, text: "Personal Rudraksha recommendation based on birth details" },
  { icon: MessageCircle, text: "Chat with us on WhatsApp: +1 437-267-1257" },
];

export default function PromotionalBanner() {
  const repeatedPromotions = [...promotions, ...promotions];

  return (
    <section className="promotional-banner" aria-label="Current promotions">
      <div className="promotional-live-label">
        <span className="promotional-live-dot" aria-hidden="true" />
        LIVE
      </div>

      <div className="promotional-feed">
        <div className="promotional-track">
          {repeatedPromotions.map(({ icon: Icon, text }, index) => (
            <span className="promotional-item" key={`${text}-${index}`}>
              <Icon aria-hidden="true" />
              {text}
            </span>
          ))}
        </div>
      </div>

      <a
        className="promotional-cta"
        href="https://wa.me/14372671257?text=Hello%20Shiva%20Rudraksha%20Inc.%2C%20I%20would%20like%20to%20know%20about%20your%20current%20offers."
        target="_blank"
        rel="noreferrer"
      >
        Enquire
      </a>
    </section>
  );
}
