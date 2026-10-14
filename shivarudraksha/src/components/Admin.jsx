import { useMemo, useState } from "react";
import {
  ArrowLeft,
  CheckCircle2,
  Edit3,
  Github,
  ImagePlus,
  LoaderCircle,
  LogOut,
  RefreshCw,
  Save,
  Trash2,
} from "lucide-react";
import Logo from "./Logo";
import { IMAGE_SLOTS, assetUrl, emptyImages } from "../data/catalogue";
import {
  clearSettings,
  getSettings,
  loadRepositoryProducts,
  removeImage,
  saveRepositoryProducts,
  saveSettings,
  uploadSlotImage,
  verifyRepository,
} from "../data/github";

const VARIANT_NAMES = ["Small", "Medium", "Large", "Collector", "Super Collector"];
const emptyVariant = (name) => ({
  name,
  price: "",
  images: emptyImages(),
});

const newProduct = () => ({
  id: "",
  mukhi: "",
  name: "",
  category: "Nepal Rudraksha",
  origin: "Nepal",
  variants: VARIANT_NAMES.map(emptyVariant),
  description: "",
  certificateAvailable: true,
  stockStatus: "Available",
  badge: "Certified",
});

const slugify = (name) =>
  name.toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

const normalizeVariants = (product = {}) => {
  const existing = Array.isArray(product.variants) ? product.variants : [];
  return VARIANT_NAMES.map((name) => {
    const found = existing.find((item) => item.name === name) || {};
    return {
      name,
      price: found.price ?? product.price ?? "",
      // Do not copy legacy product images into every variant. They are shown
      // as a preview fallback until this size gets its own uploaded image.
      images: {
        ...emptyImages(),
        ...(found.images || {}),
      },
    };
  });
};

export default function Admin({ onBack }) {
  const [settings, setSettingsState] = useState({
    owner: "shivarudrakshain-hub",
    repo: "shiva-rudraksha-catalogue",
    branch: "main",
    token: "",
    ...getSettings(),
  });
  const [connected, setConnected] = useState(false);
  const [products, setProducts] = useState([]);
  const [form, setForm] = useState(newProduct());
  const [files, setFiles] = useState({});
  const [previews, setPreviews] = useState({});
  const [status, setStatus] = useState("");
  const [busy, setBusy] = useState(false);
  const [search, setSearch] = useState("");
  const [activeVariant, setActiveVariant] = useState(0);

  const visible = useMemo(
    () => products.filter((product) =>
      `${product.name} ${product.origin}`.toLowerCase().includes(search.toLowerCase())
    ),
    [products, search]
  );

  const fileKey = (variantIndex, slot) => `${variantIndex}:${slot}`;

  const updateSetting = (event) =>
    setSettingsState((current) => ({ ...current, [event.target.name]: event.target.value }));

  const updateField = (event) =>
    setForm((current) => ({ ...current, [event.target.name]: event.target.value }));

  const updateVariant = (index, field, value) =>
    setForm((current) => ({
      ...current,
      variants: current.variants.map((variant, variantIndex) =>
        variantIndex === index ? { ...variant, [field]: value } : variant
      ),
    }));

  async function connect(event) {
    event.preventDefault();
    setBusy(true);
    setStatus("Connecting to GitHub…");
    try {
      await verifyRepository(settings);
      const loaded = (await loadRepositoryProducts(settings)).map((product) => ({
        ...product,
        variants: normalizeVariants(product),
      }));
      setProducts(loaded);
      saveSettings(settings);
      setConnected(true);
      setStatus(`Connected. ${loaded.length} products loaded.`);
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  async function refresh() {
    setBusy(true);
    try {
      const loaded = (await loadRepositoryProducts(settings)).map((product) => ({
        ...product,
        variants: normalizeVariants(product),
      }));
      setProducts(loaded);
      setStatus("Catalogue refreshed.");
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  function selectImage(variantIndex, slot, event) {
    const file = event.target.files?.[0];
    if (!file) return;
    if (!file.type.startsWith("image/")) return setStatus("Please select an image file.");
    if (file.size > 8 * 1024 * 1024) return setStatus("Each image must be smaller than 8 MB.");
    const key = fileKey(variantIndex, slot);
    setFiles((current) => ({ ...current, [key]: file }));
    setPreviews((current) => ({ ...current, [key]: URL.createObjectURL(file) }));
  }

  function resetForm() {
    Object.values(previews).forEach((url) => url?.startsWith("blob:") && URL.revokeObjectURL(url));
    setForm(newProduct());
    setFiles({});
    setPreviews({});
    setActiveVariant(0);
  }

  function editProduct(product) {
    resetForm();
    const variants = normalizeVariants(product);
    setForm({ ...product, variants });
    const nextPreviews = {};
    variants.forEach((variant, variantIndex) => {
      Object.entries(variant.images || {}).forEach(([slot, value]) => {
        if (value) nextPreviews[fileKey(variantIndex, slot)] = assetUrl(value);
      });
    });
    setPreviews(nextPreviews);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  async function saveProduct(event) {
    event.preventDefault();
    if (!form.name.trim()) return setStatus("Product name is required.");

    const hasAnyFront = Boolean(form.images?.front) || form.variants.some((variant, index) =>
      variant.images?.front || files[fileKey(index, "front")]
    );
    if (!hasAnyFront) return setStatus("Add at least one Front image.");

    setBusy(true);
    try {
      const variants = [];
      for (let variantIndex = 0; variantIndex < form.variants.length; variantIndex += 1) {
        const variant = form.variants[variantIndex];
        const images = { ...emptyImages(), ...(variant.images || {}) };

        for (const slot of IMAGE_SLOTS) {
          const key = fileKey(variantIndex, slot.key);
          if (!files[key]) continue;
          setStatus(`Uploading ${variant.name} ${slot.label} image…`);
          images[slot.key] = await uploadSlotImage(
            settings,
            files[key],
            form.name,
            slot.key,
            variant.name
          );
        }

        variants.push({
          name: variant.name,
          price: Number(variant.price || 0),
          images,
        });
      }

      const saved = {
        ...form,
        id: form.id || slugify(form.name),
        mukhi: Number(form.mukhi) || null,
        name: form.name.trim(),
        certificateAvailable: true,
        variants,
      };
      // Preserve the original product-level six images as the fallback for
      // every size that does not yet have its own uploaded photos.
      saved.images = { ...emptyImages(), ...(form.images || {}) };
      delete saved.price;
      delete saved.size;
      delete saved.weight;

      const updated = form.id
        ? products.map((product) => (product.id === form.id ? saved : product))
        : [saved, ...products];

      await saveRepositoryProducts(
        settings,
        updated,
        form.id ? `Update ${saved.name}` : `Add ${saved.name}`
      );

      setProducts(updated);
      resetForm();
      setStatus("Saved to GitHub. Run the deployment workflow when ready.");
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  async function deleteProduct(product) {
    if (!confirm(`Delete ${product.name}?`)) return;
    setBusy(true);
    try {
      const updated = products.filter((item) => item.id !== product.id);
      await saveRepositoryProducts(settings, updated, `Delete ${product.name}`);
      const paths = normalizeVariants(product).flatMap((variant) => Object.values(variant.images || {}));
      for (const path of [...new Set(paths.filter(Boolean))]) {
        try { await removeImage(settings, path); } catch {}
      }
      setProducts(updated);
      setStatus("Product deleted.");
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  if (!connected) {
    return (
      <div className="admin-page">
        <header className="admin-topbar"><div className="container admin-topbar-inner"><Logo /><button onClick={onBack}><ArrowLeft /> Catalogue</button></div></header>
        <main className="admin-login">
          <section>
            <Github className="github-icon" />
            <span>REPOSITORY CONNECTION</span>
            <h1>Connect the product manager to GitHub</h1>
            <p>Use a fine-grained token with Contents: Read and write access.</p>
            {status && <div className="status-message">{status}</div>}
            <form onSubmit={connect}>
              <label>GitHub owner<input name="owner" value={settings.owner} onChange={updateSetting} required /></label>
              <label>Repository<input name="repo" value={settings.repo} onChange={updateSetting} required /></label>
              <label>Branch<input name="branch" value={settings.branch} onChange={updateSetting} required /></label>
              <label>Fine-grained token<input type="password" name="token" value={settings.token} onChange={updateSetting} required /></label>
              <button disabled={busy}>{busy ? <LoaderCircle className="spin" /> : <Github />} Connect</button>
            </form>
            <aside><strong>Token safety</strong><p>The token is kept only in this browser session.</p></aside>
          </section>
        </main>
      </div>
    );
  }

  const selected = form.variants[activeVariant];

  return (
    <div className="admin-page">
      <header className="admin-topbar">
        <div className="container admin-topbar-inner">
          <Logo />
          <div>
            <button onClick={refresh}><RefreshCw /> Refresh</button>
            <button onClick={() => { clearSettings(); setConnected(false); }}><LogOut /> Disconnect</button>
            <button onClick={onBack}><ArrowLeft /> Catalogue</button>
          </div>
        </div>
      </header>

      <main className="container admin-content">
        <div className="admin-heading">
          <div><span>GITHUB PRODUCT MANAGER</span><h1>Manage each size with six unique images</h1></div>
          <div className="connected"><CheckCircle2 /> Connected</div>
        </div>

        {status && <div className="status-message">{status}</div>}

        <div className="admin-grid">
          <form className="product-form" onSubmit={saveProduct}>
            <h2>{form.id ? "Edit product" : "Add product"}</h2>
            <div className="field-grid">
              <label className="wide">Product name<input name="name" value={form.name} onChange={updateField} required /></label>
              <label>Mukhi number<input type="number" name="mukhi" value={form.mukhi} onChange={updateField} /></label>
              <label>Origin<input name="origin" value={form.origin} onChange={updateField} /></label>
              <label>Stock status<select name="stockStatus" value={form.stockStatus} onChange={updateField}><option>Available</option><option>Out of stock</option><option>Reserved</option></select></label>
              <label className="wide">Description<textarea rows="4" name="description" value={form.description} onChange={updateField} /></label>
            </div>

            <h3>Size variants</h3>
            <div className="variant-tabs">
              {form.variants.map((variant, index) => (
                <button
                  type="button"
                  key={variant.name}
                  className={index === activeVariant ? "active" : ""}
                  onClick={() => setActiveVariant(index)}
                >
                  {variant.name}
                </button>
              ))}
            </div>

            <section className="variant-editor-card">
              <h3>{selected.name}</h3>
              <div className="field-grid">
                <label>
                  Price (CAD)
                  <input type="number" min="0" step="0.01" value={selected.price} onChange={(e) => updateVariant(activeVariant, "price", e.target.value)} required />
                </label>
              </div>

              <h3>{selected.name} images</h3>
              <p className="variant-image-note">
                Existing product photos are shown until you upload separate {selected.name} photos.
              </p>
              <div className="upload-grid">
                {IMAGE_SLOTS.map((slot) => {
                  const key = fileKey(activeVariant, slot.key);
                  const ownImage = selected.images?.[slot.key] || "";
                  const fallbackImage = form.images?.[slot.key] || "";
                  const preview = previews[key] || assetUrl(ownImage || fallbackImage);
                  return (
                    <div className="upload-card" key={slot.key}>
                      <div><strong>{slot.label}</strong>{slot.key === "front" && <span>Recommended</span>}</div>
                      <label>
                        {preview ? (
                          <img
                            src={preview}
                            alt={`${selected.name} ${slot.label}`}
                            onError={(event) => {
                              const fallback = assetUrl(fallbackImage);
                              if (fallback && event.currentTarget.dataset.fallbackApplied !== "true") {
                                event.currentTarget.dataset.fallbackApplied = "true";
                                event.currentTarget.src = fallback;
                              }
                            }}
                          />
                        ) : <p><ImagePlus /> Upload {slot.label}</p>}
                        <input type="file" accept="image/*" onChange={(e) => selectImage(activeVariant, slot.key, e)} />
                      </label>
                    </div>
                  );
                })}
              </div>
            </section>

            <button className="save-button" disabled={busy}>
              {busy ? <LoaderCircle className="spin" /> : <Save />}
              {form.id ? "Save product changes" : "Add product"}
            </button>
          </form>

          <section className="product-admin-list">
            <div className="admin-list-title">
              <div><span>CATALOGUE</span><h2>{products.length} products</h2></div>
              <input placeholder="Search" value={search} onChange={(e) => setSearch(e.target.value)} />
            </div>
            {visible.map((product) => {
              const variants = normalizeVariants(product);
              const cover = variants.find((variant) => variant.images?.front)?.images?.front || product.images?.front || "";
              const imageCount = variants.reduce((total, variant) => total + Object.values(variant.images || {}).filter(Boolean).length, 0);
              const availablePrices = (product.variants || []).map((variant) => Number(variant.price || 0)) .filter((price) => price > 0);
              const lowestPrice = availablePrices.length > 0 ? Math.min(...availablePrices) : 0;

              return (
                <article key={product.id}>
                  <img src={assetUrl(cover)} alt={product.name} />
                  <div>
                    <strong>{product.name}</strong>
                    <span>{product.origin} • {variants.length} size variants</span>
                     <b>{lowestPrice > 0 ? `From CAD $${lowestPrice.toFixed(2)}` : "Out of Stock"} </b>
                    <em>{imageCount}/30 size images</em>
                  </div>
                  <div>
                    <button onClick={() => editProduct(product)}><Edit3 /></button>
                    <button onClick={() => deleteProduct(product)}><Trash2 /></button>
                  </div>
                </article>
              );
            })}
          </section>
        </div>

        <div className="manual-publish">
          <strong>Manual publishing enabled</strong>
          <p>Product changes are committed to GitHub but are not published until you run the GitHub Pages workflow manually.</p>
        </div>
      </main>
    </div>
  );
}
