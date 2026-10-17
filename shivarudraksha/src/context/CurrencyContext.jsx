import React, { createContext, useContext, useState, useEffect } from "react";

export const CURRENCIES = {
  CAD: {
    code: "CAD",
    symbol: "CA$",
    sign: "$",
    label: "CAD ($)",
    flag: "🇨🇦",
    country: "Canada",
    locale: "en-CA",
    rateFromCad: 1.0,
    tenant: "com",
  },
  INR: {
    code: "INR",
    symbol: "₹",
    sign: "₹",
    label: "INR (₹)",
    flag: "🇮🇳",
    country: "India",
    locale: "en-IN",
    rateFromCad: 62.0, // 1 CAD ≈ 62 INR standard market exchange
    tenant: "in",
  },
};

/**
 * Detects domain tenant and default currency:
 * .in -> INR / tenant "in" (e.g. shivarudraksha.in)
 * .com -> CAD / tenant "com" (e.g. shivarudrakshainc.com)
 */
export function detectDefaultTenant() {
  if (typeof window === "undefined") return { currency: "CAD", tenant: "com" };

  const host = window.location.hostname.toLowerCase();
  if (host.endsWith(".in")) {
    return { currency: "INR", tenant: "in" };
  }
  return { currency: "CAD", tenant: "com" };
}

export function detectDefaultCurrency() {
  if (typeof window === "undefined") return "CAD";

  // 1. Check if user previously selected a preferred currency
  try {
    const saved = localStorage.getItem("shiva_currency");
    if (saved && CURRENCIES[saved]) {
      return saved;
    }
  } catch (e) {
    // ignore localStorage errors
  }

  // 2. Fallback to domain detection
  return detectDefaultTenant().currency;
}

const CurrencyContext = createContext(null);

export function CurrencyProvider({ children }) {
  const [currency, setCurrencyState] = useState(detectDefaultCurrency);
  const defaultTenant = detectDefaultTenant().tenant;

  // Sync to localStorage when user manually changes currency
  const setCurrency = (code) => {
    if (!CURRENCIES[code]) return;
    setCurrencyState(code);
    try {
      localStorage.setItem("shiva_currency", code);
    } catch (e) {}
  };

  const currentConfig = CURRENCIES[currency] || CURRENCIES.CAD;
  // Current active market tenant ("com" or "in")
  const activeTenant = currentConfig.tenant || defaultTenant;

  /**
   * Checks if a product or variant is available in the current tenant/store
   * @param {object} item - Product or variant object
   * @returns {boolean}
   */
  const isAvailableForTenant = (item) => {
    if (!item) return false;
    const tenants = item.tenants || item.available_in || item.markets;
    if (!tenants || !Array.isArray(tenants) || tenants.length === 0) {
      return true; // Available in all tenants by default
    }
    const cleanTenants = tenants.map((t) => String(t).toLowerCase());
    return cleanTenants.includes(activeTenant) || cleanTenants.includes("all");
  };

  /**
   * Returns specific stock status text for the current market/tenant
   */
  const getTenantStockStatus = (item) => {
    if (!item) return "Available";
    if (activeTenant === "com") {
      return item.stockStatus_com || item.stockStatus || "Available";
    }
    return item.stockStatus_in || item.stockStatus || "Available";
  };

  /**
   * Returns numeric price for product/variant in active currency
   */
  const getNumericPrice = (amount, productOverride = null) => {
    const target = productOverride || {};

    if (currency === "INR") {
      // Check explicit INR price override on variant or product
      if (Number(target.price_inr) > 0) {
        return Number(target.price_inr);
      }
      const baseNum = Number(amount || target.price || 0);
      return Math.round(baseNum * currentConfig.rateFromCad);
    }

    // CAD
    if (Number(target.price_cad) > 0) {
      return Number(target.price_cad);
    }
    return Number(amount || target.price || 0);
  };

  /**
   * Formats a product price according to active currency & tenant overrides
   * @param {number} amount - Base price (stored in CAD)
   * @param {object} [productOverride] - Optional product with explicit price_inr / price_cad
   */
  const formatPrice = (amount, productOverride = null) => {
    const num = getNumericPrice(amount, productOverride);
    if (!num || num <= 0) {
      return "Price on Request";
    }

    if (currency === "INR") {
      return `₹${num.toLocaleString("en-IN")}`;
    }

    // CAD
    return `CA$ ${num.toLocaleString("en-CA")}`;
  };

  return (
    <CurrencyContext.Provider
      value={{
        currency,
        setCurrency,
        currencyConfig: currentConfig,
        tenant: activeTenant,
        isAvailableForTenant,
        getTenantStockStatus,
        formatPrice,
        getNumericPrice,
        currencies: CURRENCIES,
      }}
    >
      {children}
    </CurrencyContext.Provider>
  );
}

export function useCurrency() {
  const ctx = useContext(CurrencyContext);
  if (!ctx) {
    throw new Error("useCurrency must be used within a CurrencyProvider");
  }
  return ctx;
}
