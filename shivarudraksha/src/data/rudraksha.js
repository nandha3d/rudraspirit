export const rudrakshaGuide = [
  {mukhi:1,deity:"Lord Shiva",planet:"Sun",chakra:"Sahasrara (Crown)",nakshatra:"Krittika, Uttara Phalguni, Uttara Ashadha",benefits:"Spiritual awareness, leadership, concentration and inner peace.",mantra:"Om Hreem Namah"},
  {mukhi:2,deity:"Ardhanarishvara",planet:"Moon",chakra:"Anahata (Heart)",nakshatra:"Rohini, Hasta, Shravana",benefits:"Harmony, relationships, emotional balance and unity.",mantra:"Om Namah"},
  {mukhi:3,deity:"Agni",planet:"Mars",chakra:"Manipura (Solar Plexus)",nakshatra:"Mrigashira, Chitra, Dhanishta",benefits:"Confidence, vitality and release from past emotional burdens.",mantra:"Om Kleem Namah"},
  {mukhi:4,deity:"Lord Brahma",planet:"Mercury",chakra:"Vishuddha (Throat)",nakshatra:"Ashlesha, Jyeshtha, Revati",benefits:"Knowledge, creativity, memory and communication.",mantra:"Om Hreem Namah"},
  {mukhi:5,deity:"Kalagni Rudra",planet:"Jupiter",chakra:"Vishuddha (Throat)",nakshatra:"Punarvasu, Vishakha, Purva Bhadrapada",benefits:"Peace, health, discipline and spiritual growth.",mantra:"Om Hreem Namah"},
  {mukhi:6,deity:"Lord Kartikeya",planet:"Venus",chakra:"Ajna (Third Eye)",nakshatra:"Bharani, Purva Phalguni, Purva Ashadha",benefits:"Wisdom, discipline, communication and confidence.",mantra:"Om Hreem Hum Namah"},
  {mukhi:7,deity:"Mahalakshmi",planet:"Saturn",chakra:"Muladhara (Root)",nakshatra:"Pushya, Anuradha, Uttara Bhadrapada",benefits:"Prosperity, stability and financial wellbeing.",mantra:"Om Hum Namah"},
  {mukhi:8,deity:"Lord Ganesha",planet:"Rahu",chakra:"Muladhara (Root)",nakshatra:"Ardra, Swati, Shatabhisha",benefits:"Obstacle removal, success and protection.",mantra:"Om Ganeshaya Namah"},
  {mukhi:9,deity:"Goddess Durga",planet:"Ketu",chakra:"Manipura (Solar Plexus)",nakshatra:"Ashwini, Magha, Mula",benefits:"Courage, power, protection and fearlessness.",mantra:"Om Hreem Hum Namah"},
  {mukhi:10,deity:"Lord Vishnu",planet:"All Planets",chakra:"Anahata (Heart)",nakshatra:"Traditionally regarded as universal",benefits:"Protection from negativity and greater inner stability.",mantra:"Om Hreem Namah"},
  {mukhi:11,deity:"Lord Hanuman",planet:"None",chakra:"Ajna (Third Eye)",nakshatra:"Traditionally regarded as universal",benefits:"Strength, confidence, devotion and fearlessness.",mantra:"Om Hreem Hum Namah"},
  {mukhi:12,deity:"Lord Surya",planet:"Sun",chakra:"Manipura (Solar Plexus)",nakshatra:"Krittika, Uttara Phalguni, Uttara Ashadha",benefits:"Authority, vitality, confidence and leadership.",mantra:"Om Kraum Sraum Raum Namah"},
  {mukhi:13,deity:"Lord Indra / Kamadeva",planet:"Venus",chakra:"Svadhisthana (Sacral)",nakshatra:"Bharani, Purva Phalguni, Purva Ashadha",benefits:"Charm, creativity, fulfilment and attraction.",mantra:"Om Hreem Namah"},
  {mukhi:14,deity:"Lord Hanuman / Shiva",planet:"Saturn",chakra:"Ajna (Third Eye)",nakshatra:"Pushya, Anuradha, Uttara Bhadrapada",benefits:"Intuition, decision-making and protection.",mantra:"Om Namah"},
  {mukhi:15,deity:"Lord Pashupatinath",planet:"Venus",chakra:"Anahata (Heart)",nakshatra:"Bharani, Purva Phalguni, Purva Ashadha",benefits:"Emotional healing, love and clarity.",mantra:"Om Hreem Namah"},
  {mukhi:16,deity:"Mahamrityunjaya Shiva",planet:"Moon",chakra:"Vishuddha (Throat)",nakshatra:"Rohini, Hasta, Shravana",benefits:"Protection, wellbeing, courage and resilience.",mantra:"Om Hreem Hum Namah"},
  {mukhi:17,deity:"Goddess Katyayani / Vishwakarma",planet:"Saturn",chakra:"Ajna (Third Eye)",nakshatra:"Pushya, Anuradha, Uttara Bhadrapada",benefits:"Opportunity, creativity, prosperity and achievement.",mantra:"Om Hreem Hum Namah"},
  {mukhi:18,deity:"Bhumi Devi",planet:"Earth",chakra:"Muladhara (Root)",nakshatra:"Traditionally associated with grounding",benefits:"Property, stability, abundance and grounding.",mantra:"Om Hreem Shreem Vasudhaye Swaha"},
  {mukhi:19,deity:"Lord Narayana",planet:"Sun",chakra:"Manipura (Solar Plexus)",nakshatra:"Krittika, Uttara Phalguni, Uttara Ashadha",benefits:"Recognition, success, protection and fulfilment.",mantra:"Om Hreem Namah"},
  {mukhi:20,deity:"Lord Brahma",planet:"All Planets",chakra:"Sahasrara (Crown)",nakshatra:"Traditionally regarded as universal",benefits:"Vision, knowledge, wisdom and spiritual progress.",mantra:"Om Hreem Hum Namah"},
  {mukhi:21,deity:"Lord Kubera",planet:"All Planets",chakra:"Sahasrara (Crown)",nakshatra:"Traditionally regarded as universal",benefits:"Prosperity, success, abundance and achievement.",mantra:"Om Hreem Hum Namah"}
];

export const products = rudrakshaGuide.map(item => ({
  ...item,
  name: `${item.mukhi} Mukhi Rudraksha`,
  origin: item.mukhi === 2 || item.mukhi === 14 ? "Indonesia" : "Nepal",
  certified: true,
  images: {
    front: `products/${item.mukhi}-mukhi/Front.jpg`,
    back: `products/${item.mukhi}-mukhi/Back.jpg`,
    top: `products/${item.mukhi}-mukhi/Top.jpg`,
    bottom: `products/${item.mukhi}-mukhi/Bottom.jpg`,
    certificate: `products/${item.mukhi}-mukhi/Certificate.jpg`,
    xray: `products/${item.mukhi}-mukhi/XRay.jpg`
  }
}));
