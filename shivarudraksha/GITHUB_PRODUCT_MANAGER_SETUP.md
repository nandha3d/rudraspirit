# GitHub Product Manager Setup

## How it works

1. Open the Product Manager at `#admin`.
2. Enter a fine-grained GitHub personal access token.
3. Upload a product image and enter the product details.
4. The application commits the image to:
   `public/images/products/`
5. The application updates:
   `public/data/products.json`
6. The commit triggers `.github/workflows/deploy.yml`.
7. GitHub Pages rebuilds and publishes the catalogue.

## 1. Create the repository

Create a GitHub repository named:

`shiva-rudraksha-catalogue`

Upload every file from this project to the `main` branch.

## 2. Configure the Vite base path

The included `vite.config.js` uses:

```js
base: "/shiva-rudraksha-catalogue/"
```

Change it when your repository has a different name.

For a custom domain, use:

```js
base: "/"
```

## 3. Enable GitHub Pages

Open:

Repository → Settings → Pages

Choose:

`Source: GitHub Actions`

## 4. Create a fine-grained token

In GitHub:

Settings → Developer settings → Personal access tokens → Fine-grained tokens

Use these settings:

- Resource owner: your GitHub account
- Repository access: Only select repositories
- Select: `shiva-rudraksha-catalogue`
- Repository permissions:
  - Contents: Read and write
  - Metadata: Read-only
- Choose a short expiration date

Do not place the token in `.env`, JavaScript source code, GitHub files or screenshots.

## 5. Open the product manager

Local:

`http://localhost:5173/shiva-rudraksha-catalogue/#admin`

GitHub Pages:

`https://YOUR-USERNAME.github.io/shiva-rudraksha-catalogue/#admin`

Enter:

- Owner: your GitHub username
- Repository: `shiva-rudraksha-catalogue`
- Branch: `main`
- Token: your fine-grained token

## Security notes

The token is stored in `sessionStorage`, not `localStorage`. It is removed when the browser session ends or when you select Disconnect.

Because this is a browser-based admin tool, use a fine-grained token that:

- Has access only to this one repository
- Has only Contents read/write permission
- Expires regularly
- Is revoked immediately if exposed

For stronger production security, replace the browser token approach with a GitHub App or a small authenticated backend.
