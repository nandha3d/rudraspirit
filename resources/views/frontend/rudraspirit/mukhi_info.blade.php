@extends('frontend.layouts.app')

@section('content')
@php
    // Comprehensive spiritual metadata for 1 to 14 Mukhi Rudraksha
    $mukhiData = [
        1 => [
            'deity' => translate('Lord Shiva'),
            'planet' => translate('Sun'),
            'mantra' => 'Om Hreem Namah',
            'chakra' => translate('Sahasrara (Crown) Chakra'),
            'significance' => translate('The 1 Mukhi Rudraksha is the most sacred and rarest of all beads. It represents Lord Shiva himself and is considered the king of all Rudrakshas. It helps the wearer connect with the supreme consciousness and attain spiritual liberation (Moksha).'),
            'benefits_spiritual' => translate('Promotes deep meditation, connects with the divine energy, and helps destroy past negative karmas.'),
            'benefits_mental' => translate('Improves focus, concentration, leadership qualities, willpower, and decreases ego-centric behavior.'),
            'benefits_physical' => translate('Believed to alleviate headaches, heart-related conditions, and eye issues by regulating solar energy.'),
            'wearing_day' => translate('Monday'),
        ],
        2 => [
            'deity' => translate('Ardhanarishwara (Shiva & Parvati)'),
            'planet' => translate('Moon'),
            'mantra' => 'Om Namah',
            'chakra' => translate('Swadhisthana (Sacral) Chakra'),
            'significance' => translate('Representing the united form of Lord Shiva and Goddess Parvati, the 2 Mukhi Rudraksha symbolizes companionship, unity, and emotional balance. It is the bead of cooperation and relationship harmony.'),
            'benefits_spiritual' => translate('Aligns yin and yang energies, fostering unconditional love and deep spiritual alignment with partners.'),
            'benefits_mental' => translate('Brings emotional stability, relieves stress and anxiety, and helps resolve relationship conflicts.'),
            'benefits_physical' => translate('Believed to support kidney, intestine, and reproductive health while balancing bodily fluids.'),
            'wearing_day' => translate('Monday'),
        ],
        3 => [
            'deity' => translate('Agni (Fire God)'),
            'planet' => translate('Mars'),
            'mantra' => 'Om Kleem Namah',
            'chakra' => translate('Manipura (Solar Plexus) Chakra'),
            'significance' => translate('The 3 Mukhi Rudraksha represents Agni, the deity of fire. Just as fire purifies everything, this bead burns away past sins, emotional baggage, and guilt, leaving the wearer pure and energized.'),
            'benefits_spiritual' => translate('Cleanses the aura, destroys past negative karmic blockages, and releases soul-level blockages.'),
            'benefits_mental' => translate('Enhances self-esteem, courage, and motivation. Releases depression, anxiety, and guilt.'),
            'benefits_physical' => translate('Boosts metabolism, improves digestion, heals stomach ailments, and increases physical energy.'),
            'wearing_day' => translate('Sunday or Monday'),
        ],
        4 => [
            'deity' => translate('Lord Brahma'),
            'planet' => translate('Mercury'),
            'mantra' => 'Om Hreem Namah',
            'chakra' => translate('Vishuddha (Throat) Chakra'),
            'significance' => translate('Governed by Lord Brahma, the creator of the universe, and representing Goddess Saraswati, the 4 Mukhi Rudraksha is the bead of knowledge, creativity, intellect, and supreme communication.'),
            'benefits_spiritual' => translate('Assists in vocalizing spiritual truths, enhances prayer, and expands cosmic intelligence.'),
            'benefits_mental' => translate('Improves memory power, concentration, logical thinking, and communication skills. Ideal for students and professionals.'),
            'benefits_physical' => translate('Helpful in treating throat infections, thyroid imbalances, speech disorders, and asthma.'),
            'wearing_day' => translate('Thursday'),
        ],
        5 => [
            'deity' => translate('Kalagni Rudra (Lord Shiva)'),
            'planet' => translate('Jupiter'),
            'mantra' => 'Om Hreem Namah',
            'chakra' => translate('Anahata (Heart) Chakra'),
            'significance' => translate('The 5 Mukhi Rudraksha represents Kalagni Rudra, a fierce form of Shiva. It is the most commonly available and worn bead, revered for bringing peace of mind, good health, and overall well-being.'),
            'benefits_spiritual' => translate('Brings spiritual growth, fosters peaceful meditation, and cleanses the five elements of the body.'),
            'benefits_mental' => translate('Calms the mind, removes fears, reduces anger, and brings mental clarity and peace.'),
            'benefits_physical' => translate('Regulates blood pressure, helps in cardiac health, reduces stress levels, and boosts immunity.'),
            'wearing_day' => translate('Thursday'),
        ],
        6 => [
            'deity' => translate('Lord Kartikeya'),
            'planet' => translate('Venus'),
            'mantra' => 'Om Hreem Hoom Namah',
            'chakra' => translate('Muladhara (Root) Chakra'),
            'significance' => translate('Governed by Lord Kartikeya, the commander of the celestial army, the 6 Mukhi Rudraksha provides the wearer with supreme willpower, grounding, emotional stability, and the courage to conquer obstacles.'),
            'benefits_spiritual' => translate('Connects the wearer to the grounding energy of Mother Earth, aligning spiritual intent with physical action.'),
            'benefits_mental' => translate('Overcomes lethargy, builds focus, willpower, confidence, and leadership qualities.'),
            'benefits_physical' => translate('Strengthens nerves, relieves joint pain, manages throat and thyroid issues, and supports muscle health.'),
            'wearing_day' => translate('Monday'),
        ],
        7 => [
            'deity' => translate('Goddess Mahalakshmi'),
            'planet' => translate('Saturn'),
            'mantra' => 'Om Hoom Namah',
            'chakra' => translate('Anahata (Heart) Chakra'),
            'significance' => translate('The 7 Mukhi Rudraksha represents Goddess Mahalakshmi, the deity of wealth, luxury, and prosperity. It is traditionally worn to remove financial blocks and attract abundance.'),
            'benefits_spiritual' => translate('Aligns the heart with the frequency of gratitude and cosmic abundance, dissolving scarcity mindsets.'),
            'benefits_mental' => translate('Brings financial security, career growth, fame, and protects against bad luck or business hurdles.'),
            'benefits_physical' => translate('Helps alleviate chronic diseases, regulates liver/pancreas health, and relieves muscle pain.'),
            'wearing_day' => translate('Friday'),
        ],
        8 => [
            'deity' => translate('Lord Ganesha'),
            'planet' => translate('Rahu'),
            'mantra' => 'Om Hoom Namah',
            'chakra' => translate('Muladhara (Root) Chakra'),
            'significance' => translate('Representing Lord Ganesha, the Vighnaharta (remover of obstacles), the 8 Mukhi Rudraksha clears hurdles from the wearer\'s life path, bringing success, wisdom, and intelligence.'),
            'benefits_spiritual' => translate('Shields the user from negative astral energies and aligns them with the flow of effortless action.'),
            'benefits_mental' => translate('Boosts confidence, improves analytical thinking, and protects against bad planetary influences of Rahu.'),
            'benefits_physical' => translate('Supports nervous system function, relieves stress, and aids in healing lung/respiratory diseases.'),
            'wearing_day' => translate('Wednesday'),
        ],
        9 => [
            'deity' => translate('Goddess Durga'),
            'planet' => translate('Ketu'),
            'mantra' => 'Om Hreem Hoom Namah',
            'chakra' => translate('Ajna (Third Eye) Chakra'),
            'significance' => translate('The 9 Mukhi Rudraksha is blessed by Goddess Durga (Navadurga). It infuses the wearer with dynamic energy, fearlessness, courage, and protection from all forms of evil forces.'),
            'benefits_spiritual' => translate('Awakens inner power (Kundalini Shakti) and protects the wearer from negative psychic or black magic attacks.'),
            'benefits_mental' => translate('Overcomes fear, builds courage, removes depression, and counters the negative influences of Ketu.'),
            'benefits_physical' => translate('Strengthens the nervous system, relieves body aches, and boosts physical stamina and vitality.'),
            'wearing_day' => translate('Saturday or Monday'),
        ],
        10 => [
            'deity' => translate('Lord Vishnu'),
            'planet' => translate('All Planets'),
            'mantra' => 'Om Hreem Namah Namah',
            'chakra' => translate('Anahata (Heart) Chakra'),
            'significance' => translate('Governed by Lord Vishnu, the preserver of the universe, the 10 Mukhi Rudraksha acts as a powerful protective shield. It carries no negative planetary associations and brings peace.'),
            'benefits_spiritual' => translate('Purifies the soul, protects the energetic aura, and eliminates debts or blockages across past lives.'),
            'benefits_mental' => translate('Instills security, shields from negative thoughts/jealousy, and helps resolve legal issues.'),
            'benefits_physical' => translate('Relieves stress, anxiety, insomnia, and promotes deep, healing, restorative sleep.'),
            'wearing_day' => translate('Thursday'),
        ],
        11 => [
            'deity' => translate('Lord Hanuman'),
            'planet' => translate('All Planets'),
            'mantra' => 'Om Hreem Hoom Namah',
            'chakra' => translate('Ajna (Third Eye) Chakra'),
            'significance' => translate('The 11 Mukhi Rudraksha is blessed by Lord Hanuman and the Eleven Rudras. It grants the wearer incredible physical strength, high intellect, and absolute protection from accidents.'),
            'benefits_spiritual' => translate('Sharpens meditation, fosters true devotion, and activates the wisdom of the crown and third eye chakras.'),
            'benefits_mental' => translate('Improves decision-making skills, logic, bravery, and clears confusion and low self-esteem.'),
            'benefits_physical' => translate('Boosts immune system function, physical vitality, strength, and helps cure chronic fatigue.'),
            'wearing_day' => translate('Tuesday'),
        ],
        12 => [
            'deity' => translate('Lord Surya (Sun God)'),
            'planet' => translate('Sun'),
            'mantra' => 'Om Krom Srom Rom Namah',
            'chakra' => translate('Manipura (Solar Plexus) Chakra'),
            'significance' => translate('Governed by the Sun God, the 12 Mukhi Rudraksha radiates leadership, power, authority, and brilliance. It is worn to gain respect, eliminate self-doubt, and achieve success.'),
            'benefits_spiritual' => translate('Awakens inner fire, aligns willpower with cosmic purpose, and burns away karmic impurities.'),
            'benefits_mental' => translate('Brings absolute clarity of mind, boosts charisma, authority, leadership, and deletes fear.'),
            'benefits_physical' => translate('Improves cardiac health, regulates digestion, enhances eye health, and boosts cellular energy.'),
            'wearing_day' => translate('Sunday'),
        ],
        13 => [
            'deity' => translate('Lord Kamadeva / Indra'),
            'planet' => translate('Venus'),
            'mantra' => 'Om Hreem Namah',
            'chakra' => translate('Swadhisthana (Sacral) Chakra'),
            'significance' => translate('Blessed by Lord Kamadeva (the deity of desire and attraction) and Lord Indra, the 13 Mukhi Rudraksha enhances the wearer\'s magnetic charm, charisma, and ability to fulfill material desires.'),
            'benefits_spiritual' => translate('Balances desire and spiritual pursuit, channeling creative energies into higher consciousness.'),
            'benefits_mental' => translate('Builds exceptional interpersonal skills, confidence, social magnetism, and persuasive speech.'),
            'benefits_physical' => translate('Supports reproductive health, hormone balance, thyroid function, and increases skin radiance.'),
            'wearing_day' => translate('Monday'),
        ],
        14 => [
            'deity' => translate('Lord Hanuman / Shiva'),
            'planet' => translate('Saturn'),
            'mantra' => 'Om Namah',
            'chakra' => translate('Ajna (Third Eye) Chakra'),
            'significance' => translate('The 14 Mukhi Rudraksha (Devamani) is the most precious and powerful bead. Blessed by Lord Shiva\'s third eye and Lord Hanuman, it activates the sixth sense, intuition, and foresight.'),
            'benefits_spiritual' => translate('Opens the third eye chakra, enhances sixth sense, triggers spiritual visions, and supports spiritual mastery.'),
            'benefits_mental' => translate('Improves judgment, helps in critical decision-making, and eliminates Saturn\'s negative Sade Sati effects.'),
            'benefits_physical' => translate('Helps in nervous system healing, bone strength, regulates blood sugar, and promotes overall longevity.'),
            'wearing_day' => translate('Saturday'),
        ],
    ];

    $mId = $mukhiNumber ?? 1;
    // Prefer admin-managed DB content; fall back to the built-in reference data.
    if (!empty($mukhiInfo)) {
        $info = [
            'deity' => $mukhiInfo->deity,
            'planet' => $mukhiInfo->planet,
            'mantra' => $mukhiInfo->mantra,
            'chakra' => $mukhiInfo->chakra,
            'significance' => $mukhiInfo->significance,
            'benefits_spiritual' => $mukhiInfo->benefits_spiritual,
            'benefits_mental' => $mukhiInfo->benefits_mental,
            'benefits_physical' => $mukhiInfo->benefits_physical,
            'wearing_day' => $mukhiInfo->wearing_day,
        ];
    } else {
        $info = $mukhiData[$mId] ?? $mukhiData[1];
    }
@endphp

<main class="aiz-rudraspirit" style="background:var(--rs-cream); min-height:100vh;">
    <!-- Breadcrumbs & Header -->
    <section style="padding:54px 32px 24px; text-align:center;">
        <div style="font-size:13px; letter-spacing:.24em; text-transform:uppercase; color:var(--rs-gold);">
            <a href="{{ route('home') }}" style="text-decoration:none; color:inherit;">{{ translate('Home') }}</a> / 
            <a href="{{ route('search') }}" style="text-decoration:none; color:inherit;">{{ translate('Shop') }}</a> / 
            {{ $product->getTranslation('name') }}
        </div>
        <h1 class="rs-serif" style="font-weight:500; font-size:42px; letter-spacing:.04em; text-transform:uppercase; color:var(--rs-ink); margin:14px 0 0;">
            {{ $product->getTranslation('name') }}
        </h1>
        <p style="color:var(--rs-gold-deep); font-size:16px; letter-spacing:0.12em; text-transform:uppercase; margin-top:8px; font-weight:500;">
            {{ translate('Ruling Deity') }}: {{ $info['deity'] }}
        </p>
    </section>

    <!-- Main Layout Grid -->
    <section class="rs-info-layout" style="max-width:1280px; margin:0 auto; padding:20px 32px 90px; display:grid; grid-template-columns:380px 1fr; gap:50px; align-items:start;">
        
        <!-- Left Column: Product Card Card -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div style="background:#fff; border:1px solid var(--rs-cream-deep); border-radius:18px; padding:20px; box-shadow:0 10px 24px rgba(36,27,18,0.04);">
                <div style="font-size:13px; letter-spacing:.16em; text-transform:uppercase; color:var(--rs-ink-muted); margin-bottom:14px; text-align:center; font-weight:500;">
                    {{ translate('Authentic Product Preview') }}
                </div>
                <!-- Embed Product Card -->
                <div class="rs-embedded-card">
                    @include('frontend.rudraspirit.partials.product_card', ['product' => $product])
                </div>
                <div style="margin-top:16px; text-align:center;">
                    <a href="{{ route('product', $product->slug) }}" class="rs-btn" style="display:block; text-align:center; width:100%; text-decoration:none; padding:12px 0; font-size:13px;">
                        {{ translate('View Purchase Page') }} &rarr;
                    </a>
                </div>
            </div>
            
            <!-- Quality Seal -->
            <div style="background:#fff; border:1px solid var(--rs-cream-deep); border-radius:16px; padding:22px; text-align:center; display:flex; flex-direction:column; align-items:center; gap:8px;">
                <div class="rs-logo-dot" style="width:42px; height:42px;"></div>
                <h4 class="rs-serif" style="margin:8px 0 0; color:var(--rs-ink); font-size:18px; font-weight:600;">100% {{ translate('Lab Certified') }}</h4>
                <p style="font-size:13px; color:var(--rs-ink-muted); line-height:1.6; margin:0;">
                    {{ translate('Every Rudraksha bead we source is tested in ISO-certified laboratories for natural clefts, density, and authenticity before shipping.') }}
                </p>
            </div>
        </div>

        <!-- Right Column: Spiritual Profile & Detail Tabs -->
        <div>
            <!-- Spiritual Quick Profile Grid -->
            <div style="background:#fff; border:1px solid var(--rs-cream-deep); border-radius:18px; padding:28px; box-shadow:0 10px 24px rgba(36,27,18,0.04); margin-bottom:34px;">
                <h3 class="rs-serif" style="font-size:22px; font-weight:500; color:var(--rs-ink); margin:0 0 20px; border-bottom:1px solid var(--rs-cream-deep); padding-bottom:12px;">
                    {{ translate('Spiritual Profile') }}
                </h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px 30px;">
                    <div>
                        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.18em; color:var(--rs-gold);">{{ translate('Ruling Deity') }}</div>
                        <div style="font-size:17px; font-weight:500; color:var(--rs-ink); margin-top:4px;">{{ $info['deity'] }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.18em; color:var(--rs-gold);">{{ translate('Ruling Planet') }}</div>
                        <div style="font-size:17px; font-weight:500; color:var(--rs-ink); margin-top:4px;">{{ $info['planet'] }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.18em; color:var(--rs-gold);">{{ translate('Chakra Alignment') }}</div>
                        <div style="font-size:17px; font-weight:500; color:var(--rs-ink); margin-top:4px;">{{ $info['chakra'] }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.18em; color:var(--rs-gold);">{{ translate('Auspicious Chanting Day') }}</div>
                        <div style="font-size:17px; font-weight:500; color:var(--rs-ink); margin-top:4px;">{{ $info['wearing_day'] }}</div>
                    </div>
                </div>
                
                <div style="margin-top:24px; padding:18px; background:var(--rs-cream); border-left:4px solid var(--rs-gold); border-radius:4px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.18em; color:var(--rs-gold-deep); font-weight:600;">{{ translate('Beej Mantra') }}</div>
                    <div class="rs-serif" style="font-size:21px; color:var(--rs-ink); font-style:italic; margin-top:6px; letter-spacing:0.04em;">
                        "{{ $info['mantra'] }}"
                    </div>
                    <div style="font-size:12px; color:var(--rs-ink-muted); margin-top:6px;">
                        * {{ translate('Chant this mantra 108 times with devotion using a japa mala before wearing.') }}
                    </div>
                </div>
            </div>

            <!-- Detailed Tabs System -->
            <div style="background:#fff; border:1px solid var(--rs-cream-deep); border-radius:18px; overflow:hidden; box-shadow:0 10px 24px rgba(36,27,18,0.04);">
                
                <!-- Tab Triggers -->
                <div style="display:flex; border-bottom:1px solid var(--rs-cream-deep); background:var(--rs-cream);">
                    <button class="rs-tab-btn active" onclick="openTab(event, 'tab-significance')" style="flex:1; border:none; background:none; padding:16px; font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:0.12em; color:var(--rs-ink-muted); cursor:pointer; transition:all 0.3s; border-bottom:3px solid transparent;">
                        {{ translate('Significance') }}
                    </button>
                    <button class="rs-tab-btn" onclick="openTab(event, 'tab-benefits')" style="flex:1; border:none; background:none; padding:16px; font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:0.12em; color:var(--rs-ink-muted); cursor:pointer; transition:all 0.3s; border-bottom:3px solid transparent;">
                        {{ translate('Key Benefits') }}
                    </button>
                    <button class="rs-tab-btn" onclick="openTab(event, 'tab-ritual')" style="flex:1; border:none; background:none; padding:16px; font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:0.12em; color:var(--rs-ink-muted); cursor:pointer; transition:all 0.3s; border-bottom:3px solid transparent;">
                        {{ translate('How to Wear') }}
                    </button>
                </div>

                <!-- Tab Contents -->
                <div style="padding:32px;">
                    <!-- Significance Tab -->
                    <div id="tab-significance" class="rs-tab-content" style="display:block;">
                        <h4 class="rs-serif" style="font-size:20px; font-weight:500; color:var(--rs-ink); margin:0 0 14px;">
                            {{ translate('Mythological significance & Origin') }}
                        </h4>
                        <p style="font-size:15px; color:var(--rs-ink-soft); line-height:1.8; margin:0 0 16px;">
                            {{ $info['significance'] }}
                        </p>
                        <p style="font-size:15px; color:var(--rs-ink-soft); line-height:1.8; margin:0;">
                            {{ translate('According to ancient Vedic texts (Shiva Purana and Padma Purana), different mukhi beads represent different aspects of cosmic deities. Sourced directly from Nepal, this high-density bead carries concentrated bio-magnetic charges that interact positively with the wearer\'s aura.') }}
                        </p>
                    </div>

                    <!-- Benefits Tab -->
                    <div id="tab-benefits" class="rs-tab-content" style="display:none;">
                        <h4 class="rs-serif" style="font-size:20px; font-weight:500; color:var(--rs-ink); margin:0 0 20px;">
                            {{ translate('Energetic & Astrological Benefits') }}
                        </h4>
                        <div style="display:flex; flex-direction:column; gap:20px;">
                            <div style="display:flex; gap:16px; align-items:start;">
                                <div style="width:24px; height:24px; border-radius:50%; background:var(--rs-cream-deep); display:flex; align-items:center; justify-content:center; color:var(--rs-gold-deep); font-weight:bold; flex:none; font-size:12px;">ॐ</div>
                                <div>
                                    <h5 style="margin:0 0 4px; font-size:15px; font-weight:600; color:var(--rs-ink);">{{ translate('Spiritual Alignment') }}</h5>
                                    <p style="margin:0; font-size:14px; color:var(--rs-ink-soft); line-height:1.6;">{{ $info['benefits_spiritual'] }}</p>
                                </div>
                            </div>
                            <div style="display:flex; gap:16px; align-items:start;">
                                <div style="width:24px; height:24px; border-radius:50%; background:var(--rs-cream-deep); display:flex; align-items:center; justify-content:center; color:var(--rs-gold-deep); font-weight:bold; flex:none; font-size:12px;">ॐ</div>
                                <div>
                                    <h5 style="margin:0 0 4px; font-size:15px; font-weight:600; color:var(--rs-ink);">{{ translate('Mental & Emotional Calm') }}</h5>
                                    <p style="margin:0; font-size:14px; color:var(--rs-ink-soft); line-height:1.6;">{{ $info['benefits_mental'] }}</p>
                                </div>
                            </div>
                            <div style="display:flex; gap:16px; align-items:start;">
                                <div style="width:24px; height:24px; border-radius:50%; background:var(--rs-cream-deep); display:flex; align-items:center; justify-content:center; color:var(--rs-gold-deep); font-weight:bold; flex:none; font-size:12px;">ॐ</div>
                                <div>
                                    <h5 style="margin:0 0 4px; font-size:15px; font-weight:600; color:var(--rs-ink);">{{ translate('Physical Health Alignment') }}</h5>
                                    <p style="margin:0; font-size:14px; color:var(--rs-ink-soft); line-height:1.6;">{{ $info['benefits_physical'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ritual Tab -->
                    <div id="tab-ritual" class="rs-tab-content" style="display:none;">
                        <h4 class="rs-serif" style="font-size:20px; font-weight:500; color:var(--rs-ink); margin:0 0 16px;">
                            {{ translate('Step-by-Step Chanting & Wearing Ritual') }}
                        </h4>
                        <ol style="padding-left:20px; margin:0 0 20px; font-size:14px; color:var(--rs-ink-soft); line-height:1.8; display:flex; flex-direction:column; gap:8px;">
                            <li>
                                <strong>{{ translate('Purification') }}:</strong> 
                                {{ translate('Wash the bead with fresh water or Gangajal. You may gently clean it with a soft brush, then let it dry naturally.') }}
                            </li>
                            <li>
                                <strong>{{ translate('Best Wearing Day') }}:</strong> 
                                {{ translate('Traditionally worn on a') }} <strong>{{ $info['wearing_day'] }}</strong> {{ translate('morning before noon.') }}
                            </li>
                            <li>
                                <strong>{{ translate('Incense & Lamp') }}:</strong> 
                                {{ translate('Place the bead in your pooja area, light incense and a ghee lamp.') }}
                            </li>
                            <li>
                                <strong>{{ translate('Mantra Chanting') }}:</strong> 
                                {{ translate('Facing North or East, chant the Beej Mantra') }} <strong style="color:var(--rs-gold-deep);">"{{ $info['mantra'] }}"</strong> {{ translate('or') }} <strong>"Om Namah Shivaya"</strong> {{ translate('exactly 108 times using a japa mala.') }}
                            </li>
                            <li>
                                <strong>{{ translate('Wearing') }}:</strong> 
                                {{ translate('Wear the bead (strung in silk/wool thread or capped in silver/gold) and touch it to your forehead before placing it around your neck.') }}
                            </li>
                        </ol>
                        <div style="background:var(--rs-cream); padding:14px; border-radius:6px; font-size:13px; color:var(--rs-ink-muted); line-height:1.6;">
                            <strong>{{ translate('Precautions') }}:</strong> {{ translate('Remove the bead during sleep, funerals, or visits to newborn infants to keep the sacred energy pure.') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
</main>
@endsection

@section('script')
<script type="text/javascript">
    function openTab(evt, tabName) {
        // Hide all tab content
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("rs-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Remove active class from all buttons
        tablinks = document.getElementsByClassName("rs-tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        // Show current tab, and add active class to button that opened it
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }
</script>
@endsection
