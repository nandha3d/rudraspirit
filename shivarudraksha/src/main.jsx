import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";
import { CurrencyProvider } from "./context/CurrencyContext";
import "./styles.css";
import "./productEnhancements.css";
import "./responsive.css";

ReactDOM.createRoot(document.getElementById("root")).render(
  <React.StrictMode>
    <CurrencyProvider>
      <App />
    </CurrencyProvider>
  </React.StrictMode>
);

