import { Droplets, Brush, MoonStar, Sun, Sparkles } from "lucide-react";

const tips = [
  {
    icon: <Sparkles />,
    number: "01",
    title: "Oil Every 30 Days",
    text: "Apply a light coating of pure cow ghee, mustard oil, sandalwood oil or olive oil approximately once every 30 days. Allow it to absorb before wearing the Rudraksha again.",
  },
  {
    icon: <Brush />,
    number: "02",
    title: "Monthly Cleaning",
    text: "Place the Rudraksha briefly in hot water, gently brush each bead with a new soft toothbrush, rinse with clean water, and allow it to dry completely for a few hours. Once dry, apply a light coat of oil before wearing it again.",
  },
  {
    icon: <MoonStar />,
    number: "03",
    title: "While Sleeping",
    text: "If you move frequently while sleeping, it is better to remove the Rudraksha to avoid pressure on the natural bead surface. Keep it respectfully in your altar area, near your head, or beneath the pillow in a protective pouch.",
  },
  {
    icon: <Sun />,
    number: "04",
    title: "If the Beads Become Wet",
    text: "If the Rudraksha becomes thoroughly wet, allow it to dry completely in a well-ventilated place. Gentle sunlight for a limited period may be used, but avoid prolonged heat or harsh drying conditions.",
  },
];

export default function RudrakshaMaintenance() {
  return (
    <main className="rs-info-page">
      <div className="container rs-info-container">
      <section className="rs-info-hero maintenance-hero">
        <span>RUDRAKSHA CARE</span>
        <h1>Rudraksha Maintenance</h1>
        <p>Simple traditional care practices to help preserve the natural beauty and longevity of your Rudraksha.</p>
      </section>

      <section className="rs-info-section rs-intro">
        <div className="rs-section-title"><span>01</span><div><small>LONG-TERM CARE</small><h2>Keep Your Rudraksha in Good Condition</h2></div></div>
        <p>Once you begin wearing Rudraksha regularly, proper maintenance becomes important. Natural beads can collect moisture, dust and body oils over time, so gentle periodic care helps preserve their surface and structure.</p>
      </section>

      <section className="rs-info-section">
        <div className="rs-maintenance-grid">
          {tips.map((tip) => (
            <article className="rs-maintenance-card" key={tip.number}>
              <div className="rs-maintenance-number">{tip.number}</div>
              <div className="rs-maintenance-icon">{tip.icon}</div>
              <h2>{tip.title}</h2>
              <p>{tip.text}</p>
            </article>
          ))}
        </div>

        <div className="rs-callout rs-care-callout">
          <Droplets />
          <div><strong>Gentle Care Is Best</strong><p>Avoid detergents, harsh soaps, chemical cleaners and excessive force while brushing. Always allow the bead to dry completely before oiling and wearing it again.</p></div>
        </div>
      </section>

      <section className="rs-final-note">
        <div>📿</div>
        <div><small>DAILY CARE</small><h2>Make It Part of Your Routine</h2><p>Handle your Rudraksha respectfully, keep it clean, and inspect the thread or mounting periodically. Replace damaged thread or cord before continuing to wear it.</p></div>
      </section>
          </div>
    </main>
  );
}
