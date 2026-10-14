import { useState } from "react";

export default function RudrakshaImage({ product, className = "", alt }) {
  const [src, setSrc] = useState(product.images.top);
  const [failed, setFailed] = useState(false);

  const handleError = () => {
    if (src !== product.images.front) {
      setSrc(product.images.front);
    } else {
      setFailed(true);
    }
  };

  if (failed) {
    return <div className={`image-placeholder ${className}`}><span>{product.mukhi}</span><small>Mukhi</small></div>;
  }

  return <img className={className} src={src} alt={alt || product.name} onError={handleError} loading="lazy" />;
}
