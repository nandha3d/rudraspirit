export default function ProductImage({ label }) {
  return (
    <div className="product-image" aria-label={label}>
      <svg viewBox="0 0 320 240" role="img" aria-hidden="true">
        <defs>
          <radialGradient id="bg" cx="50%" cy="35%" r="70%">
            <stop offset="0%" stopColor="#fff7e8" />
            <stop offset="100%" stopColor="#ead4ae" />
          </radialGradient>
          <radialGradient id="bead" cx="40%" cy="30%" r="70%">
            <stop offset="0%" stopColor="#9c5c2f" />
            <stop offset="55%" stopColor="#6e341d" />
            <stop offset="100%" stopColor="#3e1b10" />
          </radialGradient>
        </defs>
        <rect width="320" height="240" rx="24" fill="url(#bg)" />
        <circle cx="160" cy="118" r="70" fill="url(#bead)" />
        <ellipse cx="160" cy="118" rx="20" ry="70" fill="none" stroke="#c58b55" strokeWidth="5" />
        <ellipse cx="160" cy="118" rx="42" ry="70" fill="none" stroke="#8f532f" strokeWidth="4" />
        <path d="M160 48 C132 75 135 165 160 188" fill="none" stroke="#d3a06d" strokeWidth="4" />
        <path d="M160 48 C188 75 185 165 160 188" fill="none" stroke="#5e2a18" strokeWidth="4" />
        <circle cx="160" cy="118" r="8" fill="#2c130c" />
      </svg>
    </div>
  );
}
