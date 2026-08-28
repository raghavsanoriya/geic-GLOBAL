<?php

namespace App\Support;

use Illuminate\Support\Str;

class DestinationCatalog
{
    public static function find(string $slug): ?array
    {
        $slug = match ($slug) {
            'united-kingdom' => 'uk',
            'united-states' => 'usa',
            default => $slug,
        };

        $destination = self::destinations()[$slug] ?? null;

        if (! $destination) {
            return null;
        }

        return self::withDefaults($slug, $destination);
    }

    public static function slugs(): array
    {
        return array_keys(self::destinations());
    }

    private static function withDefaults(string $slug, array $destination): array
    {
        $name = $destination['name'];
        $universities = array_map(
            fn (string $university): array => [
                'name' => $university,
                'logo' => self::universityLogo($slug, $university),
            ],
            self::universities()[$slug] ?? [],
        );

        return array_merge([
            'slug' => $slug,
            'source' => "https://transglobeedu.com/study-destination/{$slug}",
            'hero' => "assets/transglobe/destinations/detail/{$slug}-hero.webp",
            'card' => "assets/transglobe/destinations/{$slug}.jpg",
            'hero_position' => 'center',
            'gallery' => [
                ['src' => "assets/transglobe/destinations/{$slug}/campus.webp", 'alt' => "University campus in {$name}", 'label' => 'Campus life'],
                ['src' => "assets/transglobe/destinations/{$slug}/city.webp", 'alt' => "City and lifestyle in {$name}", 'label' => 'City life'],
                ['src' => "assets/transglobe/destinations/{$slug}/students.webp", 'alt' => "International students studying in {$name}", 'label' => 'Student experience'],
            ],
            'support_image' => [
                'src' => "assets/transglobe/destinations/{$slug}/city.webp",
                'alt' => "Career and lifestyle opportunities in {$name}",
            ],
            'universities' => $universities,
            'tagline' => "Globally recognised education and career-focused learning in {$name}.",
            'facts_intro' => "A quick view of the study environment, costs and opportunities in {$name}.",
            'requirements' => [
                'A valid passport with sufficient validity for admission and visa processing',
                'Class 10, Class 12 and previous degree transcripts and certificates',
                'English proficiency results such as IELTS, PTE Academic or TOEFL, where required',
                'Statement of Purpose explaining your academic background and career direction',
                'Academic or professional recommendation letters, where applicable',
                'Resume, work-experience evidence or portfolio for relevant programs',
            ],
            'journey' => [
                ['Discover', 'Free counselling', 'Map your academic profile, preferred course, budget and long-term career direction.'],
                ['Match', 'Shortlist and apply', 'Choose suitable institutions, prepare documents and submit strong applications.'],
                ['Offer', 'Receive your offer', 'Review the admission decision, conditions, scholarship and tuition details.'],
                ['Confirm', 'Accept your place', 'Complete the deposit and enrolment requirements to secure your seat.'],
                ['Prepare', 'Financials and documents', 'Arrange financial evidence, insurance, accommodation and visa documents.'],
                ['Submit', 'Apply for student visa', 'Lodge the relevant student visa or study-permit application with complete evidence.'],
                ['Decide', 'Visa decision', 'Respond to any requests, receive the outcome and prepare for departure.'],
                ['Arrive', "Fly to {$name}", 'Use pre-departure and arrival support to settle in and begin your studies confidently.'],
            ],
            'faqs' => [
                ["What are the basic requirements to study in {$name}?", 'Requirements normally include academic transcripts, a valid passport, accepted English-language evidence and financial documents. Exact criteria depend on the institution and program.'],
                ["When should I apply to study in {$name}?", 'Start 9–12 months before your preferred intake where possible. Early planning improves program choice and leaves enough time for scholarships and visa processing.'],
                ["Can international students work while studying in {$name}?", 'Work rights depend on the current conditions of your student visa or study permit. Our counsellors will explain the rules that apply to your course and intake.'],
                ["Are scholarships available in {$name}?", 'Many institutions offer merit-based, program-specific or regional awards. Availability and eligibility vary by institution and intake.'],
                ["Can I stay after completing my studies in {$name}?", 'Eligible graduates may have post-study work options. Duration and eligibility depend on the qualification and immigration rules in force when you apply.'],
            ],
        ], $destination);
    }

    private static function universityLogo(string $slug, string $name): string
    {
        $filename = Str::of(strtolower($name))
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-');

        return "assets/transglobe/destinations/{$slug}/universities/{$filename}.webp";
    }

    private static function destinations(): array
    {
        return [
            'australia' => [
                'name' => 'Australia',
                'flag' => 'au.png',
                'hero' => 'assets/transglobe/destinations/australia-detail-hero.jpg',
                'card' => 'assets/transglobe/destinations/australia.jpg',
                'tagline' => 'Build a globally respected education and career path through research-led universities, practical learning and welcoming student cities.',
                'overview' => 'Australia combines research-focused universities with teaching designed around industry. Students can study across the Group of Eight, technology-focused institutions and strong regional university networks.',
                'overview_2' => 'Sydney, Melbourne, Brisbane, Perth, Adelaide, Canberra and other student cities offer reliable infrastructure, healthcare, public transport and multicultural communities.',
                'stats' => [['42', 'Universities nationwide'], ['Go8', 'Plus regional, ATN and IRU networks'], ['2–3 years', 'Common post-study work range'], ['160+', 'Nationalities represented']],
                'benefits' => [
                    ['World-class universities', 'Research-intensive institutions, including the Group of Eight, with globally respected qualifications.'],
                    ['Work while studying', 'Eligible international students can work during teaching periods and more during scheduled breaks.'],
                    ['Safe and welcoming', 'Strong student protections, multicultural cities and a high standard of living support international students.'],
                    ['Wide course choice', 'Flexible pathways across business, engineering, IT, health, hospitality, creative arts and more.'],
                    ['Practical learning', 'Internships, industry projects, placements and work-integrated learning build career-ready experience.'],
                    ['Strong value', 'Internationally recognised education with scholarships and practical pathways that support long-term outcomes.'],
                ],
                'facts' => [['Universities', '42'], ['International networks', 'Go8, ATN and IRU'], ['Student cities', 'Sydney, Melbourne and more']],
                'facts_intro' => 'A practical view of Australia’s university system, student cities and graduate opportunities.',
                'requirements' => [
                    'Valid passport and an updated resume for postgraduate applicants',
                    'Class 10, Class 12 and previous degree transcripts and certificates',
                    'IELTS, PTE or TOEFL results required by the chosen institution',
                    'Academic or professional recommendation letters where applicable',
                    'Work experience evidence for selected postgraduate and MBA programs',
                ],
                'journey' => [
                    ['Discover', 'Free counselling', 'Map your profile, preferred course, budget and career direction.'],
                    ['Match', 'Shortlist and apply', 'Choose suitable universities, prepare documents and lodge applications.'],
                    ['Qualify', 'Offer and interview', 'Receive an offer and complete any Genuine Student interview.'],
                    ['Confirm', 'Accept and receive CoE', 'Pay the required deposit, arrange OSHC and obtain your eCoE.'],
                    ['Prepare', 'Financials and GS', 'Prepare financial evidence and your Genuine Student statement.'],
                    ['Submit', 'Lodge student visa', 'Submit the Subclass 500 application with the required evidence.'],
                    ['Decide', 'Visa decision', 'Receive the outcome and complete your pre-departure preparation.'],
                    ['Arrive', 'Fly to Australia', 'Use accommodation and arrival support to settle in confidently.'],
                ],
                'visa_title' => 'Subclass 500 Student Visa',
                'visa_copy' => 'Prepare accepted English evidence, a Genuine Student statement, financial capacity, Confirmation of Enrolment and Overseas Student Health Cover before lodging your application.',
                'costs' => [['Undergraduate tuition', 'AUD 24K–40K'], ['Postgraduate tuition', 'AUD 25K–45K'], ['Living & accommodation', 'AUD 29,710'], ['Airfare planning', 'AUD 2,000']],
                'careers' => ['Commerce & Analytics', 'Machine Learning & AI', 'Nursing & Paramedical', 'Accounting & Finance', 'Hospitality & Tourism', 'Education & Teaching', 'Psychology & Social Sciences', 'Environmental Science'],
                'intakes' => [['February', 'Main intake with the widest course selection.'], ['July', 'Strong mid-year intake across many institutions.'], ['October', 'Selected programs at participating universities.']],
                'faqs' => [
                    ['Which Australian universities are highly regarded?', 'Australia has internationally recognised institutions including the University of Melbourne, Australian National University and the University of Sydney. The Group of Eight represents leading research-intensive universities.'],
                    ['How many hours can international students work?', 'Eligible students can generally work up to 48 hours per fortnight during academic sessions and full-time during scheduled study breaks.'],
                    ['What is the Genuine Student statement?', 'The GS statement explains your genuine intention to study in Australia and is an important part of the Subclass 500 student visa process.'],
                    ['Are scholarships available?', 'Australian universities and government programs provide merit-based and other scholarships for eligible international students.'],
                    ['What are the main Australian intakes?', 'February is the primary intake, July is the main mid-year option, and selected institutions also offer an October intake.'],
                    ['Can graduates work in Australia after studying?', 'Eligible graduates may explore post-study work options. Duration and eligibility depend on the qualification, location and current immigration rules.'],
                ],
            ],
            'new-zealand' => [
                'name' => 'New Zealand', 'flag' => 'nz.png', 'card' => 'assets/transglobe/destinations/new-zealand.jpg',
                'tagline' => 'Government-regulated education, practical learning and an exceptional quality of life.',
                'overview' => 'New Zealand is home to eight government-funded universities, all internationally recognised for academic excellence, research and industry-focused education. Auckland, Wellington, Christchurch, Hamilton and Dunedin combine modern infrastructure with safe, welcoming multicultural communities.',
                'overview_2' => 'Programs frequently include internships, industry projects, research and work-integrated learning. Globally respected qualifications and post-study opportunities make New Zealand a strong choice for students seeking career-ready education and personal growth.',
                'stats' => [['8', 'Government-funded universities'], ['125,000+', 'International students'], ['Up to 3 years', 'Post-study work'], ['180+', 'Nationalities represented']],
                'benefits' => [
                    ['World-class universities', 'All eight universities are internationally recognised and regulated within a quality-focused education system.'],
                    ['Work while studying', 'Eligible students can gain experience during study and work full-time during scheduled breaks, subject to visa conditions.'],
                    ['Safe and welcoming', 'Peaceful communities, reliable services and strong student support create an excellent learning environment.'],
                    ['Practical learning', 'Internships, research projects and industry placements connect classroom knowledge to real workplaces.'],
                    ['Wide course choice', 'Popular fields include IT, business, engineering, health sciences, agriculture and environmental science.'],
                    ['Strong value', 'Competitive tuition and living costs combine with respected qualifications and graduate opportunities.'],
                ],
                'facts' => [['Population', '5.3 million'], ['Living costs', 'NZD 20K–27K / year'], ['Climate', '10°C–25°C']],
                'visa_title' => 'New Zealand Student Visa', 'visa_copy' => 'Secure an Offer of Place, demonstrate financial capacity, meet health and character requirements and lodge your application with Immigration New Zealand.',
                'costs' => [['Undergraduate tuition', 'NZD 28,000–40,000 / year'], ['Postgraduate tuition', 'NZD 35,000–55,000 / year'], ['Living and accommodation', 'NZD 20,000–27,000 / year'], ['Student visa', 'About NZD 850']],
                'careers' => ['Information Technology', 'Artificial Intelligence & Data', 'Engineering & Construction', 'Healthcare & Nursing', 'Finance & Accounting', 'Agriculture & Agribusiness', 'Hospitality & Tourism', 'Sustainability'],
                'intakes' => [['February', 'Main intake with the widest course and scholarship choice.'], ['July', 'Major mid-year intake across many institutions.'], ['September–November', 'Limited intake for selected programs.']],
            ],
            'uk' => [
                'name' => 'United Kingdom', 'flag' => 'gb.png', 'card' => 'assets/transglobe/destinations/uk.jpg',
                'tagline' => 'Prestigious universities, shorter degrees and globally recognised qualifications.',
                'overview' => 'The United Kingdom combines Russell Group research universities with respected modern and specialist institutions. London, Manchester, Birmingham, Edinburgh, Glasgow, Leeds and Bristol offer world-class infrastructure and diverse student communities.',
                'overview_2' => 'Industry-designed degrees, placements, live projects and professional accreditation help students graduate with relevant experience. Many master’s programs take one year, while most undergraduate degrees take three years.',
                'stats' => [['160+', 'Universities nationwide'], ['Russell Group', 'Research excellence'], ['1 year', 'Typical master’s degree'], ['180+', 'Nationalities represented']],
                'benefits' => [
                    ['Academic prestige', 'Study at globally respected research, modern and specialist universities.'],
                    ['Shorter programs', 'Most master’s degrees are completed in one year, reducing time and total living costs.'],
                    ['Industry exposure', 'Placements, internships, laboratories and live briefs add practical value.'],
                    ['Course variety', 'Choose from thousands of specialist and interdisciplinary programs.'],
                    ['Multicultural cities', 'Live and learn in globally connected student cities across four nations.'],
                    ['Graduate opportunities', 'Build experience after study through eligible graduate work routes.'],
                ],
                'facts' => [['Universities', '160+'], ['Living costs', 'GBP 12K–18K / year'], ['Typical PG duration', '1 year']],
                'visa_title' => 'UK Student Visa', 'visa_copy' => 'You normally need a Confirmation of Acceptance for Studies (CAS), financial evidence, accepted English proof and any required health or credibility documents.',
                'costs' => [['Undergraduate tuition', 'GBP 12,000–25,000 / year'], ['Postgraduate tuition', 'GBP 13,000–30,000 / year'], ['Living and accommodation', 'GBP 12,000–18,000 / year'], ['Visa and health surcharge', 'Depends on course duration']],
                'careers' => ['Business & Finance', 'Artificial Intelligence', 'Engineering', 'Healthcare', 'Creative Industries', 'Law & Policy', 'Data Analytics', 'Life Sciences'],
                'intakes' => [['September / October', 'Primary intake with maximum university and program choice.'], ['January / February', 'Strong secondary intake for selected universities.'], ['May', 'Limited intake for selected courses.']],
            ],
            'ireland' => [
                'name' => 'Ireland', 'flag' => 'ie.png', 'card' => 'assets/transglobe/destinations/ireland.jpg',
                'tagline' => 'English-medium education inside the EU with powerful technology and life-sciences connections.',
                'overview' => 'Ireland’s universities, Technological Universities and Institutes of Technology are known for research and practical, industry-focused education. Dublin, Cork, Galway, Limerick and Waterford offer active student communities and a welcoming environment.',
                'overview_2' => 'As a European technology and life-sciences hub, Ireland hosts major multinational employers including leading technology, pharmaceutical and financial-services companies, creating access to internships and graduate careers.',
                'stats' => [['24+', 'Higher-education institutions'], ['English-speaking', 'European Union nation'], ['1–2 years', 'Stamp 1G graduate stay'], ['160+', 'Nationalities on campus']],
                'benefits' => [
                    ['Industry-connected study', 'Programs connect classroom learning with projects, placements and employer collaboration.'],
                    ['Technology hub', 'Study close to major global technology and life-sciences employers.'],
                    ['English-speaking EU base', 'Access European opportunity while studying and living in English.'],
                    ['Welcoming communities', 'Student-friendly cities are known for culture, warmth and international networks.'],
                    ['Research and innovation', 'Institutions offer modern facilities and practical, solution-oriented learning.'],
                    ['Strong return', 'Recognised degrees and graduate pathways support international career growth.'],
                ],
                'facts' => [['International students', '44,500+'], ['Graduate stay', '1–2 years'], ['Key hubs', 'Dublin, Cork, Galway']],
                'visa_title' => 'Ireland Long Stay Study Visa', 'visa_copy' => 'Applicants generally need an eligible course offer, evidence of fee payment, academic and English documents, private medical insurance and proof of sufficient funds.',
                'costs' => [['Undergraduate tuition', 'EUR 10,000–25,000 / year'], ['Postgraduate tuition', 'EUR 12,000–30,000 / year'], ['Living and accommodation', 'EUR 10,000–18,000 / year'], ['Visa and registration', 'Budget separately']],
                'careers' => ['Software & Cloud', 'Data Analytics', 'Pharmaceuticals', 'Medical Technology', 'Finance & FinTech', 'Engineering', 'Supply Chain', 'Business Services'],
                'intakes' => [['September', 'Primary intake with the broadest program choice.'], ['January / February', 'Secondary intake at selected institutions.']],
            ],
            'germany' => [
                'name' => 'Germany', 'flag' => 'de.png', 'card' => 'assets/transglobe/destinations/germany.webp',
                'tagline' => 'World-class public education, engineering excellence and a strong European career pathway.',
                'overview' => 'Germany combines centuries of academic tradition with cutting-edge technological research. Most public universities charge no standard tuition, though semester contributions and living costs still apply.',
                'overview_2' => 'Students can choose research universities or practice-oriented Universities of Applied Sciences, with thousands of English-taught options. Graduates can explore an 18-month job-seeking period under current rules.',
                'stats' => [['400+', 'Public and private universities'], ['Low tuition', 'At many public universities'], ['18 months', 'Graduate job-seeking period'], ['420,000+', 'International students']],
                'benefits' => [
                    ['Low public tuition', 'Many public universities charge no standard tuition, though semester fees apply.'],
                    ['Engineering excellence', 'Globally respected technical education and advanced research infrastructure.'],
                    ['English-taught options', 'A growing selection of international bachelor’s and master’s programs.'],
                    ['Applied learning', 'Universities of Applied Sciences connect study with industry practice.'],
                    ['European access', 'Live in the heart of Europe with strong transport and Schengen connectivity.'],
                    ['Graduate pathway', 'Eligible graduates may remain for up to 18 months to seek skilled employment.'],
                ],
                'facts' => [['Semester contribution', 'EUR 150–400'], ['Blocked account', 'Approx. EUR 11,904'], ['Graduate stay', '18 months']],
                'visa_title' => 'German Student Visa', 'visa_copy' => 'Admission, recognised academic qualifications, proof of funds—often through a blocked account—health insurance and language evidence form the core requirements.',
                'costs' => [['Public-university tuition', 'Often no standard tuition'], ['Semester contribution', 'EUR 150–400'], ['Living and accommodation', 'EUR 11,000–15,000 / year'], ['Blocked-account benchmark', 'About EUR 11,904']],
                'careers' => ['Mechanical Engineering', 'Automotive Technology', 'Artificial Intelligence', 'Renewable Energy', 'Robotics', 'Software Engineering', 'Biotechnology', 'Supply Chain'],
                'intakes' => [['September / October', 'Winter semester and the main intake.'], ['March / April', 'Summer semester with selected programs.']],
            ],
            'europe' => [
                'name' => 'Europe', 'flag' => 'eu.png', 'card' => 'assets/transglobe/destinations/europe-card.jpg',
                'tagline' => 'Affordable, internationally recognised education across culturally rich European destinations.',
                'overview' => 'France, Spain, Italy, Austria and other European destinations offer internationally recognised qualifications, high academic standards and diverse multicultural experiences. Students can choose from historic public universities, specialist schools and modern business institutions.',
                'overview_2' => 'English-taught programs, efficient travel links and Bologna Process-compatible qualifications make European study flexible and globally portable. Costs, visa procedures and graduate rights vary by country.',
                'stats' => [['4,000+', 'Higher-education institutions'], ['Bologna Process', 'Compatible qualifications'], ['1–2 years', 'Typical graduate stay options'], ['700,000+', 'International students']],
                'benefits' => [
                    ['Affordable pathways', 'Public universities in several countries offer highly subsidised tuition.'],
                    ['Academic heritage', 'Study at historic institutions with modern teaching and research.'],
                    ['English-taught degrees', 'Thousands of international programs are delivered entirely in English.'],
                    ['Cultural immersion', 'Learn inside some of the world’s richest cultural and creative centres.'],
                    ['Schengen mobility', 'Explore neighbouring countries through connected transport networks.'],
                    ['Specialist strengths', 'Choose luxury, design, hospitality, engineering, sustainability and more.'],
                ],
                'facts' => [['Public tuition', 'EUR 1,500–8,000 / year'], ['Living costs', 'EUR 700–1,600 / month'], ['Study work', 'Often 20–30 hours / week']],
                'visa_title' => 'European National Student Visa', 'visa_copy' => 'Requirements vary by country but normally include an unconditional offer, sufficient funds, qualifying health insurance, accommodation and translated or legalised academic documents.',
                'costs' => [['Public universities', 'EUR 1,500–8,000 / year'], ['Private universities', 'EUR 6,000–40,000+ / year'], ['Living and accommodation', 'EUR 700–1,600 / month'], ['Visa fee', 'Varies by destination']],
                'careers' => ['Engineering & IT', 'Luxury Management', 'Fashion & Design', 'Architecture', 'Hospitality', 'Sustainable Energy', 'Finance & Logistics', 'Advanced Manufacturing'],
                'intakes' => [['September / October', 'Primary autumn intake across most European systems.'], ['February / March', 'Limited spring intake at selected institutions.']],
            ],
            'usa' => [
                'name' => 'United States', 'flag' => 'us.png', 'card' => 'assets/transglobe/destinations/usa.jpg',
                'tagline' => 'Unmatched program choice, research leadership and career-building practical experience.',
                'overview' => 'The United States includes Ivy League universities, major public research institutions, private universities and community colleges. Its flexible system supports majors, minors, electives and interdisciplinary study.',
                'overview_2' => 'Internships, co-op, research assistantships, capstone projects and industry collaboration help students connect theory with practice while building networks in one of the world’s largest economies.',
                'stats' => [['4,000+', 'Universities nationwide'], ['Ivy League', 'Plus public and private institutions'], ['1–3 years', 'OPT and eligible STEM options'], ['200+', 'Nationalities represented']],
                'benefits' => [
                    ['World-class universities', 'Choose globally recognised institutions across every type of campus and region.'],
                    ['Flexible academics', 'Combine majors, minors, electives and interdisciplinary specialisations.'],
                    ['Research leadership', 'Access advanced laboratories, innovation hubs and funded research ecosystems.'],
                    ['Practical experience', 'Build employability through internships, assistantships, co-op and capstones.'],
                    ['Global network', 'Join diverse campuses and alumni communities spanning the world.'],
                    ['Career opportunity', 'Eligible graduates can gain experience through OPT and qualifying STEM extensions.'],
                ],
                'facts' => [['Population', '340 million'], ['International students', '1.1+ million'], ['Living costs', 'USD 12K–25K / year']],
                'visa_title' => 'F-1 Student Visa', 'visa_copy' => 'After admission, receive Form I-20, pay the SEVIS fee, complete DS-160, demonstrate finances and prepare carefully for the visa interview.',
                'costs' => [['Undergraduate tuition', 'USD 20,000–50,000 / year'], ['Postgraduate tuition', 'USD 25,000–60,000 / year'], ['Living and accommodation', 'USD 12,000–25,000 / year'], ['SEVIS + visa fee', 'About USD 535']],
                'careers' => ['Artificial Intelligence', 'Computer Science', 'Data Science', 'Healthcare', 'Advanced Manufacturing', 'Finance & FinTech', 'Cybersecurity', 'Public Policy'],
                'intakes' => [['August / September', 'Fall—the primary intake with maximum program choice.'], ['January', 'Spring—a strong secondary intake.'], ['May / June', 'Summer—a limited intake for selected programs.']],
            ],
            'canada' => [
                'name' => 'Canada', 'flag' => 'ca.png', 'card' => 'assets/transglobe/destinations/canada.jpg',
                'tagline' => 'Research excellence, co-op education and a welcoming multicultural study environment.',
                'overview' => 'Canada offers research-intensive universities, polytechnics and public colleges across Toronto, Vancouver, Montreal, Calgary, Edmonton, Ottawa and Halifax. Its education system emphasises innovation and career readiness.',
                'overview_2' => 'Co-operative education, internships, field placements and applied research give students real-world experience. Eligible programs may support post-graduation work opportunities under current rules.',
                'stats' => [['100+', 'Universities and colleges'], ['U15', 'Research-intensive network'], ['Up to 3 years', 'Eligible PGWP'], ['250+', 'Cultural origins represented']],
                'benefits' => [
                    ['High-quality education', 'Learn at respected universities, colleges and polytechnic institutions.'],
                    ['Co-op experience', 'Apply learning through paid co-op, placements and industry projects.'],
                    ['Research and innovation', 'Access laboratories, research centres and collaborative innovation hubs.'],
                    ['Multicultural society', 'Join welcoming communities with extensive international student support.'],
                    ['Career-focused programs', 'Choose practical credentials aligned with technology, health, business and trades.'],
                    ['Graduate opportunities', 'Eligible graduates may gain Canadian work experience through PGWP.'],
                ],
                'facts' => [['Institutions', '100+'], ['Graduate work', 'Up to 3 years'], ['Major hubs', 'Toronto, Vancouver, Montreal']],
                'visa_title' => 'Canadian Study Permit', 'visa_copy' => 'You normally need a letter of acceptance, proof of funds, a provincial attestation where applicable, identity documents and evidence that you meet temporary-resident requirements.',
                'costs' => [['Undergraduate tuition', 'CAD 18,000–40,000 / year'], ['Postgraduate tuition', 'CAD 18,000–35,000 / year'], ['Living and accommodation', 'CAD 15,000–24,000 / year'], ['Study permit and biometrics', 'Budget separately']],
                'careers' => ['Software & Technology', 'Business Analytics', 'Engineering', 'Healthcare', 'Finance', 'Skilled Trades', 'Supply Chain', 'Clean Technology'],
                'intakes' => [['September', 'Fall—the largest and most popular intake.'], ['January', 'Winter—a major secondary intake.'], ['May', 'Summer—limited programs and institutions.']],
            ],
            'singapore' => [
                'name' => 'Singapore', 'flag' => 'sg.png', 'card' => 'assets/transglobe/destinations/singapore.jpg',
                'tagline' => 'Asia’s global education and business hub with English-medium, industry-focused programs.',
                'overview' => 'Singapore is a safe, modern and globally connected centre for education, finance, technology and innovation. Autonomous universities, specialised institutes and international providers offer respected qualifications in an English-medium environment.',
                'overview_2' => 'Strong links between institutions and multinational companies support internships, applied projects and global networking. Efficient transport, advanced infrastructure and a multicultural society make student life convenient.',
                'stats' => [['40+', 'Recognised institutions'], ['30+', 'Universities'], ['Up to 12 months', 'Eligible job-search stay'], ['120+', 'Nationalities represented']],
                'benefits' => [
                    ['Academic excellence', 'Study at globally ranked and specialised institutions.'],
                    ['Gateway to Asia', 'Build networks in a leading regional business and innovation centre.'],
                    ['English-medium learning', 'Study and collaborate comfortably in an international academic environment.'],
                    ['Industry connections', 'Gain exposure through internships, case studies and employer projects.'],
                    ['Safe and efficient', 'Benefit from excellent public transport, healthcare and modern infrastructure.'],
                    ['Shorter pathways', 'Selected programs offer efficient routes to internationally recognised credentials.'],
                ],
                'facts' => [['Population', '6 million'], ['International students', '90,000+'], ['Living costs', 'SGD 15K–25K / year']],
                'visa_title' => 'Singapore Student’s Pass', 'visa_copy' => 'After accepting an eligible offer, apply through the ICA SOLAR system with admission, financial and identification documents, then complete any medical and issuance formalities.',
                'costs' => [['Undergraduate tuition', 'SGD 20,000–45,000 / year'], ['Postgraduate tuition', 'SGD 25,000–55,000 / year'], ['Living and accommodation', 'SGD 15,000–25,000 / year'], ['Student’s Pass', 'Fees vary by issuance']],
                'careers' => ['Finance & FinTech', 'Artificial Intelligence', 'Data Analytics', 'Cybersecurity', 'Logistics', 'Engineering', 'Hospitality', 'Biomedical Sciences'],
                'intakes' => [['August', 'Primary intake at major universities.'], ['January', 'Secondary intake for selected institutions.'], ['Multiple intakes', 'Private institutions may offer additional starts.']],
            ],
            'dubai' => [
                'name' => 'Dubai & UAE', 'flag' => 'ae.png', 'card' => 'assets/transglobe/destinations/dubai-card.jpg', 'hero_position' => 'center 35%',
                'tagline' => 'Globally recognised branch-campus degrees in a fast-growing international business hub.',
                'overview' => 'Dubai hosts international branch campuses from leading UK, Australian, European and UAE institutions. Students can earn globally recognised qualifications in a safe, multicultural and business-focused city.',
                'overview_2' => 'Competitive fees, proximity to India, modern infrastructure and strong industry links make Dubai attractive for students seeking international exposure and practical career opportunity.',
                'stats' => [['International', 'University branch campuses'], ['230,000+', 'International students in UAE'], ['Close to India', 'Short direct flights'], ['Global hub', 'Business and innovation']],
                'benefits' => [
                    ['Recognised degrees', 'Earn UK, Australian, European or UAE qualifications from international campuses.'],
                    ['Competitive cost', 'Access international education with potentially lower total costs than traditional destinations.'],
                    ['Simple visa process', 'Universities commonly support the student residence-visa process.'],
                    ['Close to home', 'Direct flights keep students connected with family in India.'],
                    ['Safe and multicultural', 'Live in a modern city with residents and employers from around the world.'],
                    ['Career exposure', 'Connect with industries spanning technology, aviation, finance, tourism and logistics.'],
                ],
                'facts' => [['UAE population', '11.57 million'], ['Living costs', 'AED 35K–60K / year'], ['Climate', '18°C–42°C']],
                'visa_title' => 'UAE Student Residence Visa', 'visa_copy' => 'After accepting your offer and paying the required deposit, the sponsoring institution generally coordinates the student residence-visa process and supporting documents.',
                'costs' => [['Undergraduate tuition', 'AED 35,000–75,000 / year'], ['Postgraduate tuition', 'AED 45,000–100,000 / year'], ['Living and accommodation', 'AED 35,000–60,000 / year'], ['Visa and insurance', 'Varies by institution']],
                'careers' => ['Business & Management', 'Aviation', 'Artificial Intelligence', 'Hospitality & Tourism', 'Finance', 'Logistics', 'Construction Management', 'Digital Marketing'],
                'intakes' => [['September', 'Primary intake with the widest choice.'], ['January', 'Major secondary intake.'], ['May / June', 'Selected institutions and programs.']],
            ],
            'malaysia' => [
                'name' => 'Malaysia', 'flag' => 'my.png', 'card' => 'assets/transglobe/destinations/malaysia.webp',
                'tagline' => 'Affordable international education with leading Asian and branch-campus university options.',
                'overview' => 'Malaysia combines public universities, private institutions and international branch campuses in a multicultural, English-friendly environment. It offers a cost-effective route to internationally recognised qualifications.',
                'overview_2' => 'Modern campuses, low living costs and strong regional connections support a comfortable student experience. Programs often include internships, industrial training and practical industry projects.',
                'stats' => [['35 million', 'Population'], ['Branch campuses', 'UK and Australian universities'], ['Affordable', 'Tuition and living'], ['Multiple', 'Annual intakes']],
                'benefits' => [
                    ['Affordable study', 'Tuition and living costs are among the most accessible across major destinations.'],
                    ['International campuses', 'Study with recognised UK, Australian and local university providers.'],
                    ['English-friendly', 'Many programs are taught in English in a multicultural environment.'],
                    ['Modern student cities', 'Kuala Lumpur and other hubs offer excellent infrastructure and connectivity.'],
                    ['Practical learning', 'Industrial training and employer projects strengthen graduate readiness.'],
                    ['Regional gateway', 'Build networks across fast-growing Southeast Asian economies.'],
                ],
                'facts' => [['Population', '35 million'], ['Living costs', 'MYR 18K–30K / year'], ['Climate', '23°C–33°C']],
                'visa_title' => 'Malaysia Student Pass', 'visa_copy' => 'Applications proceed through Education Malaysia Global Services (EMGS), followed by the Electronic Visa Approval Letter, entry formalities, medical screening and Student Pass endorsement.',
                'costs' => [['Diploma tuition', 'MYR 10,000–25,000 / year'], ['Bachelor’s tuition', 'MYR 15,000–50,000 / year'], ['Postgraduate tuition', 'MYR 20,000–60,000 / year'], ['Living and accommodation', 'MYR 18,000–30,000 / year']],
                'careers' => ['Business & Management', 'Software Development', 'Artificial Intelligence', 'Engineering', 'Accounting & Finance', 'Hospitality', 'Life Sciences', 'Logistics'],
                'intakes' => [['January / February', 'Popular start for diploma, bachelor’s and master’s programs.'], ['July', 'Widely available across institutions.'], ['September / October', 'Major intake with broad course availability.']],
            ],
            'switzerland' => [
                'name' => 'Switzerland', 'flag' => 'ch.png', 'card' => 'assets/transglobe/destinations/switzerland.webp',
                'tagline' => 'Research, innovation, hospitality and quality of life at the heart of Europe.',
                'overview' => 'Switzerland is known for outstanding universities, federal institutes, applied-science institutions and specialist hospitality schools. Zurich, Geneva, Lausanne, Basel and Bern offer safe, highly international environments.',
                'overview_2' => 'Strong links to finance, pharmaceuticals, engineering, tourism and global organisations give students access to practical projects, research and multinational career networks.',
                'stats' => [['40+', 'Recognised institutions'], ['150+', 'Nationalities represented'], ['Up to 6 months', 'Graduate job-search period'], ['Global leader', 'Research and innovation']],
                'benefits' => [
                    ['World-class institutions', 'Study at leading universities, federal institutes and specialist schools.'],
                    ['Innovation ecosystem', 'Access advanced research, laboratories and industry collaboration.'],
                    ['Hospitality leadership', 'Train in one of the world’s strongest hospitality and tourism education markets.'],
                    ['Multilingual experience', 'Live among German, French, Italian and international communities.'],
                    ['Safe and connected', 'Benefit from excellent transport, healthcare and central European access.'],
                    ['Premium careers', 'Connect with finance, pharma, engineering and multinational employers.'],
                ],
                'facts' => [['Population', '9 million'], ['International students', '75,000+'], ['Living costs', 'CHF 18K–30K / year']],
                'visa_title' => 'Swiss National D Student Visa', 'visa_copy' => 'Secure admission, demonstrate finances and accommodation, arrange health insurance, submit the National D visa application and register with cantonal authorities after arrival.',
                'costs' => [['Undergraduate tuition', 'CHF 1,000–25,000 / year'], ['Postgraduate tuition', 'CHF 1,500–35,000 / year'], ['Living and accommodation', 'CHF 18,000–30,000 / year'], ['National D visa', 'About CHF 88–100']],
                'careers' => ['Mechanical Engineering', 'Robotics & AI', 'Data Science', 'Cybersecurity', 'Banking & FinTech', 'Hospitality', 'Pharmaceuticals', 'Biotechnology'],
                'intakes' => [['August / September', 'Primary fall intake with maximum program choice.'], ['February', 'Secondary spring intake for selected programs.']],
            ],
        ];
    }

    private static function universities(): array
    {
        return [
            'australia' => [
                'Australian National University (ANU)',
                'University of Adelaide',
                'University of Melbourne',
                'University of New South Wales (UNSW Sydney)',
                'University of Queensland (UQ)',
                'University of Sydney',
                'University of Western Australia (UWA)',
                'Monash University',
            ],
            'new-zealand' => [
                'University of Auckland',
                'Auckland University of Technology (AUT)',
                'Massey University',
                'University of Waikato',
                'Victoria University of Wellington',
                'University of Canterbury',
                'University of Otago',
                'Lincoln University',
            ],
            'uk' => [
                'University of Oxford',
                'University of Cambridge',
                'Imperial College London',
                'University College London',
                'University of Edinburgh',
                'Manchester University',
                "King's College London",
                'LSE University',
            ],
            'ireland' => [
                'Trinity College Dublin',
                'University College Dublin',
                'University College Cork',
                'University of Galway',
                'Dublin City University',
                'University of Limerick',
                'Maynooth University',
            ],
            'germany' => [
                'Technical University of Munich',
                'Karlsruhe Institute of Technology (KIT)',
                'Technical University of Berlin',
                'Technical University of Dresden',
                'Technical University of Darmstadt',
                'University of Stuttgart',
                'Leibniz University Hannover',
                'Technical University of Braunschweig',
                'RWTH Aachen University',
            ],
            'europe' => [
                'University of Bologna (Italy)',
                'WU Vienna (Austria)',
                'Sorbonne University (France)',
                'TU Leoben (Austria)',
                'Autonomous University of Madrid (Spain)',
            ],
            'usa' => [
                'Arizona State University',
                'Baltimore Maryland University',
                'Columbia University',
                'Cornell University',
                'DEPAUL UNIVERSITY',
                'Drexel University',
                'Duquesne University',
                'East Tennessee State University',
                'Educo Global',
                'Embry Riddle Aeronautical University',
            ],
            'canada' => [
                'Brock University',
                'Cape Breton University',
                'Concordia University',
                'Durham College',
                'Fraser International College (FIC)',
                'International College of Manitoba (ICM)',
                'McGill University',
                'McMaster University',
                'Ontario Tech University',
                'Oulton College',
            ],
            'singapore' => [
                'SP Jain School Of Global Management In Singapore',
                'Curtin University, Singapore',
                'PSB Academy',
                'Murdoch University, Singapore',
                'James Cook University, Singapore',
                'First Media Design School (FDMS)',
                'Raffles Design Institute, Singapore',
                'Singapore Institute Of Materials Management (SIMM)',
                'Management Development Institute Of Singapore (MDIS)',
            ],
            'dubai' => [
                'Arden University Dubai',
                'SAE Institute Dubai',
                'HTMi Dubai',
                'University of Europe for Applied Sciences Dubai',
                'Royal Roads University (Canada) – Dubai Partnerships',
                'University of Wollongong in Dubai',
                'Middlesex University Dubai',
                'Heriot-Watt University Dubai',
                'Curtin University Dubai',
                'University of Birmingham Dubai',
                'Murdoch University Dubai',
                'SP Jain School of Global Management',
                'Hult International Business School',
                'Ajman University',
                'Emirates Aviation University',
                'Amity University Dubai',
                'Jaipur National University',
                'Manipal Academy of Higher Education Dubai',
                'University of Hertfordshire Dubai',
            ],
            'malaysia' => [
                'Monash University - Malaysia',
                'Curtin University Malaysia',
                'Heriot-Watt University - Malaysia',
                'Lincoln University - Malaysia',
                'Western Sydney University - Indonesia',
                "Taylor's University",
            ],
            'switzerland' => [
                'Culinary Arts Academy Switzerland',
                'HIM Business School',
                'Les Roches',
                'Glion Institute of Higher Education',
                'HTMi Switzerland',
                'EU Business School',
                'SRH University',
                'Swiss Hotel Management School (SHMS)',
                'César Ritz Colleges Switzerland',
            ],
        ];
    }
}
