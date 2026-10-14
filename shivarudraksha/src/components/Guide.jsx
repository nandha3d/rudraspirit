import { MessageCircle } from "lucide-react";
import { products } from "../data/rudraksha";
import RudrakshaImage from "./RudrakshaImage";

export default function Guide({ setPage }) {
  return (
    <main>
      <section className="page-hero compact">
        <span className="eyebrow">RUDRAKSHA GUIDE</span>
        <h1>1 to 21 Mukhi Rudraksha Guide</h1>
        <p>Discover the traditional deity, planet, chakra and nakshatra associations for every Mukhi.</p>
      </section>

      <section className="guide-list container">
        {products.map(product => (
          <article className="guide-card" key={product.mukhi}>
            <div className="guide-image-column">
              <div className="guide-circle">
                <RudrakshaImage product={product} />
              </div>
              <strong>{product.mukhi} Mukhi</strong>
            </div>

            <div className="guide-copy">
              <span className="origin-pill">{product.origin}</span>
              <h2>{product.name}</h2>
              <p>{product.benefits}</p>
              <div className="guide-actions">
                <button className="primary-button" onClick={() => setPage("shop")}>View Product</button>
                <a className="text-button" href={`https://wa.me/14372671257?text=${encodeURIComponent(`Namaste, I would like to know more about ${product.name}.`)}`} target="_blank" rel="noreferrer">
                  <MessageCircle size={17}/> Chat
                </a>
              </div>
            </div>

            <dl className="attribute-grid">
              <div><dt>Ruling Deity</dt><dd>{product.deity}</dd></div>
              <div><dt>Planet</dt><dd>{product.planet}</dd></div>
              <div><dt>Chakra</dt><dd>{product.chakra}</dd></div>
              <div><dt>Nakshatra</dt><dd>{product.nakshatra}</dd></div>
              <div><dt>Beej Mantra</dt><dd>{product.mantra}</dd></div>
            </dl>
          </article>
        ))}
      </section>
    </main>
  );
}
