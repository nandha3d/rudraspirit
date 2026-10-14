import { useEffect, useMemo, useRef, useState } from "react";
import { ChevronLeft, ChevronRight, Expand, X } from "lucide-react";
import { IMAGE_SLOTS } from "../data/catalogue";

export default function Gallery({ product, compact = false, resetKey = "" }) {
  const images = useMemo(
    () =>
      IMAGE_SLOTS.map((slot) => ({
        ...slot,
        src: product.images?.[slot.key] || "",
        fallbackSrc: product.fallbackImages?.[slot.key] || "",
      })).filter((item) => item.src || item.fallbackSrc),
    [product]
  );

  const [index, setIndex] = useState(0);
  const [fullscreen, setFullscreen] = useState(false);
  const startX = useRef(null);
  useEffect(() => {
    setIndex(0);
    setFullscreen(false);
  }, [resetKey]);

  const active = images[index] || images[0];

  const useFallback = (event, fallbackSrc) => {
    if (!fallbackSrc || event.currentTarget.dataset.fallbackApplied === "true") return;
    event.currentTarget.dataset.fallbackApplied = "true";
    event.currentTarget.src = fallbackSrc;
  };

  if (!active) return <div className="no-image">No image available</div>;

  const previous = () =>
    setIndex((current) => (current === 0 ? images.length - 1 : current - 1));
  const next = () =>
    setIndex((current) => (current === images.length - 1 ? 0 : current + 1));

  const touchStart = (event) => {
    startX.current = event.changedTouches[0].clientX;
  };
  const touchEnd = (event) => {
    if (startX.current == null) return;
    const delta = event.changedTouches[0].clientX - startX.current;
    if (Math.abs(delta) > 45) delta > 0 ? previous() : next();
    startX.current = null;
  };

  if (compact) {
    return (
      <div className="card-gallery" onTouchStart={touchStart} onTouchEnd={touchEnd}>
        <img src={active.src || active.fallbackSrc} onError={(event) => useFallback(event, active.fallbackSrc)} alt={`${product.name} ${active.label}`} />
        {images.length > 1 && (
          <>
            <button
              className="gallery-button left"
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                previous();
              }}
            >
              <ChevronLeft />
            </button>
            <button
              className="gallery-button right"
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                next();
              }}
            >
              <ChevronRight />
            </button>
          </>
        )}
        <span className="image-type">{active.label}</span>
        <span className="image-count">{index + 1}/{images.length}</span>
      </div>
    );
  }

  return (
    <>
      <div className="detail-gallery">
        <div
          className="detail-main-image"
          onTouchStart={touchStart}
          onTouchEnd={touchEnd}
        >
          <img src={active.src || active.fallbackSrc} onError={(event) => useFallback(event, active.fallbackSrc)} alt={`${product.name} ${active.label}`} />
          <button className="gallery-button left" onClick={previous}>
            <ChevronLeft />
          </button>
          <button className="gallery-button right" onClick={next}>
            <ChevronRight />
          </button>
          <button className="expand-button" onClick={() => setFullscreen(true)}>
            <Expand />
          </button>
          <span className="image-type">{active.label}</span>
          <span className="image-count">{index + 1}/{images.length}</span>
        </div>

        <div className="thumbnails">
          {images.map((item, i) => (
            <button
              key={item.key}
              className={i === index ? "active" : ""}
              onClick={() => setIndex(i)}
            >
              <img src={item.src || item.fallbackSrc} onError={(event) => useFallback(event, item.fallbackSrc)} alt={item.label} />
              <span>{item.label}</span>
            </button>
          ))}
        </div>
      </div>

      {fullscreen && (
        <div className="fullscreen">
          <button className="close-fullscreen" onClick={() => setFullscreen(false)}>
            <X />
          </button>
          <button className="full-arrow left" onClick={previous}>
            <ChevronLeft />
          </button>
          <figure>
            <img src={active.src || active.fallbackSrc} onError={(event) => useFallback(event, active.fallbackSrc)} alt={`${product.name} ${active.label}`} />
            <figcaption>
              {product.name} — {active.label} ({index + 1}/{images.length})
            </figcaption>
          </figure>
          <button className="full-arrow right" onClick={next}>
            <ChevronRight />
          </button>
        </div>
      )}
    </>
  );
}
