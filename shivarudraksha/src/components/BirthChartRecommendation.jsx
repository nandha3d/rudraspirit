import { useState } from "react";
import {
  CalendarDays,
  Clock3,
  MapPin,
  MessageCircle,
  Sparkles,
  UserRound,
} from "lucide-react";

const initialForm = {
  fullName: "",
  dateOfBirth: "",
  placeOfBirth: "",
  timeOfBirth: "",
};

export default function BirthChartRecommendation() {
  const [form, setForm] = useState(initialForm);
  const [error, setError] = useState("");

  const updateField = (event) => {
    setForm((current) => ({
      ...current,
      [event.target.name]: event.target.value,
    }));
    setError("");
  };

  const submitRequest = (event) => {
    event.preventDefault();

    const fullName = form.fullName.trim();
    const placeOfBirth = form.placeOfBirth.trim();

    if (!fullName || !form.dateOfBirth || !placeOfBirth || !form.timeOfBirth) {
      setError("Please complete all birth details before submitting.");
      return;
    }

    const message = [
      "Namaste Shiva Rudraksha Inc.,",
      "",
      "I would like a Rudraksha recommendation based on my birth chart.",
      "",
      `Full name: ${fullName}`,
      `Date of birth: ${form.dateOfBirth}`,
      `Time of birth: ${form.timeOfBirth}`,
      `Place of birth: ${placeOfBirth}`,
      "",
      "Please review my birth details and recommend the suitable Rudraksha.",
    ].join("\n");

    const whatsappUrl =
      `https://wa.me/14372671257?text=${encodeURIComponent(message)}`;

    window.open(whatsappUrl, "_blank", "noopener,noreferrer");
  };

  return (
    <section id="birth-chart" className="birth-chart-section">
      <div className="container birth-chart-layout">
        <div className="birth-chart-intro">
          <span className="birth-chart-eyebrow">
            <Sparkles />
            PERSONALIZED GUIDANCE
          </span>

          <h2>Birth chart Rudraksha recommendation</h2>

          <p>
            Share your birth details to request a personalized Rudraksha
            recommendation. After submitting, WhatsApp will open with your
            information ready to send.
          </p>

          <div className="birth-chart-points">
            <span><UserRound /> Personalized review</span>
            <span><MessageCircle /> Direct WhatsApp request</span>
            <span><Sparkles /> Suitable Mukhi recommendation</span>
          </div>
        </div>

        <form className="birth-chart-form" onSubmit={submitRequest}>
          <div className="birth-chart-form-heading">
            <span>REQUEST A RECOMMENDATION</span>
            <h3>Enter your birth details</h3>
            <p>All fields are required.</p>
          </div>

          <label>
            <span><UserRound /> Full name</span>
            <input
              type="text"
              name="fullName"
              value={form.fullName}
              onChange={updateField}
              placeholder="Enter your full name"
              autoComplete="name"
              required
            />
          </label>

          <div className="birth-chart-form-grid">
            <label>
              <span><CalendarDays /> Date of birth</span>
              <input
                type="date"
                name="dateOfBirth"
                value={form.dateOfBirth}
                onChange={updateField}
                required
              />
            </label>

            <label>
              <span><Clock3 /> Time of birth</span>
              <input
                type="time"
                name="timeOfBirth"
                value={form.timeOfBirth}
                onChange={updateField}
                required
              />
            </label>
          </div>

          <label>
            <span><MapPin /> Place of birth</span>
            <input
              type="text"
              name="placeOfBirth"
              value={form.placeOfBirth}
              onChange={updateField}
              placeholder="City, state/province, country"
              autoComplete="address-level2"
              required
            />
          </label>

          {error && <p className="birth-chart-error">{error}</p>}

          <button type="submit" className="birth-chart-submit">
            <MessageCircle />
            Send Request via WhatsApp
          </button>

          <p className="birth-chart-note">
            Your information is not stored by this website. It is placed
            directly into the WhatsApp message you choose to send.
          </p>
        </form>
      </div>
    </section>
  );
}
