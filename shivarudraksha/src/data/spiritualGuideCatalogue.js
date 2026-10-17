/**
 * Sacred Spiritual Guide Catalogue & Vedic Template Data
 * Extracted and modeled directly from the reference PDF (DOC-20260926-WA0028.pdf)
 * 
 * Each product entry follows the exact 2-card brochure format:
 *  - Card 1: Sacred Identity, Deity Illustration & Vedic Specifications (Associated God, Planet, Nakshatra, Chakra, Who Should Wear, When to Wear, Mantra)
 *  - Card 2: Traditional Benefits with 6 Sacred Medallion Icons & Explanations
 */

export const spiritualGuideData = {
  1: {
    mukhi: 1,
    title: "1 MUKHI RUDRAKSHA",
    subtitle: "Symbol of Supreme Consciousness",
    brand: "Shiva Rudraksha Inc.",
    tagline: "CONNECT ✦ BALANCE ✦ TRANSFORM",
    motto: "Ancient Wisdom for a Better Tomorrow",
    deityMantraSanskrit: "ॐ नमः शिवाय",
    deityMantraEnglish: "OM NAMAH SHIVAYA",
    deityArt: "images/spiritual-guide/1-mukhi/shiva-banner.avif",
    brochureCards: {
      specsCard: "images/spiritual-guide/1-mukhi/card1-specs.avif",
      benefitsCard: "images/spiritual-guide/1-mukhi/card2-benefits.avif",
      fullSpread: "images/spiritual-guide/1-mukhi/full-spread.avif",
    },
    specs: [
      {
        id: "god",
        label: "Associated God",
        value: "Lord Shiva",
        icon: "trishul",
      },
      {
        id: "planet",
        label: "Planet",
        value: "Sun (Surya)",
        icon: "sun",
      },
      {
        id: "nakshatra",
        label: "Nakshatra",
        value: "Krittika • Uttara Phalguni • Uttara Ashadha",
        icon: "star",
      },
      {
        id: "chakra",
        label: "Chakra",
        value: "Sahasrara (Crown Chakra)",
        icon: "lotus",
      },
      {
        id: "wearer",
        label: "Who Should Wear",
        value: "Spiritual seekers, meditators, leaders and those seeking higher purpose and inner clarity.",
        icon: "people",
      },
      {
        id: "when",
        label: "When to Wear",
        value: "Monday, preferably in the morning.",
        icon: "calendar",
      },
      {
        id: "mantra",
        label: "Mantra",
        value: "Om Namah Shivaya – 108 times.",
        icon: "om",
      },
    ],
    card1Footer: "HIGHER AWARENESS ✦ A BRIGHTER LIFE",
    benefitsTitle: "1 MUKHI RUDRAKSHA",
    benefitsSubtitle: "Traditional Benefits",
    benefits: [
      {
        id: 1,
        icon: "shiva_dhyana",
        title: "DEEPER CONNECTION WITH LORD SHIVA",
        text: "Considered the most sacred Rudraksha, it is believed to strengthen your spiritual connection with Lord Shiva and support higher consciousness.",
      },
      {
        id: 2,
        icon: "mental_focus",
        title: "ENHANCES MENTAL FOCUS AND CLARITY",
        text: "Helps in concentration, clear thinking and decision-making, allowing you to stay focused on what truly matters.",
      },
      {
        id: 3,
        icon: "deep_meditation",
        title: "SUPPORTS DEEP MEDITATION",
        text: "Traditionally worn to calm the mind, reduce distractions and help you attain deeper meditative states.",
      },
      {
        id: 4,
        icon: "leadership_sun",
        title: "BUILDS CONFIDENCE AND LEADERSHIP",
        text: "Associated with the energy of the Sun, it is believed to enhance self-confidence, willpower and leadership qualities, helping you take purposeful actions in life.",
      },
      {
        id: 5,
        icon: "detachment_mountain",
        title: "HELPS IN DETACHMENT AND WISDOM",
        text: "Supports letting go of ego, negative patterns and worldly attachments, guiding you towards a more meaningful and purposeful life.",
      },
      {
        id: 6,
        icon: "inner_peace_tree",
        title: "BRINGS INNER PEACE AND FULFILLMENT",
        text: "Helps in emotional balance, self-awareness and a sense of contentment, allowing you to experience a calmer, more centered and joyful state of being.",
      },
    ],
    card2Footer: "OM NAMAH SHIVAYA",
  },

  2: {
    mukhi: 2,
    title: "2 MUKHI RUDRAKSHA",
    subtitle: "Symbol of Unity and Harmony",
    brand: "Shiva Rudraksha Inc.",
    tagline: "CONNECT ✦ BALANCE ✦ TRANSFORM",
    motto: "Ancient Wisdom for a Better Tomorrow",
    deityMantraSanskrit: "ॐ नमः शिवाय",
    deityMantraEnglish: "OM NAMAH SHIVAYA",
    deityArt: "images/spiritual-guide/2-mukhi/deity-banner.avif",
    brochureCards: {
      specsCard: "images/spiritual-guide/2-mukhi/card1-specs.avif",
      benefitsCard: "images/spiritual-guide/2-mukhi/card2-benefits.avif",
      fullSpread: "images/spiritual-guide/2-mukhi/full-spread.avif",
    },
    specs: [
      {
        id: "god",
        label: "Associated God",
        value: "Ardhanarishvara (Lord Shiva and Goddess Shakti)",
        icon: "trishul",
      },
      {
        id: "planet",
        label: "Planet",
        value: "Moon (Chandra)",
        icon: "moon",
      },
      {
        id: "nakshatra",
        label: "Nakshatra",
        value: "Rohini • Hasta • Shravana",
        icon: "star",
      },
      {
        id: "chakra",
        label: "Chakra",
        value: "Swadhisthana (Sacral Chakra)",
        icon: "lotus",
      },
      {
        id: "wearer",
        label: "Who Should Wear",
        value: "Those seeking harmony in relationships, emotional balance, unity and inner stability.",
        icon: "people",
      },
      {
        id: "when",
        label: "When to Wear",
        value: "Monday, preferably in the morning.",
        icon: "calendar",
      },
      {
        id: "mantra",
        label: "Mantra",
        value: "Om Namah Shivaya – 108 times.",
        icon: "om",
      },
    ],
    card1Footer: "HIGHER AWARENESS ✦ A BRIGHTER LIFE",
    benefitsTitle: "2 MUKHI RUDRAKSHA",
    benefitsSubtitle: "Traditional Benefits",
    benefits: [
      {
        id: 1,
        icon: "harmony_couple",
        title: "MARRIAGE & RELATIONSHIP HARMONY",
        text: "Traditionally associated with strengthening understanding, emotional connection and harmony between husband and wife or partners.",
      },
      {
        id: 2,
        icon: "shiva_shakti",
        title: "SHIVA–SHAKTI BALANCE",
        text: "Represents Ardhanarishvara, the unified form of Shiva and Shakti, symbolizing balance between complementary masculine and feminine energies.",
      },
      {
        id: 3,
        icon: "emotional_stability",
        title: "EMOTIONAL STABILITY",
        text: "Because of its traditional association with the Moon, it is worn to support emotional steadiness and help calm an unsettled or fluctuating mind.",
      },
      {
        id: 4,
        icon: "heal_conflict",
        title: "HEALS CONFLICT & DISCONNECTION",
        text: "Traditionally chosen when there is misunderstanding, emotional distance or repeated conflict in close relationships, with the intention of restoring unity.",
      },
      {
        id: 5,
        icon: "family_bonding",
        title: "FAMILY BONDING & TOGETHERNESS",
        text: "Worn with the intention of encouraging mutual understanding, compassion and stronger bonds within the family.",
      },
      {
        id: 6,
        icon: "inner_unity",
        title: "INNER UNITY & SELF-ACCEPTANCE",
        text: "Beyond relationships, the two faces symbolize bringing opposing aspects of oneself into harmony, supporting greater self-acceptance and inner integration.",
      },
    ],
    card2Footer: "OM NAMAH SHIVAYA",
  },
};

/**
 * Resolver function: extracts or generates the spiritual guide card data
 * for any product in the catalog.
 */
export function getSpiritualGuideForProduct(product) {
  if (!product) return null;

  // Try direct mukhi match
  const mukhiNumber = Number(product.mukhi);
  if (mukhiNumber && spiritualGuideData[mukhiNumber]) {
    return spiritualGuideData[mukhiNumber];
  }

  // Check by ID if starts with a number (e.g. "1-mukhi-nepal-rudraksha")
  const idMatch = String(product.id || "").match(/^(\d+)-mukhi/i);
  if (idMatch && spiritualGuideData[Number(idMatch[1])]) {
    return spiritualGuideData[Number(idMatch[1])];
  }

  // Dynamic template fallback: construct guide structure from product info
  const name = product.name || "Rudraksha";
  const num = mukhiNumber || (idMatch ? Number(idMatch[1]) : 1);

  return {
    mukhi: num,
    title: name.toUpperCase(),
    subtitle: "Authentic Sacred Nepal Bead",
    brand: "Shiva Rudraksha Inc.",
    tagline: "CONNECT ✦ BALANCE ✦ TRANSFORM",
    motto: "Ancient Wisdom for a Better Tomorrow",
    deityMantraSanskrit: "ॐ नमः शिवाय",
    deityMantraEnglish: "OM NAMAH SHIVAYA",
    deityArt: `images/spiritual-guide/${num}-mukhi/deity-banner.avif`,
    brochureCards: {
      specsCard: `images/spiritual-guide/${num}-mukhi/card1-specs.avif`,
      benefitsCard: `images/spiritual-guide/${num}-mukhi/card2-benefits.avif`,
      fullSpread: `images/spiritual-guide/${num}-mukhi/full-spread.avif`,
    },
    specs: [
      { id: "god", label: "Associated God", value: "Lord Shiva / Sacred Deity", icon: "trishul" },
      { id: "planet", label: "Planet", value: "Cosmic Ruler", icon: "sun" },
      { id: "origin", label: "Origin", value: product.origin || "Nepal", icon: "star" },
      { id: "chakra", label: "Chakra", value: "Spiritual Energy Center", icon: "lotus" },
      { id: "wearer", label: "Who Should Wear", value: "Spiritual seekers and devotees looking for balance, focus and divine grace.", icon: "people" },
      { id: "when", label: "When to Wear", value: "Monday, preferably in the morning.", icon: "calendar" },
      { id: "mantra", label: "Mantra", value: "Om Namah Shivaya – 108 times.", icon: "om" },
    ],
    card1Footer: "HIGHER AWARENESS ✦ A BRIGHTER LIFE",
    benefitsTitle: name.toUpperCase(),
    benefitsSubtitle: "Traditional Benefits",
    benefits: [
      {
        id: 1,
        icon: "shiva_dhyana",
        title: "SPIRITUAL CONNECTION & AWAKENING",
        text: product.description || "Supports deep spiritual connection, personal protection and divine alignment.",
      },
      {
        id: 2,
        icon: "mental_focus",
        title: "MENTAL CLARITY & PURPOSE",
        text: "Aids in clear thinking, decision-making and focused meditation on life goals.",
      },
      {
        id: 3,
        icon: "deep_meditation",
        title: "INNER STRENGTH & BALANCE",
        text: "Provides grounding energy and harmony between physical vitality and emotional calm.",
      },
      {
        id: 4,
        icon: "leadership_sun",
        title: "REMOVAL OF LIMITATIONS",
        text: "Assists in dissolving negative influences and cultivating confidence and perseverance.",
      },
      {
        id: 5,
        icon: "detachment_mountain",
        title: "EMOTIONAL & ENERGETIC HEALING",
        text: "Fosters self-acceptance, calmness and harmonious resonance in daily activities.",
      },
      {
        id: 6,
        icon: "inner_peace_tree",
        title: "PEACE, PROSPERITY & WELL-BEING",
        text: "Creates an aura of sacred serenity, protective grace and holistic prosperity.",
      },
    ],
    card2Footer: "OM NAMAH SHIVAYA",
  };
}
