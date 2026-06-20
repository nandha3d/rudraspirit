<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mukhi_infos')) {
            return;
        }

        Schema::create('mukhi_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('mukhi_number')->unique();
            $table->string('deity')->nullable();
            $table->string('planet')->nullable();
            $table->string('mantra')->nullable();
            $table->string('chakra')->nullable();
            $table->text('significance')->nullable();
            $table->text('benefits_spiritual')->nullable();
            $table->text('benefits_mental')->nullable();
            $table->text('benefits_physical')->nullable();
            $table->string('wearing_day')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        // Seed with the previously hardcoded 1–14 Mukhi content so nothing changes visually.
        $now = now();
        $seed = $this->seedData();
        foreach ($seed as $row) {
            $row['status'] = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            DB::table('mukhi_infos')->insert($row);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mukhi_infos');
    }

    private function seedData(): array
    {
        return [
            ['mukhi_number' => 1, 'deity' => 'Lord Shiva', 'planet' => 'Sun', 'mantra' => 'Om Hreem Namah', 'chakra' => 'Sahasrara (Crown) Chakra', 'significance' => 'The 1 Mukhi Rudraksha is the most sacred and rarest of all beads. It represents Lord Shiva himself and is considered the king of all Rudrakshas. It helps the wearer connect with the supreme consciousness and attain spiritual liberation (Moksha).', 'benefits_spiritual' => 'Promotes deep meditation, connects with the divine energy, and helps destroy past negative karmas.', 'benefits_mental' => 'Improves focus, concentration, leadership qualities, willpower, and decreases ego-centric behavior.', 'benefits_physical' => 'Believed to alleviate headaches, heart-related conditions, and eye issues by regulating solar energy.', 'wearing_day' => 'Monday'],
            ['mukhi_number' => 2, 'deity' => 'Ardhanarishwara (Shiva & Parvati)', 'planet' => 'Moon', 'mantra' => 'Om Namah', 'chakra' => 'Swadhisthana (Sacral) Chakra', 'significance' => 'Representing the united form of Lord Shiva and Goddess Parvati, the 2 Mukhi Rudraksha symbolizes companionship, unity, and emotional balance. It is the bead of cooperation and relationship harmony.', 'benefits_spiritual' => 'Aligns yin and yang energies, fostering unconditional love and deep spiritual alignment with partners.', 'benefits_mental' => 'Brings emotional stability, relieves stress and anxiety, and helps resolve relationship conflicts.', 'benefits_physical' => 'Believed to support kidney, intestine, and reproductive health while balancing bodily fluids.', 'wearing_day' => 'Monday'],
            ['mukhi_number' => 3, 'deity' => 'Agni (Fire God)', 'planet' => 'Mars', 'mantra' => 'Om Kleem Namah', 'chakra' => 'Manipura (Solar Plexus) Chakra', 'significance' => 'The 3 Mukhi Rudraksha represents Agni, the deity of fire. Just as fire purifies everything, this bead burns away past sins, emotional baggage, and guilt, leaving the wearer pure and energized.', 'benefits_spiritual' => 'Cleanses the aura, destroys past negative karmic blockages, and releases soul-level blockages.', 'benefits_mental' => 'Enhances self-esteem, courage, and motivation. Releases depression, anxiety, and guilt.', 'benefits_physical' => 'Boosts metabolism, improves digestion, heals stomach ailments, and increases physical energy.', 'wearing_day' => 'Sunday or Monday'],
            ['mukhi_number' => 4, 'deity' => 'Lord Brahma', 'planet' => 'Mercury', 'mantra' => 'Om Hreem Namah', 'chakra' => 'Vishuddha (Throat) Chakra', 'significance' => 'Governed by Lord Brahma, the creator of the universe, and representing Goddess Saraswati, the 4 Mukhi Rudraksha is the bead of knowledge, creativity, intellect, and supreme communication.', 'benefits_spiritual' => 'Assists in vocalizing spiritual truths, enhances prayer, and expands cosmic intelligence.', 'benefits_mental' => 'Improves memory power, concentration, logical thinking, and communication skills. Ideal for students and professionals.', 'benefits_physical' => 'Helpful in treating throat infections, thyroid imbalances, speech disorders, and asthma.', 'wearing_day' => 'Thursday'],
            ['mukhi_number' => 5, 'deity' => 'Kalagni Rudra (Lord Shiva)', 'planet' => 'Jupiter', 'mantra' => 'Om Hreem Namah', 'chakra' => 'Anahata (Heart) Chakra', 'significance' => 'The 5 Mukhi Rudraksha represents Kalagni Rudra, a fierce form of Shiva. It is the most commonly available and worn bead, revered for bringing peace of mind, good health, and overall well-being.', 'benefits_spiritual' => 'Brings spiritual growth, fosters peaceful meditation, and cleanses the five elements of the body.', 'benefits_mental' => 'Calms the mind, removes fears, reduces anger, and brings mental clarity and peace.', 'benefits_physical' => 'Regulates blood pressure, helps in cardiac health, reduces stress levels, and boosts immunity.', 'wearing_day' => 'Thursday'],
            ['mukhi_number' => 6, 'deity' => 'Lord Kartikeya', 'planet' => 'Venus', 'mantra' => 'Om Hreem Hoom Namah', 'chakra' => 'Muladhara (Root) Chakra', 'significance' => 'Governed by Lord Kartikeya, the commander of the celestial army, the 6 Mukhi Rudraksha provides the wearer with supreme willpower, grounding, emotional stability, and the courage to conquer obstacles.', 'benefits_spiritual' => 'Connects the wearer to the grounding energy of Mother Earth, aligning spiritual intent with physical action.', 'benefits_mental' => 'Overcomes lethargy, builds focus, willpower, confidence, and leadership qualities.', 'benefits_physical' => 'Strengthens nerves, relieves joint pain, manages throat and thyroid issues, and supports muscle health.', 'wearing_day' => 'Monday'],
            ['mukhi_number' => 7, 'deity' => 'Goddess Mahalakshmi', 'planet' => 'Saturn', 'mantra' => 'Om Hoom Namah', 'chakra' => 'Anahata (Heart) Chakra', 'significance' => 'The 7 Mukhi Rudraksha represents Goddess Mahalakshmi, the deity of wealth, luxury, and prosperity. It is traditionally worn to remove financial blocks and attract abundance.', 'benefits_spiritual' => 'Aligns the heart with the frequency of gratitude and cosmic abundance, dissolving scarcity mindsets.', 'benefits_mental' => 'Brings financial security, career growth, fame, and protects against bad luck or business hurdles.', 'benefits_physical' => 'Helps alleviate chronic diseases, regulates liver/pancreas health, and relieves muscle pain.', 'wearing_day' => 'Friday'],
            ['mukhi_number' => 8, 'deity' => 'Lord Ganesha', 'planet' => 'Rahu', 'mantra' => 'Om Hoom Namah', 'chakra' => 'Muladhara (Root) Chakra', 'significance' => "Representing Lord Ganesha, the Vighnaharta (remover of obstacles), the 8 Mukhi Rudraksha clears hurdles from the wearer's life path, bringing success, wisdom, and intelligence.", 'benefits_spiritual' => 'Shields the user from negative astral energies and aligns them with the flow of effortless action.', 'benefits_mental' => 'Boosts confidence, improves analytical thinking, and protects against bad planetary influences of Rahu.', 'benefits_physical' => 'Supports nervous system function, relieves stress, and aids in healing lung/respiratory diseases.', 'wearing_day' => 'Wednesday'],
            ['mukhi_number' => 9, 'deity' => 'Goddess Durga', 'planet' => 'Ketu', 'mantra' => 'Om Hreem Hoom Namah', 'chakra' => 'Ajna (Third Eye) Chakra', 'significance' => 'The 9 Mukhi Rudraksha is blessed by Goddess Durga (Navadurga). It infuses the wearer with dynamic energy, fearlessness, courage, and protection from all forms of evil forces.', 'benefits_spiritual' => 'Awakens inner power (Kundalini Shakti) and protects the wearer from negative psychic or black magic attacks.', 'benefits_mental' => 'Overcomes fear, builds courage, removes depression, and counters the negative influences of Ketu.', 'benefits_physical' => 'Strengthens the nervous system, relieves body aches, and boosts physical stamina and vitality.', 'wearing_day' => 'Saturday or Monday'],
            ['mukhi_number' => 10, 'deity' => 'Lord Vishnu', 'planet' => 'All Planets', 'mantra' => 'Om Hreem Namah Namah', 'chakra' => 'Anahata (Heart) Chakra', 'significance' => 'Governed by Lord Vishnu, the preserver of the universe, the 10 Mukhi Rudraksha acts as a powerful protective shield. It carries no negative planetary associations and brings peace.', 'benefits_spiritual' => 'Purifies the soul, protects the energetic aura, and eliminates debts or blockages across past lives.', 'benefits_mental' => 'Instills security, shields from negative thoughts/jealousy, and helps resolve legal issues.', 'benefits_physical' => 'Relieves stress, anxiety, insomnia, and promotes deep, healing, restorative sleep.', 'wearing_day' => 'Thursday'],
            ['mukhi_number' => 11, 'deity' => 'Lord Hanuman', 'planet' => 'All Planets', 'mantra' => 'Om Hreem Hoom Namah', 'chakra' => 'Ajna (Third Eye) Chakra', 'significance' => 'The 11 Mukhi Rudraksha is blessed by Lord Hanuman and the Eleven Rudras. It grants the wearer incredible physical strength, high intellect, and absolute protection from accidents.', 'benefits_spiritual' => 'Sharpens meditation, fosters true devotion, and activates the wisdom of the crown and third eye chakras.', 'benefits_mental' => 'Improves decision-making skills, logic, bravery, and clears confusion and low self-esteem.', 'benefits_physical' => 'Boosts immune system function, physical vitality, strength, and helps cure chronic fatigue.', 'wearing_day' => 'Tuesday'],
            ['mukhi_number' => 12, 'deity' => 'Lord Surya (Sun God)', 'planet' => 'Sun', 'mantra' => 'Om Krom Srom Rom Namah', 'chakra' => 'Manipura (Solar Plexus) Chakra', 'significance' => 'Governed by the Sun God, the 12 Mukhi Rudraksha radiates leadership, power, authority, and brilliance. It is worn to gain respect, eliminate self-doubt, and achieve success.', 'benefits_spiritual' => 'Awakens inner fire, aligns willpower with cosmic purpose, and burns away karmic impurities.', 'benefits_mental' => 'Brings absolute clarity of mind, boosts charisma, authority, leadership, and deletes fear.', 'benefits_physical' => 'Improves cardiac health, regulates digestion, enhances eye health, and boosts cellular energy.', 'wearing_day' => 'Sunday'],
            ['mukhi_number' => 13, 'deity' => 'Lord Kamadeva / Indra', 'planet' => 'Venus', 'mantra' => 'Om Hreem Namah', 'chakra' => 'Swadhisthana (Sacral) Chakra', 'significance' => "Blessed by Lord Kamadeva (the deity of desire and attraction) and Lord Indra, the 13 Mukhi Rudraksha enhances the wearer's magnetic charm, charisma, and ability to fulfill material desires.", 'benefits_spiritual' => 'Balances desire and spiritual pursuit, channeling creative energies into higher consciousness.', 'benefits_mental' => 'Builds exceptional interpersonal skills, confidence, social magnetism, and persuasive speech.', 'benefits_physical' => 'Supports reproductive health, hormone balance, thyroid function, and increases skin radiance.', 'wearing_day' => 'Monday'],
            ['mukhi_number' => 14, 'deity' => 'Lord Hanuman / Shiva', 'planet' => 'Saturn', 'mantra' => 'Om Namah', 'chakra' => 'Ajna (Third Eye) Chakra', 'significance' => "The 14 Mukhi Rudraksha (Devamani) is the most precious and powerful bead. Blessed by Lord Shiva's third eye and Lord Hanuman, it activates the sixth sense, intuition, and foresight.", 'benefits_spiritual' => 'Opens the third eye chakra, enhances sixth sense, triggers spiritual visions, and supports spiritual mastery.', 'benefits_mental' => "Improves judgment, helps in critical decision-making, and eliminates Saturn's negative Sade Sati effects.", 'benefits_physical' => 'Helps in nervous system healing, bone strength, regulates blood sugar, and promotes overall longevity.', 'wearing_day' => 'Saturday'],
        ];
    }
};
