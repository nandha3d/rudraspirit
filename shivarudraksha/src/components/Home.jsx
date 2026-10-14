import { ArrowRight, BadgeCheck, Globe2, ScanLine, MessageCircle } from "lucide-react";

export default function Home({ setPage }) {
  return (
    <main>
      <section className="hero">
        <div className="hero-copy">
          <span className="eyebrow">AUTHENTIC • CERTIFIED • SACRED</span>
          <h1>Divine Rudraksha, chosen with care.</h1>
          <p>Authentic Nepal and Indonesian Rudraksha supported by certificate and X-ray verification, delivered from Canada to devotees worldwide.</p>
          <div className="hero-buttons">
            <button className="primary-button" onClick={()=>setPage("shop")}>Explore Collection <ArrowRight size={18}/></button>
            <a className="outline-button" href="https://wa.me/14372671257" target="_blank" rel="noreferrer"><MessageCircle size={18}/> Chat</a>
          </div>
        </div>
        <div className="hero-orbit">
          <div className="hero-bead">ॐ</div>
          <span className="orbit orbit-one"></span>
          <span className="orbit orbit-two"></span>
        </div>
      </section>

      <section className="trust-grid container">
        <article><BadgeCheck/><h3>Authentic Products</h3><p>Carefully sourced Rudraksha selected for natural Mukhi formation.</p></article>
        <article><ScanLine/><h3>X-ray Verified</h3><p>Internal Mukhi structure can be presented through supporting X-ray images.</p></article>
        <article><BadgeCheck/><h3>Certificate Included</h3><p>Certificate imagery is available alongside the product gallery.</p></article>
        <article><Globe2/><h3>Worldwide Support</h3><p>Personal consultation and order support through WhatsApp.</p></article>
      </section>
    </main>
  );
}
