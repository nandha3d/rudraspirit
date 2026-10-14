import { MoonStar } from "lucide-react";

const guidance = [
  ["Funerals", "Traditionally, Rudraksha is removed before attending funerals or cremation ceremonies."],
  ["Women Can Wear Rudraksha", "Traditional Rudraksha guidance does not universally prohibit women from wearing Rudraksha during menstruation. Practices may vary by family, lineage and personal belief."],
  ["Do Not Share Worn Rudraksha", "Once a Rudraksha has been personally worn, it is traditionally recommended not to exchange or transfer it to another person."],
  ["Wear Close to the Body", "When worn as a mala or pendant, keep the Rudraksha comfortably close to the body. Malas are traditionally worn around the neck."],
  ["Thread or Metal", "Rudraksha may be worn using black, yellow, white or red thread, or mounted in silver, gold, copper or another suitable metal."],
  ["Keep It Personal", "Treat your Rudraksha as a personal spiritual item and avoid unnecessary handling by other people."],
  ["Wear Consistently", "Traditional practice encourages wearing Rudraksha consistently rather than frequently putting it on and removing it."],
  ["Daily Practice", "Wear your Rudraksha naturally and respectfully as part of your everyday spiritual practice."],
  ["Lifestyle", "Different traditions have different recommendations regarding food, alcohol and smoking. Follow the spiritual discipline that is meaningful to you."],
  ["Keep It Close", "Keep the beads close to you when possible. Rudraksha may also be respectfully kept in a Puja room for family worship."],
  ["If the Thread Breaks", "If the thread or cord breaks, replace it with a new clean thread or suitable chain and continue wearing the Rudraksha."],
];

const base = import.meta.env.BASE_URL;

const steps = [
  { title: "Cleanse", image: `${base}images/knowledge/step-1-cleanse.png`, alt: "Step 1 cleanse Rudraksha with clean plain water" },
  { title: "Turmeric Water", image: `${base}images/knowledge/step-2-turmeric-water.png`, alt: "Step 2 wash Rudraksha with turmeric water" },
  { title: "Soak in Cow Ghee", image: `${base}images/knowledge/step-3-cow-ghee.png`, alt: "Step 3 soak Rudraksha in cow ghee for 24 hours" },
  { title: "Soak in Cow Milk", image: `${base}images/knowledge/step-4-cow-milk.png`, alt: "Step 4 soak Rudraksha in full-fat cow milk for 24 hours" },
  { title: "Remove & Dry", image: `${base}images/knowledge/step-5-remove-dry.png`, alt: "Step 5 remove Rudraksha and dry it carefully" },
  { title: "Chant & Energize", image: `${base}images/knowledge/step-6-chant-energize.png`, alt: "Step 6 chant Om Namah Shivaya and energize Rudraksha" },
];

export default function RudrakshaKnowledge() {
  return (
    <main className="rs-info-page">
      <div className="container rs-info-container">
      <section className="rs-info-hero">
        <span>RUDRAKSHA KNOWLEDGE</span>
        <h1>Basic Energizing Method</h1>
        <p>Traditional guidance for preparing, wearing and respecting your Rudraksha.</p>
      </section>

      <section className="rs-info-section rs-intro">
        <div className="rs-section-title"><span>01</span><div><small>TRADITIONAL GUIDANCE</small><h2>Before You Begin</h2></div></div>
        <p>Rudraksha has been revered for centuries in Hindu spiritual traditions and is especially associated with Lord Shiva. References to Rudraksha are found across Shaiva and Puranic traditions, including the Shiva Purana and Devi Bhagavata Purana.</p>
        <p>Rudraksha may traditionally be worn by people of different ages and backgrounds. Customs vary between families, teachers and spiritual lineages, so the following method is presented as a simple traditional practice.</p>
      </section>

      <section className="rs-info-section">
        <div className="rs-section-title"><span>02</span><div><small>STEP BY STEP</small><h2>How to Energize Your Rudraksha</h2></div></div>
        <div className="rs-energize-timeline">
          {steps.map((step, index) => (
            <article className={`rs-timeline-step ${index % 2 ? "rs-timeline-step--reverse" : ""}`} key={step.title}>
              <div className="rs-timeline-image-wrap">
                <img src={step.image} alt={step.alt} loading="lazy" />
              </div>
              <div className="rs-timeline-marker" aria-hidden="true">
                <span>{String(index + 1).padStart(2, "0")}</span>
              </div>
              <div className="rs-timeline-copy">
                <small>STEP {index + 1}</small>
                <h3>{step.title}</h3>
                <p>{step.alt.replace(/^Step \d+ /, "")}</p>
              </div>
            </article>
          ))}
        </div>
        <div className="rs-callout"><MoonStar /><div><strong>Traditional Wearing Day</strong><p>Monday is traditionally considered especially auspicious for energizing and wearing Rudraksha.</p></div></div>
      </section>

      <section className="rs-info-section rs-dark-section">
        <div className="rs-section-title"><span>03</span><div><small>CARE & TRADITION</small><h2>Do’s & Don’ts</h2></div></div>
        <div className="rs-guidance-grid">
          {guidance.map(([title, text], index) => (
            <article className="rs-guidance-card" key={title}>
              <span>{String(index + 1).padStart(2, "0")}</span>
              <div><h3>{title}</h3><p>{text}</p></div>
            </article>
          ))}
        </div>
      </section>

      <section className="rs-final-note">
        <div>🔱</div>
        <div><small>MOST IMPORTANT</small><h2>Respect the Rudraksha</h2><p>Rudraksha is traditionally regarded as sacred and associated with Lord Shiva. Wear and care for it respectfully, and never use spiritual practices with the intention of harming or manipulating another person.</p></div>
      </section>

      <section className="rs-disclaimer"><strong>Traditional & Cultural Information</strong><p>Information on this page reflects traditional, spiritual and cultural beliefs associated with Rudraksha. It is not medical, psychological, financial or professional advice.</p></section>
          </div>
    </main>
  );
}
