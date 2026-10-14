const API_ROOT = "https://api.github.com";
const SETTINGS_KEY = "shiva-rudraksha-github-settings";

export function getSettings() {
  try {
    return JSON.parse(sessionStorage.getItem(SETTINGS_KEY) || "{}");
  } catch {
    return {};
  }
}

export function saveSettings(settings) {
  sessionStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
}

export function clearSettings() {
  sessionStorage.removeItem(SETTINGS_KEY);
}

function headers(token) {
  return {
    Accept: "application/vnd.github+json",
    Authorization: `Bearer ${token}`,
    "X-GitHub-Api-Version": "2022-11-28",
    "Content-Type": "application/json",
  };
}

async function githubRequest(url, token, options = {}) {
  const response = await fetch(`${API_ROOT}${url}`, {
    ...options,
    headers: {
      ...headers(token),
      ...(options.headers || {}),
    },
  });

  if (!response.ok) {
    let detail = "";
    try {
      const body = await response.json();
      detail = body.message || JSON.stringify(body);
    } catch {
      detail = await response.text();
    }
    throw new Error(`GitHub API ${response.status}: ${detail}`);
  }

  if (response.status === 204) return null;
  return response.json();
}

export async function verifyRepository({ owner, repo, token }) {
  return githubRequest(`/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}`, token);
}

export async function getFile({ owner, repo, branch, token, path }) {
  try {
    return await githubRequest(
      `/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}/contents/${path
        .split("/")
        .map(encodeURIComponent)
        .join("/")}?ref=${encodeURIComponent(branch)}`,
      token
    );
  } catch (error) {
    if (String(error.message).includes("GitHub API 404")) return null;
    throw error;
  }
}

export function utf8ToBase64(value) {
  const bytes = new TextEncoder().encode(value);
  let binary = "";
  bytes.forEach((byte) => {
    binary += String.fromCharCode(byte);
  });
  return btoa(binary);
}

export function base64ToUtf8(value) {
  const cleaned = value.replace(/\n/g, "");
  const binary = atob(cleaned);
  const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
  return new TextDecoder().decode(bytes);
}

export async function putFile({
  owner,
  repo,
  branch,
  token,
  path,
  contentBase64,
  message,
  sha,
}) {
  return githubRequest(
    `/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}/contents/${path
      .split("/")
      .map(encodeURIComponent)
      .join("/")}`,
    token,
    {
      method: "PUT",
      body: JSON.stringify({
        message,
        content: contentBase64,
        branch,
        ...(sha ? { sha } : {}),
      }),
    }
  );
}

export async function deleteFile({
  owner,
  repo,
  branch,
  token,
  path,
  message,
  sha,
}) {
  return githubRequest(
    `/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}/contents/${path
      .split("/")
      .map(encodeURIComponent)
      .join("/")}`,
    token,
    {
      method: "DELETE",
      body: JSON.stringify({
        message,
        sha,
        branch,
      }),
    }
  );
}

export async function loadProductsFromGitHub(settings) {
  const file = await getFile({
    ...settings,
    path: "public/data/products.json",
  });
  if (!file?.content) return [];
  return JSON.parse(base64ToUtf8(file.content));
}

export async function saveProductsToGitHub(settings, products, message) {
  const path = "public/data/products.json";
  const existing = await getFile({ ...settings, path });

  return putFile({
    ...settings,
    path,
    sha: existing?.sha,
    message,
    contentBase64: utf8ToBase64(`${JSON.stringify(products, null, 2)}\n`),
  });
}

export async function uploadProductImage(settings, file, filename) {
  const path = `public/images/products/${filename}`;
  const existing = await getFile({ ...settings, path });
  const rawBase64 = await fileToBase64(file);

  await putFile({
    ...settings,
    path,
    sha: existing?.sha,
    message: `Upload product image: ${filename}`,
    contentBase64: rawBase64,
  });

  return `images/products/${filename}`;
}

export async function removeProductImage(settings, imagePath) {
  if (!imagePath || imagePath.startsWith("data:") || imagePath.startsWith("http")) return;
  const repositoryPath = imagePath.startsWith("/")
    ? `public${imagePath}`
    : `public/${imagePath}`;
  const existing = await getFile({ ...settings, path: repositoryPath });
  if (!existing?.sha) return;

  await deleteFile({
    ...settings,
    path: repositoryPath,
    sha: existing.sha,
    message: `Delete product image: ${repositoryPath.split("/").pop()}`,
  });
}

export function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error("Unable to read the selected image."));
    reader.onload = () => {
      const result = String(reader.result);
      resolve(result.substring(result.indexOf(",") + 1));
    };
    reader.readAsDataURL(file);
  });
}

export function safeFilename(name, originalFilename) {
  const extension = (originalFilename.split(".").pop() || "jpg")
    .toLowerCase()
    .replace(/[^a-z0-9]/g, "") || "jpg";
  const slug = name
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 70) || "product";
  return `${slug}-${Date.now()}.${extension}`;
}

export function publicAssetUrl(path) {
  if (!path) return "";
  if (/^(https?:|data:)/.test(path)) return path;
  const base = import.meta.env.BASE_URL || "/";
  return `${base}${path.replace(/^\/+/, "")}`;
}
