import { useMemo, useState } from "react";
import { Search, MessageCircle, ShieldCheck } from "lucide-react";
import { products } from "../data/rudraksha";
import RudrakshaImage from "./RudrakshaImage";

export default function Shop() {
  const [query, setQuery] = useState("");
  const [origin, setOrigin] = useState("All");

  const filtered = useMemo(() => products.filter(p => {
    const text = `${p.name} ${p.deity} ${p.planet} ${p.chakra} ${p.benefits}`.toLowerCase();
    return text.includes(query.toLowerCase()) && (origin === "All" || p.origin === origin);
  }), [query, origin]);

  return (
    <main>
      <section className="page-hero compact">
        <span className="eyebrow">AUTHENTIC COLLECTION</span>
        <h1>Certified Rudraksha Catalogue</h1>
        <p>Browse authentic Rudraksha with six-view photography, certificate and X-ray verification.</p>
      </section>

      <section className="shop-toolbar container">
        <label className="search-box"><Search size={19}/><input value={query} onChange={e=>setQuery(e.target.value)} placeholder="Search by Mukhi, deity, planet or benefit"/></label>
        <div className="filter-pills">
          {["All","Nepal","Indonesia"].map(v => <button className={origin===v?"selected":""} onClick={()=>setOrigin(v)} key={v}>{v}</button>)}
        </div>
      </section>

      <section className="product-grid container">
        {filtered.map(product => (
          <article className="product-card" key={product.mukhi}>
            <div className="product-image-wrap"><RudrakshaImage product={product}/></div>
            <div className="product-content">
              <div className="product-meta"><span>{product.origin}</span><span><ShieldCheck size={15}/> Certified</span></div>
              <h2>{product.name}</h2>
              <p>{product.benefits}</p>
              <div className="product-facts"><span>{product.deity}</span><span>{product.planet}</span></div>
              <a className="primary-button full" href={`https://wa.me/14372671257?text=${encodeURIComponent(`Namaste, I am interested in ${product.name} (${product.origin}). Please share price and availability.`)}`} target="_blank" rel="noreferrer">
                <MessageCircle size={18}/> Chat
              </a>
            </div>
          </article>
        ))}
      </section>
    </main>
  );
}
