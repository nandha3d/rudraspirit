import { useState } from "react";
import { MessageCircle } from "lucide-react";

export default function Recommendation() {
  const [form,setForm] = useState({name:"",dob:"",time:"",place:"",purpose:"Business / Career"});
  const update = e => setForm({...form,[e.target.name]:e.target.value});
  const submit = e => {
    e.preventDefault();
    const message = `Namaste Shiva Rudraksha Inc.,

I would like a Rudraksha birth-chart recommendation.

Full Name: ${form.name}
Date of Birth: ${form.dob}
Time of Birth: ${form.time}
Place of Birth: ${form.place}
Primary Purpose: ${form.purpose}

Please review my details and recommend a suitable Rudraksha or combination.`;
    window.open(`https://wa.me/14372671257?text=${encodeURIComponent(message)}`,"_blank");
  };

  return (
    <main>
      <section className="page-hero compact">
        <span className="eyebrow">PERSONAL GUIDANCE</span>
        <h1>Birth Chart Recommendation</h1>
        <p>Submit your birth details securely through WhatsApp for a personalized consultation.</p>
      </section>
      <form className="recommend-form container" onSubmit={submit}>
        <label>Full Name<input required name="name" value={form.name} onChange={update}/></label>
        <label>Date of Birth<input required type="date" name="dob" value={form.dob} onChange={update}/></label>
        <label>Time of Birth<input required type="time" name="time" value={form.time} onChange={update}/></label>
        <label>Place of Birth<input required name="place" value={form.place} onChange={update}/></label>
        <label className="full-field">Primary Purpose
          <select name="purpose" value={form.purpose} onChange={update}>
            {["Business / Career","Wealth / Prosperity","Marriage / Relationships","Education / Memory","Health / Wellbeing","Spiritual Growth","Protection"].map(x=><option key={x}>{x}</option>)}
          </select>
        </label>
        <button className="primary-button form-submit" type="submit"><MessageCircle size={19}/> Send Request on WhatsApp</button>
      </form>
    </main>
  );
}
