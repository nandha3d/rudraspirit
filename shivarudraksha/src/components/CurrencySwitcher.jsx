import React, { useState, useRef, useEffect } from "react";
import { useCurrency } from "../context/CurrencyContext";
import { ChevronDown, Globe } from "lucide-react";
import "./CurrencySwitcher.css";

export default function CurrencySwitcher() {
  const { currency, setCurrency, currencies, currencyConfig } = useCurrency();
  const [open, setOpen] = useState(false);
  const containerRef = useRef(null);

  // Close dropdown on outside click
  useEffect(() => {
    function handleClickOutside(event) {
      if (containerRef.current && !containerRef.current.contains(event.target)) {
        setOpen(false);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  return (
    <div className="currency-switcher-wrap" ref={containerRef}>
      <button
        type="button"
        className="currency-trigger-btn"
        onClick={() => setOpen((prev) => !prev)}
        aria-label="Select currency"
        aria-expanded={open}
      >
        <span className="currency-flag">{currencyConfig.flag}</span>
        <span className="currency-code">{currencyConfig.code}</span>
        <span className="currency-symbol">({currencyConfig.symbol})</span>
        <ChevronDown size={14} className={`currency-chevron ${open ? "rotate" : ""}`} />
      </button>

      {open && (
        <div className="currency-dropdown-menu">
          <div className="currency-dropdown-header">Select Currency</div>
          {Object.values(currencies).map((c) => {
            const isSelected = c.code === currency;
            return (
              <button
                key={c.code}
                type="button"
                className={`currency-option-item ${isSelected ? "selected" : ""}`}
                onClick={() => {
                  setCurrency(c.code);
                  setOpen(false);
                }}
              >
                <span className="option-flag">{c.flag}</span>
                <div className="option-info">
                  <strong className="option-code">{c.code}</strong>
                  <small className="option-country">{c.country}</small>
                </div>
                <span className="option-symbol">{c.symbol}</span>
                {isSelected && <span className="option-check">✓</span>}
              </button>
            );
          })}
        </div>
      )}
    </div>
  );
}
