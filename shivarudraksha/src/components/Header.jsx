import { Menu, X, MessageCircle } from "lucide-react";
import { useState } from "react";

export default function Header() {
  const [open, setOpen] = useState(false);

  const scrollTo = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth" });
    setOpen(false);
  };

  return (
    <header className="site-header">
      <div className="container header-inner">
        <button className="brand" onClick={() => scrollTo("home")} aria-label="Go to homepage">
          <span className="brand-mark">ॐ</span>
          <span>
            <strong>SHIVA RUDRAKSHA</strong>
            <small>INC.</small>
          </span>
        </button>

        <nav className={open ? "nav open" : "nav"}>
          <button onClick={() => scrollTo("home")}>Home</button>
          <button onClick={() => scrollTo("shop")}>Shop</button>
          <button onClick={() => scrollTo("guide")}>Rudraksha Guide</button>
          <button onClick={() => scrollTo("recommendation")}>Recommendations</button>
          <button onClick={() => scrollTo("about")}>About</button>
          <button onClick={() => scrollTo("contact")}>Contact</button>
        </nav>

        <a
          className="header-cta"
          href="https://wa.me/14372671257?text=Hello%20Shiva%20Rudraksha%20Inc."
          target="_blank"
          rel="noreferrer"
        >
          <MessageCircle size={28} /> WhatsApp
        </a>

        <button className="menu-button" onClick={() => setOpen(!open)} aria-label="Toggle navigation">
          {open ? <X /> : <Menu />}
        </button>
      </div>
    </header>
  );
}
