import { useState, useEffect, useRef, useMemo } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import "./HeroCarousel.css";

const slides = [
  {
    id: 1,
    image: "images/hero/slide-1.avif",
    kicker: "DIRECTLY SOURCED FROM THE HIMALAYAS",
    title: "Authentic Certified Rudraksha from",
    titleHighlight: "Nepal.",
    description:
      "Handpicked rare collector beads, Siddha Malas and energized combinations—supplied worldwide with complete government lab authentication and X-ray verification.",
    primaryCta: {
      text: "Explore Collection",
      link: "#shop",
      icon: "arrow",
    },
    secondaryCta: {
      text: "Rudraksha Guide",
      link: "#guide",
    },
    badges: [
      "100% Authentic Nepal Origin",
      "Lab Certificate & X-Ray Included",
      "Canada & USA Fast Delivery",
      "Personal WhatsApp Consultation",
    ],
  },
  {
    id: 2,
    image: "images/hero/siddha-indra-mala-banner-v2.avif",
    kicker: "SACRED 1 TO 21 MUKHI ENERGIZED BEADS",
    title: "Master the Energies of the",
    titleHighlight: "Cosmos.",
    description:
      "From the revered 1 Mukhi symbol of Supreme Consciousness to the rare 21 Mukhi of Lord Kuber, explore the sacred attributes, ruling planets, and traditional blessings.",
    primaryCta: {
      text: "1–21 Mukhi Guide",
      link: "#guide",
      icon: "arrow",
    },
    secondaryCta: {
      text: "Consult via WhatsApp",
      link: "https://wa.me/14372671257",
      external: true,
    },
    badges: [
      "Super Collector & Collector Sizes",
      "Sourced from Sacred Himalayan Groves",
      "Panchamrit Energization Vidhi Included",
      "Lifetime Authenticity Assurance",
    ],
  },
  {
    id: 3,
    image: "images/hero/birth-chart-banner-v2.avif",
    kicker: "PERSONALIZED VEDIC ASTROLOGY",
    title: "Find Your Divine Bead by",
    titleHighlight: "Birth Chart.",
    description:
      "Every soul has a unique cosmic signature. Submit your birth date, time, and birthplace to receive a personalized Rudraksha recommendation tailored to your planetary horoscope.",
    primaryCta: {
      text: "Get Recommendation",
      link: "#recommendations",
      icon: "arrow",
    },
    secondaryCta: {
      text: "How Energizing Works",
      link: "#knowledge",
    },
    badges: [
      "Vedic Kundali Analysis",
      "Chakra & Planetary Alignment",
      "Completely Free Recommendation",
      "Direct WhatsApp Delivery",
    ],
  },
];

export default function HeroCarousel({ compact = false, product = null }) {
  const [current, setCurrent] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const autoPlayRef = useRef(null);
  const baseUrl = import.meta.env.BASE_URL;

  const activeSlides = useMemo(() => {
    if (!product) return slides;

    return [
      {
        id: 1,
        image: "images/hero/slide-1.avif",
        kicker: `${(product.origin || "Himalayan").toUpperCase()} SACRED CONSECRATION`,
        title: product.name,
        titleHighlight: "Authentic & Energized.",
        description:
          product.description ||
          "Directly sourced from sacred Himalayan groves. Handpicked for natural Mukhi purity, verified internal seed cavities, and consecrated through authentic Vedic Prana Pratishtha.",
        primaryCta: {
          text: "Configure & Order",
          link: "#select-options",
          icon: "arrow",
        },
        secondaryCta: {
          text: "WhatsApp Consultation",
          link: "https://wa.me/14372671257",
          external: true,
        },
        badges: [
          "100% Authentic Nepal Origin",
          "Govt. Lab Certificate Included",
          "Prana Pratishtha Energized",
          "Fast Worldwide Delivery",
        ],
      },
      {
        id: 2,
        image: "images/hero/siddha-indra-mala-banner-v2.avif",
        kicker: "SACRED 1 TO 21 MUKHI ENERGIZED BEADS",
        title: "Master the Energies of the",
        titleHighlight: "Cosmos.",
        description:
          "From the revered 1 Mukhi to the rare 21 Mukhi, explore the sacred attributes, ruling deities, and traditional blessings documented in ancient Puranic texts.",
        primaryCta: {
          text: "Read Spiritual Guide",
          link: "#spiritual-guide-tab",
          icon: "arrow",
        },
        secondaryCta: {
          text: "Inquire with Owner",
          link: "https://wa.me/14372671257",
          external: true,
        },
        badges: [
          "Collector & Regular Sizes",
          "X-Ray Proven Seed Cavities",
          "Panchamrit Vidhi Energized",
          "Astrological Suitability",
        ],
      },
      {
        id: 3,
        image: "images/hero/birth-chart-banner-v2.avif",
        kicker: "SHIVA RUDRAKSHA INC. • TORONTO, CANADA",
        title: "Direct Devotee Consultation &",
        titleHighlight: "Support.",
        description:
          "Connect directly with Shiva Rudraksha Inc. in Toronto for personalized bead selection, Kundali planetary matching, and customized Siddha Malas.",
        primaryCta: {
          text: "Connect via WhatsApp",
          link: "https://wa.me/14372671257",
          external: true,
          icon: "arrow",
        },
        secondaryCta: {
          text: "Explore All Mukhis",
          link: "#guide",
        },
        badges: [
          "Direct Telephone & WhatsApp",
          "Free Kundali Recommendation",
          "Insured Express Global Dispatch",
          "Lifetime Authenticity Assurance",
        ],
      },
    ];
  }, [product]);

  const nextSlide = () => {
    setCurrent((prev) => (prev + 1) % activeSlides.length);
  };

  const prevSlide = () => {
    setCurrent((prev) => (prev - 1 + activeSlides.length) % activeSlides.length);
  };

  const goToSlide = (index) => {
    setCurrent(index);
  };


  useEffect(() => {
    if (isPaused) return;
    autoPlayRef.current = setInterval(nextSlide, 7000);
    return () => clearInterval(autoPlayRef.current);
  }, [current, isPaused, activeSlides.length]);

  return (
    <section
      className={`hero-carousel-wrap ${compact ? "hero-compact" : ""}`}
      aria-label="Featured Hero Carousel"
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
    >
      {/* Background Slides */}
      <div className="hero-carousel-bg-container">
        {activeSlides.map((s, idx) => {
          const isActive = idx === current;
          return (
            <div
              key={s.id}
              className={`hero-carousel-slide ${isActive ? "active" : ""}`}
              style={{
                backgroundImage: `url(${baseUrl}${s.image})`,
              }}
              role="img"
              aria-label={s.id === 2 ? "Siddha Mala & Indra Mala — Certificate & X-Ray Verification" : s.id === 3 ? "Birth Chart Recommendation — Rudraksha Guidance Based on Vedic Astrology" : s.title} />
          );
        })}
      </div>

      {/* Carousel Navigation Arrows */}
      <button
        type="button"
        className="hero-nav-arrow hero-nav-prev"
        onClick={prevSlide}
        aria-label="Previous Slide"
      >
        <ChevronLeft size={28} />
      </button>

      <button
        type="button"
        className="hero-nav-arrow hero-nav-next"
        onClick={nextSlide}
        aria-label="Next Slide"
      >
        <ChevronRight size={28} />
      </button>

      {/* Bottom Indicator Dots & Progress Bars */}
      <div className="hero-carousel-pagination">
        {activeSlides.map((s, idx) => (
          <button
            key={s.id}
            type="button"
            className={`hero-dot-btn ${idx === current ? "active" : ""}`}
            onClick={() => goToSlide(idx)}
            aria-label={`Go to slide ${idx + 1}`}
          >
            <span className="hero-dot-pill">
              <span className="hero-dot-number">{`0${idx + 1}`}</span>
              <span className="hero-dot-label">
                {product
                  ? idx === 0
                    ? "Authentic Bead"
                    : idx === 1
                    ? "Vedic Energy"
                    : "Consultation"
                  : idx === 0
                  ? "Divine Heritage"
                  : idx === 1
                  ? "1–21 Mukhi"
                  : "Birth Chart"}
              </span>
            </span>
          </button>
        ))}
      </div>
    </section>
  );
}
