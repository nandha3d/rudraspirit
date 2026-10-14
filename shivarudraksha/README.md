# Shiva Rudraksha Public Storefront (React / Vite)

This is the standalone React/Vite frontend for **Shiva Rudraksha Inc.** ([https://shivarudraksha.in](https://shivarudraksha.in)).

A comprehensive developer guide is also available in [`../REACT_DEVELOPER_GUIDE.md`](../REACT_DEVELOPER_GUIDE.md).

---

## 📁 Repository & Folder Structure

* **React Source Code Folder:** `shivarudraksha/` (this directory)
* **Git Remote:** `https://github.com/shivarudrakshain-hub/shiva-rudraksha-catalogue.git` (`main` branch)
* **Production Build Output:** `shivarudraksha/dist/` (copied to Laravel `public/index.html` and `public/assets/` on the live server)

```
shivarudraksha/
├── public/                       # Static files copied to build output
│   ├── data/
│   │   ├── products.json         # 📦 All products, bead variants, and photo paths
│   │   └── rudraksha-guide.json  # 📜 Spiritual guide data for 1–21 Mukhi
│   └── images/
│       ├── brand/                # 🕉️ Brand logos
│       └── products/             # 📸 6-sided photo angles & lab certificates
├── src/
│   ├── App.jsx                   # Main single-page app & hash navigation
│   ├── styles.css                # Global CSS styling & design system
│   ├── components/               # React components (ProductCard, ProductDetails, etc.)
│   └── data/
│       └── catalogue.js          # Loads and normalizes products.json
├── package.json                  # React 18, Vite 6, Lucide React
└── vite.config.js                # Build configuration
```

---

## ⚡ Local Development

```bash
cd shivarudraksha
npm install
npm run dev
```

* Local URL: `http://localhost:5173/`

---

## 🔌 How Products Are Connected to the Backend Engine

**YES — The React storefront connects directly to the Laravel backend engine!**

* [`src/data/catalogue.js`](src/data/catalogue.js) fetches live products from `/api/v3/products?per_page=100`.
* Whenever an admin edits products, descriptions, photos, or stock in the Laravel Admin Dashboard (`/admin/products/all`), the changes immediately stream to the React frontend.
* It merges automatically with `public/data/products.json` as a resilient fallback so no catalogue bead is ever missing during offline local development.

---

## 🚀 Building & Deploying to Live

1. Build production bundle:
   ```bash
   npm run build
   ```
2. Copy built files into the web root:
   ```powershell
   Copy-Item dist\index.html ..\public\index.html -Force
   Copy-Item dist\assets\* ..\public\assets\ -Force
   ```
3. Deploy to live server (from the root project directory):
   ```powershell
   cd ..
   .\deploy.ps1 -Direct "Update React storefront"
   ```

