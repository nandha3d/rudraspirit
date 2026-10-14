const API_ROOT = "https://api.github.com";
const SETTINGS_KEY = "shiva-rudraksha-github-settings";

export const getSettings = () => {
  try {
    return JSON.parse(sessionStorage.getItem(SETTINGS_KEY) || "{}");
  } catch {
    return {};
  }
};

export const saveSettings = (settings) =>
  sessionStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));

export const clearSettings = () =>
  sessionStorage.removeItem(SETTINGS_KEY);

function headers(token) {
  return {
    Accept: "application/vnd.github+json",
    Authorization: `Bearer ${token}`,
    "X-GitHub-Api-Version": "2022-11-28",
    "Content-Type": "application/json",
  };
}

async function request(path, token, options = {}) {
  const response = await fetch(`${API_ROOT}${path}`, {
    ...options,
    headers: { ...headers(token), ...(options.headers || {}) },
  });

  if (!response.ok) {
    let detail = "";
    try {
      detail = (await response.json()).message;
    } catch {
      detail = await response.text();
    }
    throw new Error(`GitHub API ${response.status}: ${detail}`);
  }

  if (response.status === 204) return null;
  return response.json();
}

const encodePath = (path) =>
  path.split("/").map(encodeURIComponent).join("/");

export const verifyRepository = ({ owner, repo, token }) =>
  request(`/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}`, token);

export async function getFile(settings, path) {
  try {
    return await request(
      `/repos/${encodeURIComponent(settings.owner)}/${encodeURIComponent(
        settings.repo
      )}/contents/${encodePath(path)}?ref=${encodeURIComponent(settings.branch)}`,
      settings.token
    );
  } catch (error) {
    if (String(error.message).includes("GitHub API 404")) return null;
    throw error;
  }
}

function textToBase64(text) {
  const bytes = new TextEncoder().encode(text);
  let binary = "";
  bytes.forEach((byte) => (binary += String.fromCharCode(byte)));
  return btoa(binary);
}

function base64ToText(value) {
  const binary = atob(value.replace(/\n/g, ""));
  const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
  return new TextDecoder().decode(bytes);
}

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error("Unable to read selected image."));
    reader.onload = () => {
      const value = String(reader.result);
      resolve(value.substring(value.indexOf(",") + 1));
    };
    reader.readAsDataURL(file);
  });
}

async function putFile(settings, path, content, message, sha) {
  return request(
    `/repos/${encodeURIComponent(settings.owner)}/${encodeURIComponent(
      settings.repo
    )}/contents/${encodePath(path)}`,
    settings.token,
    {
      method: "PUT",
      body: JSON.stringify({
        message,
        content,
        branch: settings.branch,
        ...(sha ? { sha } : {}),
      }),
    }
  );
}

async function deleteFile(settings, path, message, sha) {
  return request(
    `/repos/${encodeURIComponent(settings.owner)}/${encodeURIComponent(
      settings.repo
    )}/contents/${encodePath(path)}`,
    settings.token,
    {
      method: "DELETE",
      body: JSON.stringify({
        message,
        sha,
        branch: settings.branch,
      }),
    }
  );
}

export async function loadRepositoryProducts(settings) {
  const file = await getFile(settings, "public/data/products.json");
  if (!file?.content) return [];
  return JSON.parse(base64ToText(file.content));
}

export async function saveRepositoryProducts(settings, products, message) {
  const path = "public/data/products.json";
  const existing = await getFile(settings, path);
  return putFile(
    settings,
    path,
    textToBase64(`${JSON.stringify(products, null, 2)}\n`),
    message,
    existing?.sha
  );
}

function productSlug(name) {
  return (
    name
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "")
      .slice(0, 80) || `product-${Date.now()}`
  );
}

export async function uploadSlotImage(settings, file, productName, slot, variantName = "") {
  const extension =
    (file.name.split(".").pop() || "jpg").toLowerCase().replace(/[^a-z0-9]/g, "") ||
    "jpg";
  const slug = productSlug(productName);
  const variantSlug = variantName ? productSlug(variantName) : "";
  const path = variantSlug
    ? `public/images/products/${slug}/${variantSlug}/${slot}.${extension}`
    : `public/images/products/${slug}/${slot}.${extension}`;
  const existing = await getFile(settings, path);
  await putFile(
    settings,
    path,
    await fileToBase64(file),
    `Upload ${slot} image for ${productName}${variantName ? ` - ${variantName}` : ""}`,
    existing?.sha
  );
  return variantSlug
    ? `images/products/${slug}/${variantSlug}/${slot}.${extension}`
    : `images/products/${slug}/${slot}.${extension}`;
}

export async function removeImage(settings, imagePath) {
  if (!imagePath || /^(https?:|data:|blob:)/.test(imagePath)) return;
  let clean = imagePath.replace(/^\/+/, "");
  const base = import.meta.env.BASE_URL || "/";
  if (base !== "/" && clean.startsWith(base.replace(/^\/+/, ""))) {
    clean = clean.substring(base.replace(/^\/+/, "").length).replace(/^\/+/, "");
  }
  const path = clean.startsWith("public/") ? clean : `public/${clean}`;
  const existing = await getFile(settings, path);
  if (existing?.sha) {
    await deleteFile(settings, path, `Delete ${path.split("/").pop()}`, existing.sha);
  }
}
