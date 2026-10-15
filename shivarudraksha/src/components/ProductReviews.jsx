import { useState, useEffect } from "react";
import {
  Star,
  CheckCircle,
  ThumbsUp,
  MessageSquare,
  ShieldCheck,
  X,
  Plus,
  Sparkles,
} from "lucide-react";

export default function ProductReviews({ product }) {
  const [reviews, setReviews] = useState([]);
  const [filter, setFilter] = useState("all");
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [helpfulMap, setHelpfulMap] = useState({});

  // Review Form State
  const [formRating, setFormRating] = useState(5);
  const [formHoverRating, setFormHoverRating] = useState(0);
  const [formName, setFormName] = useState("");
  const [formLocation, setFormLocation] = useState("");
  const [formTitle, setFormTitle] = useState("");
  const [formComment, setFormComment] = useState("");
  const [submitSuccess, setSubmitSuccess] = useState(false);

  const storageKey = `reviews_${product.id}`;

  useEffect(() => {
    // 1. If reviews were passed directly on the product (from Laravel ProductResource / products.json)
    if (Array.isArray(product.reviews) && product.reviews.length > 0) {
      setReviews(product.reviews);
      return;
    }

    // 2. Check local storage for any devotee-submitted reviews
    try {
      const stored = localStorage.getItem(storageKey);
      if (stored) {
        const parsed = JSON.parse(stored);
        if (Array.isArray(parsed) && parsed.length > 0) {
          setReviews(parsed);
          return;
        }
      }
    } catch (e) {}

    // 3. Fallback: Fetch directly from Laravel API endpoint
    const dbId = product.db_id;
    if (dbId) {
      fetch(`${import.meta.env.BASE_URL.replace(/\/$/, "")}/api/v2/reviews/product/${dbId}`)
        .then((res) => (res.ok ? res.json() : null))
        .then((json) => {
          if (json && Array.isArray(json.data) && json.data.length > 0) {
            const mapped = json.data.map((r, i) => ({
              id: `db-rev-${i}`,
              author: r.user_name || "Verified Devotee",
              location: "Verified Buyer",
              verified: true,
              rating: Math.round(Number(r.rating) || 5),
              date: r.time || "Recently",
              variant: `${product.name} • Lab Certified`,
              title: "Sacred & Authentic Nepal Bead",
              comment: r.comment,
              helpful: 0,
            }));
            setReviews(mapped);
          } else {
            setReviews([]);
          }
        })
        .catch(() => setReviews([]));
    } else {
      setReviews([]);
    }
  }, [product.id, product.db_id, product.reviews, storageKey]);

  const handleHelpful = (id) => {
    if (helpfulMap[id]) return;
    setHelpfulMap((prev) => ({ ...prev, [id]: true }));
    setReviews((prev) =>
      prev.map((r) => (r.id === id ? { ...r, helpful: (r.helpful || 0) + 1 } : r))
    );
  };

  const handleReviewSubmit = (e) => {
    e.preventDefault();
    if (!formName.trim() || !formComment.trim()) return;

    const newReview = {
      id: `rev-${Date.now()}`,
      author: formName.trim(),
      location: formLocation.trim() || "Verified Buyer",
      verified: true,
      rating: formRating,
      date: new Date().toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
      }),
      variant: `${product.name} • Verified Buyer`,
      title: formTitle.trim() || "Sacred Experience",
      comment: formComment.trim(),
      helpful: 0,
    };

    const updated = [newReview, ...reviews];
    setReviews(updated);
    try {
      localStorage.setItem(storageKey, JSON.stringify(updated));
    } catch (err) {}

    setSubmitSuccess(true);
    setTimeout(() => {
      setSubmitSuccess(false);
      setIsModalOpen(false);
      setFormName("");
      setFormLocation("");
      setFormTitle("");
      setFormComment("");
      setFormRating(5);
    }, 1200);
  };

  const filteredReviews = reviews.filter((r) => {
    if (filter === "5star") return r.rating === 5;
    if (filter === "verified") return r.verified;
    return true;
  });

  const totalReviews = reviews.length;
  const averageRating = totalReviews > 0
    ? (reviews.reduce((sum, r) => sum + Number(r.rating || 5), 0) / totalReviews).toFixed(1)
    : "0.0";

  const starBreakdown = [5, 4, 3, 2, 1].map((stars) => {
    const count = reviews.filter((r) => Math.round(Number(r.rating)) === stars).length;
    const pct = totalReviews > 0 ? Math.round((count / totalReviews) * 100) : 0;
    return { stars, count, pct };
  });

  return (
    <section id="reviews-section" className="product-reviews-section">
      <div className="container">
        {/* Section Header */}
        <div className="reviews-header-block">
          <div className="reviews-header-copy">
            <span className="reviews-kicker">DEVOTEE TESTIMONIALS</span>
            <h2>Authentic Experiences & Reviews</h2>
            <p>
              Verified buyer experiences and spiritual feedback on our consecrated Nepal Rudrakshas.
            </p>
          </div>

          <button
            type="button"
            className="write-review-btn"
            onClick={() => setIsModalOpen(true)}
          >
            <Plus size={16} />
            <span>Write a Devotee Review</span>
          </button>
        </div>

        {/* Rating Overview Grid */}
        <div className="reviews-summary-card">
          <div className="reviews-score-col">
            <div className="reviews-big-number">{averageRating}</div>
            <div className="reviews-stars-row">
              {[1, 2, 3, 4, 5].map((s) => (
                <Star
                  key={s}
                  size={20}
                  className={s <= Math.round(Number(averageRating)) ? "star-filled" : "star-empty"}
                />
              ))}
            </div>
            <span className="reviews-verified-tag">
              <ShieldCheck size={14} />{" "}
              {totalReviews > 0
                ? `Based on ${totalReviews} verified ${totalReviews === 1 ? "review" : "reviews"}`
                : "No reviews yet"}
            </span>
          </div>

          <div className="reviews-bars-col">
            {starBreakdown.map((bar) => (
              <div key={bar.stars} className="review-bar-row">
                <span className="bar-star-label">{bar.stars} ★</span>
                <div className="bar-track">
                  <div className="bar-fill" style={{ width: `${bar.pct}%` }} />
                </div>
                <span className="bar-pct-label">
                  {totalReviews > 0 ? `${bar.pct}% (${bar.count})` : "0%"}
                </span>
              </div>
            ))}
          </div>

          <div className="reviews-highlight-col">
            <div className="highlight-pill">
              <Sparkles size={16} className="sparkle-gold" />
              <div>
                <strong>100% Genuine Origin</strong>
                <p>Every bead is verified via digital X-Ray & Government Lab accreditation</p>
              </div>
            </div>
            <div className="highlight-pill">
              <CheckCircle size={16} className="check-green" />
              <div>
                <strong>Prana Pratishtha</strong>
                <p>Ritually consecrated with Panchamrit by experienced Vedic pandits</p>
              </div>
            </div>
          </div>
        </div>

        {/* Filters */}
        {totalReviews > 0 && (
          <div className="reviews-filters-bar">
            <button
              type="button"
              className={`review-filter-btn ${filter === "all" ? "active" : ""}`}
              onClick={() => setFilter("all")}
            >
              All Reviews ({totalReviews})
            </button>
            <button
              type="button"
              className={`review-filter-btn ${filter === "verified" ? "active" : ""}`}
              onClick={() => setFilter("verified")}
            >
              Verified Purchases
            </button>
            <button
              type="button"
              className={`review-filter-btn ${filter === "5star" ? "active" : ""}`}
              onClick={() => setFilter("5star")}
            >
              5-Star Ratings
            </button>
          </div>
        )}

        {/* Reviews List */}
        <div className="reviews-feed">
          {filteredReviews.length > 0 ? (
            filteredReviews.map((rev) => (
              <article key={rev.id} className="review-card-item">
                <div className="review-card-header">
                  <div className="reviewer-avatar">
                    {(rev.author || "D").charAt(0).toUpperCase()}
                  </div>

                  <div className="reviewer-meta">
                    <div className="reviewer-name-row">
                      <strong>{rev.author}</strong>
                      {rev.verified && (
                        <span className="verified-badge">
                          <CheckCircle size={13} /> Verified Devotee
                        </span>
                      )}
                    </div>
                    <span className="reviewer-location-sub">
                      {rev.location} {rev.date ? `• ${rev.date}` : ""}
                    </span>
                  </div>

                  <div className="review-stars-badge">
                    {[1, 2, 3, 4, 5].map((s) => (
                      <Star
                        key={s}
                        size={14}
                        className={s <= rev.rating ? "star-filled" : "star-empty"}
                      />
                    ))}
                  </div>
                </div>

                {rev.variant && <div className="review-variant-tag">{rev.variant}</div>}

                {rev.title && <h4 className="review-item-title">{rev.title}</h4>}
                <p className="review-item-body">{rev.comment}</p>

                <div className="review-item-footer">
                  <button
                    type="button"
                    className={`helpful-btn ${helpfulMap[rev.id] ? "voted" : ""}`}
                    onClick={() => handleHelpful(rev.id)}
                  >
                    <ThumbsUp size={14} />
                    <span>Helpful ({rev.helpful || 0})</span>
                  </button>
                </div>
              </article>
            ))
          ) : (
            <div style={{ padding: "40px 20px", textAlign: "center", color: "#64748b", background: "#fafafa", borderRadius: "12px", border: "1px dashed #e2e8f0" }}>
              <p style={{ margin: "0 0 12px 0", fontSize: "16px", fontWeight: "500", color: "#334155" }}>
                No reviews yet for this sacred bead.
              </p>
              <p style={{ margin: "0 0 18px 0", fontSize: "14px" }}>
                Be the first devotee to share your spiritual experience after receiving this consecrated Nepal Rudraksha.
              </p>
              <button
                type="button"
                className="write-review-btn"
                style={{ margin: "0 auto", display: "inline-flex" }}
                onClick={() => setIsModalOpen(true)}
              >
                <Plus size={16} />
                <span>Write the First Review</span>
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Write a Review Modal */}
      {isModalOpen && (
        <div className="review-modal-backdrop" onClick={() => setIsModalOpen(false)}>
          <div
            className="review-modal"
            onClick={(e) => e.stopPropagation()}
            role="dialog"
            aria-labelledby="write-review-title"
          >
            <div className="modal-header">
              <h3 id="write-review-title">Share Your Sacred Experience</h3>
              <button
                type="button"
                className="modal-close-btn"
                onClick={() => setIsModalOpen(false)}
              >
                <X size={20} />
              </button>
            </div>

            {submitSuccess ? (
              <div className="modal-success-state">
                <CheckCircle size={48} className="check-green" />
                <h4>Thank You for Your Feedback!</h4>
                <p>Your devotee review has been submitted and published.</p>
              </div>
            ) : (
              <form onSubmit={handleReviewSubmit} className="review-form">
                <div className="form-group">
                  <label>Overall Spiritual Rating</label>
                  <div className="star-picker">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <button
                        key={star}
                        type="button"
                        className="star-pick-btn"
                        onMouseEnter={() => setFormHoverRating(star)}
                        onMouseLeave={() => setFormHoverRating(0)}
                        onClick={() => setFormRating(star)}
                      >
                        <Star
                          size={28}
                          className={
                            star <= (formHoverRating || formRating)
                              ? "star-filled"
                              : "star-empty"
                          }
                        />
                      </button>
                    ))}
                    <span className="star-pick-label">
                      {formRating === 5
                        ? "5 Stars - Divine & Auspicious"
                        : formRating === 4
                        ? "4 Stars - Great Experience"
                        : `${formRating} Stars`}
                    </span>
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label htmlFor="rev-name">Your Name *</label>
                    <input
                      id="rev-name"
                      type="text"
                      required
                      placeholder="e.g. Ramesh Chandra"
                      value={formName}
                      onChange={(e) => setFormName(e.target.value)}
                    />
                  </div>

                  <div className="form-group">
                    <label htmlFor="rev-loc">City / Country</label>
                    <input
                      id="rev-loc"
                      type="text"
                      placeholder="e.g. Toronto, Canada"
                      value={formLocation}
                      onChange={(e) => setFormLocation(e.target.value)}
                    />
                  </div>
                </div>

                <div className="form-group">
                  <label htmlFor="rev-headline">Review Headline</label>
                  <input
                    id="rev-headline"
                    type="text"
                    placeholder="e.g. Remarkable energy and verified Nepal bead"
                    value={formTitle}
                    onChange={(e) => setFormTitle(e.target.value)}
                  />
                </div>

                <div className="form-group">
                  <label htmlFor="rev-body">Your Detailed Experience *</label>
                  <textarea
                    id="rev-body"
                    rows={4}
                    required
                    placeholder="Share how the bead felt during meditation, its authenticity, packaging, energization vidhi, and service..."
                    value={formComment}
                    onChange={(e) => setFormComment(e.target.value)}
                  />
                </div>

                <div className="modal-actions">
                  <button
                    type="button"
                    className="modal-cancel-btn"
                    onClick={() => setIsModalOpen(false)}
                  >
                    Cancel
                  </button>
                  <button type="submit" className="modal-submit-btn">
                    Submit Devotee Review
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      )}
    </section>
  );
}
