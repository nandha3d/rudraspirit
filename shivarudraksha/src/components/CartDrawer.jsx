import { useState, useEffect } from "react";
import {
  X,
  Trash2,
  Plus,
  Minus,
  ShoppingBag,
  ShieldCheck,
  Sparkles,
  ArrowRight,
  ExternalLink,
} from "lucide-react";
import {
  getCart,
  updateQuantity,
  removeFromCart,
  getCartTotal,
  clearCart,
} from "../data/cartStore";
import { useCurrency } from "../context/CurrencyContext";

export default function CartDrawer({ isOpen, onClose, onNavigate }) {
  const { formatPrice, currency } = useCurrency();
  const [items, setItems] = useState([]);
  const [total, setTotal] = useState(0);

  const refreshCart = () => {
    setItems(getCart());
    setTotal(getCartTotal());
  };

  useEffect(() => {
    refreshCart();
    const handleCartUpdate = () => refreshCart();
    window.addEventListener("cart-updated", handleCartUpdate);
    return () => window.removeEventListener("cart-updated", handleCartUpdate);
  }, [isOpen]);

  useEffect(() => {
    if (!isOpen) return;
    const previousFocus = document.activeElement;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const drawer = document.querySelector(".cart-drawer");
    drawer?.querySelector("button")?.focus();
    const handleKey = (event) => {
      if (event.key === "Escape") { onClose(); return; }
      if (event.key !== "Tab") return;
      const controls = [...drawer.querySelectorAll('button:not([disabled]), a[href], input, select, textarea, [tabindex="0"]')];
      const first = controls[0];
      const last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
    };
    document.addEventListener("keydown", handleKey);
    return () => {
      document.body.style.overflow = previousOverflow;
      document.removeEventListener("keydown", handleKey);
      previousFocus?.focus();
    };
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  return (
    <div className="cart-drawer-overlay" onClick={onClose}>
      <aside
        className="cart-drawer"
        onClick={(e) => e.stopPropagation()}
        role="dialog" aria-modal="true" aria-label="Shopping Cart Drawer"
      >
        {/* Header */}
        <div className="cart-drawer-header">
          <div className="cart-header-title">
            <ShoppingBag size={22} className="cart-bag-icon" />
            <div>
              <h3>Sacred Cart</h3>
              <span className="cart-count-sub">
                {items.length} {items.length === 1 ? "bead" : "beads"} selected
              </span>
            </div>
          </div>
          <button
            type="button"
            className="cart-close-btn"
            onClick={onClose}
            aria-label="Close cart"
          >
            <X size={20} />
          </button>
        </div>

        {/* Free Energization Callout */}
        <div className="cart-perk-banner">
          <Sparkles size={16} className="cart-sparkle-icon" />
          <span>Complimentary Prana Pratishtha Energization Vidhi included</span>
        </div>

        {/* Items List */}
        <div className="cart-drawer-body">
          {items.length === 0 ? (
            <div className="cart-empty-state">
              <div className="cart-empty-icon">ॐ</div>
              <h4>Your Cart is Empty</h4>
              <p>Explore our consecrated 1 to 21 Mukhi Nepal Rudraksha collection.</p>
              <button
                type="button"
                className="cart-explore-btn"
                onClick={() => {
                  onClose();
                  onNavigate("#home");
                }}
              >
                Browse Sacred Collection
              </button>
            </div>
          ) : (
            <div className="cart-items-list">
              {items.map((item) => (
                <div key={item.id} className="cart-item-card">
                  <div className="cart-item-thumb">
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

                  <div className="cart-item-info">
                    <div className="cart-item-title-row">
                      <h4>{item.name}</h4>
                      <button
                        type="button"
                        className="cart-item-remove"
                        onClick={() => removeFromCart(item.id)}
                        title="Remove bead" aria-label={`Remove ${item.name} from cart`}
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>

                    <div className="cart-item-specs">
                      <span className="cart-spec-pill">{item.sizeName}</span>
                      <span className="cart-spec-pill cert">{item.certName}</span>
                    </div>

                    <div className="cart-item-price-row">
                      <div className="cart-item-price">
                        {formatPrice(item.price)}
                      </div>

                      <div className="cart-qty-control">
                        <button
                          type="button"
                          aria-label={`Decrease quantity of ${item.name}`} onClick={() => updateQuantity(item.id, item.quantity - 1)}
                          disabled={item.quantity <= 1}
                        >
                          <Minus size={14} />
                        </button>
                        <span>{item.quantity}</span>
                        <button
                          type="button"
                          aria-label={`Increase quantity of ${item.name}`}
                          onClick={() => updateQuantity(item.id, item.quantity + 1)}
                          disabled={item.quantity >= 99}
                        >
                          <Plus size={14} />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Footer Summary & Checkout */}
        {items.length > 0 && (
          <div className="cart-drawer-footer">
            <div className="cart-summary-line">
              <span>Subtotal</span>
              <strong className="cart-subtotal-val">
                {formatPrice(total)}
              </strong>
            </div>

            <div className="cart-summary-line minor">
              <span>Vedic Energization Vidhi</span>
              <span className="cart-free-tag">
                {currency === "CAD" ? "FREE ($40 value)" : "FREE (₹2,500 value)"}
              </span>
            </div>

            <div className="cart-summary-line minor">
              <span>Worldwide Insured Shipping</span>
              <span className="cart-free-tag">Complimentary</span>
            </div>

            <div className="cart-divider" />

            <div className="cart-summary-line total">
              <span>Total Estimated</span>
              <strong className="cart-grandtotal-val">
                {formatPrice(total)}
              </strong>
            </div>

            <div className="cart-actions-column">
              <a href="/checkout" className="cart-checkout-btn">
                <span>Proceed to Secure Checkout</span>
                <ArrowRight size={18} />
              </a>

              <div className="cart-secondary-actions">
                <a href="/cart" className="cart-view-link">
                  <span>View Full Cart Page</span>
                  <ExternalLink size={13} />
                </a>
                <button
                  type="button"
                  className="cart-clear-link"
                  onClick={() => clearCart()}
                >
                  Clear All
                </button>
              </div>
            </div>

            <div className="cart-trust-footer">
              <ShieldCheck size={14} className="cart-trust-shield" />
              <span>Government Certified • Prana Pratishtha Consecrated</span>
            </div>
          </div>
        )}
      </aside>
    </div>
  );
}
