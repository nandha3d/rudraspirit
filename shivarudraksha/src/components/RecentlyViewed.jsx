import { useState, useEffect } from "react";
import { Clock, ArrowRight, Trash2 } from "lucide-react";
import { getRecentProducts } from "../data/cartStore";
import { useCurrency } from "../context/CurrencyContext";

export default function RecentlyViewed({ currentProductId, allProducts = [], onOpenProduct }) {
  const { formatPrice } = useCurrency();
  const [recentItems, setRecentItems] = useState([]);

  useEffect(() => {
    const list = getRecentProducts().filter((p) => p.id !== currentProductId);
    setRecentItems(list);
  }, [currentProductId]);

  if (recentItems.length === 0) return null;

  const handleClear = () => {
    localStorage.removeItem("rudraspirit_recent_views");
    setRecentItems([]);
  };

  return (
    <section className="recently-viewed-section">
      <div className="container">
        <div className="recent-header-row">
          <div className="recent-header-title">
            <Clock size={18} className="recent-clock-icon" />
            <h3>Recently Viewed Beads</h3>
          </div>
          <button
            type="button"
            className="recent-clear-btn"
            onClick={handleClear}
            title="Clear browsing history"
          >
            <Trash2 size={14} />
            <span>Clear History</span>
          </button>
        </div>

        <div className="recent-scroll-track">
          {recentItems.map((item) => {
            // Find full product object from allProducts if available
            const fullProduct = allProducts.find((p) => p.id === item.id) || item;
            const price = Number(item.price || 0);

            return (
              <div
                key={item.id}
                className="recent-card-item"
                onClick={() => onOpenProduct(fullProduct)}
              >
                <div className="recent-thumb-wrap">
                  <img
                    src={item.image || "/images/brand/shiva-rudraksha-logo.png"}
                    alt={item.name}
                    loading="lazy"
                    decoding="async"
                    width="60"
                    height="60"
                    onError={(e) => {
                      e.currentTarget.src = "/images/brand/shiva-rudraksha-logo.png";
                    }}
                  />
                </div>
                <div className="recent-card-info">
                  <h4>{item.name}</h4>
                  <div className="recent-card-price">
                    {formatPrice(price, fullProduct)}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
