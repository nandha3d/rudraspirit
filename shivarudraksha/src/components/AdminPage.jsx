import { useEffect, useMemo, useState } from "react";
import {
  ArrowLeft,
  CheckCircle2,
  Edit3,
  Github,
  ImagePlus,
  LoaderCircle,
  LogOut,
  PackagePlus,
  RefreshCw,
  Save,
  Trash2,
  UploadCloud,
  X,
} from "lucide-react";
import {
  clearSettings,
  getSettings,
  loadProductsFromGitHub,
  publicAssetUrl,
  removeProductImage,
  safeFilename,
  saveProductsToGitHub,
  saveSettings,
  uploadProductImage,
  verifyRepository,
} from "../data/githubStore";

const blankProduct = {
  id: "",
  name: "",
  category: "Nepal Rudraksha",
  origin: "Nepal",
  price: "",
  badge: "New",
  description: "",
  image: "",
};

function createSlug(name) {
  return (
    name
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "")
      .slice(0, 80) || `product-${Date.now()}`
  );
}

export default function AdminPage() {
  const [settings, setSettings] = useState(() => ({
    owner: "",
    repo: "shiva-rudraksha-catalogue",
    branch: "main",
    token: "",
    ...getSettings(),
  }));
  const [connected, setConnected] = useState(false);
  const [products, setProducts] = useState([]);
  const [form, setForm] = useState(blankProduct);
  const [selectedFile, setSelectedFile] = useState(null);
  const [preview, setPreview] = useState("");
  const [status, setStatus] = useState("");
  const [busy, setBusy] = useState(false);
  const [search, setSearch] = useState("");

  useEffect(() => {
    document.title = "GitHub Product Manager | Shiva Rudraksha Inc.";
    return () => {
      if (preview.startsWith("blob:")) URL.revokeObjectURL(preview);
    };
  }, [preview]);

  const visibleProducts = useMemo(
    () =>
      products.filter((product) =>
        `${product.name} ${product.category} ${product.origin}`
          .toLowerCase()
          .includes(search.toLowerCase())
      ),
    [products, search]
  );

  async function connect(event) {
    event?.preventDefault();
    setBusy(true);
    setStatus("Connecting to GitHub…");
    try {
      await verifyRepository(settings);
      const currentProducts = await loadProductsFromGitHub(settings);
      setProducts(currentProducts);
      saveSettings(settings);
      setConnected(true);
      setStatus(`Connected. ${currentProducts.length} products loaded.`);
    } catch (error) {
      setStatus(error.message);
      setConnected(false);
    } finally {
      setBusy(false);
    }
  }

  async function refresh() {
    setBusy(true);
    setStatus("Refreshing catalogue from GitHub…");
    try {
      const currentProducts = await loadProductsFromGitHub(settings);
      setProducts(currentProducts);
      setStatus(`Catalogue refreshed. ${currentProducts.length} products loaded.`);
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  function disconnect() {
    clearSettings();
    setConnected(false);
    setProducts([]);
    setForm(blankProduct);
    setSelectedFile(null);
    setPreview("");
    setStatus("Disconnected. The token was removed from this browser session.");
  }

  function updateSetting(event) {
    const { name, value } = event.target;
    setSettings((current) => ({ ...current, [name]: value }));
  }

  function updateField(event) {
    const { name, value } = event.target;
    setForm((current) => ({ ...current, [name]: value }));
  }

  function chooseImage(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    if (!file.type.startsWith("image/")) {
      setStatus("Please select a JPG, PNG or WEBP image.");
      return;
    }
    if (file.size > 8 * 1024 * 1024) {
      setStatus("The image must be smaller than 8 MB.");
      return;
    }
    if (preview.startsWith("blob:")) URL.revokeObjectURL(preview);
    setSelectedFile(file);
    setPreview(URL.createObjectURL(file));
  }

  function cancelEdit() {
    if (preview.startsWith("blob:")) URL.revokeObjectURL(preview);
    setForm(blankProduct);
    setSelectedFile(null);
    setPreview("");
  }

  function editProduct(product) {
    cancelEdit();
    setForm({ ...product, price: String(product.price) });
    setPreview(publicAssetUrl(product.image));
    window.scrollTo({ top: 0, behavior: "smooth" });
    setStatus(`Editing ${product.name}`);
  }

  async function saveProduct(event) {
    event.preventDefault();

    const price = Number(form.price);
    if (!form.name.trim()) {
      setStatus("Product name is required.");
      return;
    }
    if (!Number.isFinite(price) || price < 0) {
      setStatus("Enter a valid product price.");
      return;
    }

    setBusy(true);
    setStatus("Saving product to GitHub…");

    try {
      let image = form.image;
      if (selectedFile) {
        const filename = safeFilename(form.name, selectedFile.name);
        setStatus("Uploading image to GitHub…");
        image = await uploadProductImage(settings, selectedFile, filename);
      }

      const product = {
        ...form,
        id: form.id || createSlug(form.name),
        name: form.name.trim(),
        price,
        image,
      };

      const updatedProducts = form.id
        ? products.map((item) => (item.id === form.id ? product : item))
        : [product, ...products];

      setStatus("Updating products.json…");
      await saveProductsToGitHub(
        settings,
        updatedProducts,
        form.id ? `Update product: ${product.name}` : `Add product: ${product.name}`
      );

      setProducts(updatedProducts);
      cancelEdit();
      setStatus(
        "Product committed successfully. GitHub Actions will publish the update shortly."
      );
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  async function deleteProduct(product) {
    if (!window.confirm(`Delete "${product.name}" from the catalogue?`)) return;

    setBusy(true);
    setStatus(`Deleting ${product.name}…`);
    try {
      const updatedProducts = products.filter((item) => item.id !== product.id);
      await saveProductsToGitHub(
        settings,
        updatedProducts,
        `Delete product: ${product.name}`
      );

      if (product.image) {
        try {
          await removeProductImage(settings, product.image);
        } catch (imageError) {
          console.warn("Product JSON was updated but image cleanup failed.", imageError);
        }
      }

      setProducts(updatedProducts);
      if (form.id === product.id) cancelEdit();
      setStatus(
        "Product deleted. GitHub Actions will publish the update shortly."
      );
    } catch (error) {
      setStatus(error.message);
    } finally {
      setBusy(false);
    }
  }

  if (!connected) {
    return (
      <div className="admin-page github-admin">
        <header className="admin-header">
          <div className="container admin-header-inner">
            <a href="./" className="admin-brand">
              <span>ॐ</span>
              <div>
                <strong>SHIVA RUDRAKSHA INC.</strong>
                <small>GitHub Product Manager</small>
              </div>
            </a>
            <a href="./" className="admin-back">
              <ArrowLeft size={18} /> View catalogue
            </a>
          </div>
        </header>

        <main className="container github-login-wrap">
          <section className="github-login-card">
            <div className="github-login-icon"><Github size={42} /></div>
            <span className="admin-kicker">Repository connection</span>
            <h1>Connect the product manager to GitHub</h1>
            <p>
              Enter a fine-grained GitHub token with access only to this repository
              and <strong>Contents: Read and write</strong>.
            </p>

            {status && <div className="admin-message">{status}</div>}

            <form onSubmit={connect} className="github-settings-form">
              <label>
                GitHub owner or username
                <input
                  name="owner"
                  value={settings.owner}
                  onChange={updateSetting}
                  placeholder="Example: muralimani"
                  required
                />
              </label>
              <label>
                Repository name
                <input
                  name="repo"
                  value={settings.repo}
                  onChange={updateSetting}
                  placeholder="shiva-rudraksha-catalogue"
                  required
                />
              </label>
              <label>
                Branch
                <input
                  name="branch"
                  value={settings.branch}
                  onChange={updateSetting}
                  placeholder="main"
                  required
                />
              </label>
              <label>
                Fine-grained personal access token
                <input
                  name="token"
                  type="password"
                  value={settings.token}
                  onChange={updateSetting}
                  placeholder="github_pat_..."
                  autoComplete="off"
                  required
                />
              </label>
              <button disabled={busy} className="github-connect-button">
                {busy ? <LoaderCircle className="spin" size={19} /> : <Github size={19} />}
                Connect to repository
              </button>
            </form>

            <div className="token-safety">
              <strong>Token safety</strong>
              <p>
                The token is kept only in this browser tab session. It is never
                written into the project or committed to GitHub. Close the browser
                or select Disconnect when finished.
              </p>
            </div>
          </section>
        </main>
      </div>
    );
  }

  return (
    <div className="admin-page github-admin">
      <header className="admin-header">
        <div className="container admin-header-inner">
          <a href="./" className="admin-brand">
            <span>ॐ</span>
            <div>
              <strong>SHIVA RUDRAKSHA INC.</strong>
              <small>GitHub Product Manager</small>
            </div>
          </a>
          <div className="admin-header-actions">
            <button onClick={refresh} disabled={busy}>
              <RefreshCw size={17} /> Refresh
            </button>
            <button onClick={disconnect}>
              <LogOut size={17} /> Disconnect
            </button>
            <a href="./" className="admin-back">
              <ArrowLeft size={18} /> Catalogue
            </a>
          </div>
        </div>
      </header>

      <main className="container admin-main">
        <section className="admin-intro">
          <div>
            <span>Live GitHub catalogue</span>
            <h1>Upload products directly to GitHub</h1>
            <p>
              Connected to <strong>{settings.owner}/{settings.repo}</strong> on{" "}
              <strong>{settings.branch}</strong>.
            </p>
          </div>
          <div className="github-connected">
            <CheckCircle2 size={18} /> Repository connected
          </div>
        </section>

        {status && <div className="admin-message global-message">{status}</div>}

        <div className="admin-layout">
          <section className="admin-panel product-form-panel">
            <div className="panel-title">
              <div>
                <span>{form.id ? "Edit product" : "New product"}</span>
                <h2>{form.id ? form.name : "Product information"}</h2>
              </div>
              {form.id && (
                <button className="icon-button" onClick={cancelEdit}>
                  <X />
                </button>
              )}
            </div>

            <form onSubmit={saveProduct} className="product-form">
              <label className="image-upload">
                {preview ? (
                  <img src={preview} alt="Product preview" />
                ) : (
                  <div>
                    <ImagePlus size={42} />
                    <strong>Choose product image</strong>
                    <span>JPG, PNG or WEBP — maximum 8 MB</span>
                  </div>
                )}
                <input type="file" accept="image/jpg,image/png,image/webp" onChange={chooseImage} />
              </label>

              {selectedFile && (
                <div className="selected-file">
                  <UploadCloud size={16} /> {selectedFile.name}
                </div>
              )}

              <div className="form-grid">
                <label className="full">
                  Product name *
                  <input
                    name="name"
                    value={form.name}
                    onChange={updateField}
                    placeholder="Example: 7 Mukhi Nepal Rudraksha"
                    required
                  />
                </label>
                <label>
                  Price (CAD) *
                  <input
                    name="price"
                    type="number"
                    min="0"
                    step="0.01"
                    value={form.price}
                    onChange={updateField}
                    required
                  />
                </label>
                <label>
                  Badge
                  <input name="badge" value={form.badge} onChange={updateField} />
                </label>
                <label>
                  Category
                  <select name="category" value={form.category} onChange={updateField}>
                    <option>Nepal Rudraksha</option>
                    <option>Indonesia Rudraksha</option>
                    <option>Special Rudraksha</option>
                    <option>Siddha Mala</option>
                    <option>Combinations</option>
                    <option>Bracelets and Malas</option>
                    <option>Gemstones</option>
                    <option>Other Products</option>
                  </select>
                </label>
                <label>
                  Origin
                  <select name="origin" value={form.origin} onChange={updateField}>
                    <option>Nepal</option>
                    <option>Indonesia</option>
                    <option>Nepal & Indonesia</option>
                    <option>India</option>
                    <option>Other</option>
                  </select>
                </label>
                <label className="full">
                  Description
                  <textarea
                    name="description"
                    rows="4"
                    value={form.description}
                    onChange={updateField}
                  />
                </label>
              </div>

              <button type="submit" className="save-product" disabled={busy}>
                {busy ? (
                  <LoaderCircle className="spin" size={18} />
                ) : form.id ? (
                  <Save size={18} />
                ) : (
                  <PackagePlus size={18} />
                )}
                {form.id ? "Commit product update" : "Upload and add product"}
              </button>
            </form>
          </section>

          <section className="admin-panel product-list-panel">
            <div className="panel-title list-title">
              <div>
                <span>Repository catalogue</span>
                <h2>{products.length} products</h2>
              </div>
              <input
                className="admin-search"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Search products"
              />
            </div>

            <div className="admin-product-list">
              {visibleProducts.map((product) => (
                <article className="admin-product-row" key={product.id}>
                  <div className="admin-thumb">
                    {product.image ? (
                      <img src={publicAssetUrl(product.image)} alt={product.name} />
                    ) : (
                      <span>ॐ</span>
                    )}
                  </div>
                  <div className="admin-product-info">
                    <strong>{product.name}</strong>
                    <span>{product.category} • {product.origin}</span>
                    <b>
                      CAD ${Number(product.price).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                      })}
                    </b>
                  </div>
                  <div className="row-actions">
                    <button disabled={busy} onClick={() => editProduct(product)}>
                      <Edit3 size={17} />
                    </button>
                    <button
                      disabled={busy}
                      className="delete"
                      onClick={() => deleteProduct(product)}
                    >
                      <Trash2 size={17} />
                    </button>
                  </div>
                </article>
              ))}
            </div>
          </section>
        </div>

        <section className="static-warning">
          <strong>Publishing process</strong>
          <p>
            Saving commits the image and products JSON to GitHub. The included
            GitHub Actions workflow then rebuilds and publishes GitHub Pages.
            Updates normally appear after the workflow completes.
          </p>
        </section>
      </main>
    </div>
  );
}
