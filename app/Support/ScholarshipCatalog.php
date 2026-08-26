<?php

namespace App\Support;

class ScholarshipCatalog
{
    public static function find(string $slug): ?array
    {
        $scholarship = self::destinations()[$slug] ?? null;

        return $scholarship ? array_merge(self::defaults($slug), $scholarship) : null;
    }

    public static function all(): array
    {
        return array_map(fn (string $slug) => self::find($slug), array_keys(self::destinations()));
    }

    private static function defaults(string $slug): array
    {
        return [
            'slug' => $slug,
            'image' => 'assets/transglobe/destinations/australia.jpg',
            'image_alt' => 'Students preparing to study abroad',
            'intro' => 'Scholarship availability and value can vary by course, university, intake and student profile. Our team helps you understand the options that fit your plan.',
            'steps' => [
                ['Review your profile', 'We review your academics, course, destination, intake and achievements.'],
                ['Match funding options', 'Identify university, government and external scholarship opportunities.'],
                ['Prepare a strong application', 'Organise evidence, written statements and timelines before the deadline.'],
            ],
            'faqs' => [
                ['Can international students apply for scholarships?', 'Yes. Many universities and external organisations offer awards for international students, although eligibility and funding vary.'],
                ['Are scholarships fully funded?', 'Some are fully funded, but many provide a partial tuition reduction. Your counsellor can help you prioritise realistic opportunities.'],
                ['When should I apply?', 'Start early. Scholarship deadlines can be separate from university application deadlines and may close well before an intake begins.'],
            ],
        ];
    }

    private static function destinations(): array
    {
        return [
            'usa' => [
                'name' => 'USA', 'flag' => 'us.png', 'image' => 'assets/transglobe/destinations/usa.jpg', 'image_alt' => 'Students studying in the USA',
                'tagline' => 'Explore merit awards, need-based aid, graduate assistantships and university scholarships for the USA.',
                'intro' => 'The USA offers a broad mix of university-based, government and external funding. Strong academics, leadership, research interests and distinctive extracurricular achievements can improve your scholarship profile.',
                'awards' => [
                    ['Fulbright Foreign Student Program', 'A prestigious funding route for eligible international Master’s and PhD students.'],
                    ['Merit-based university scholarships', 'Academic awards offered automatically or through competitive university applications.'],
                    ['Graduate assistantships', 'Teaching, research and graduate assistantships can support tuition and living costs for eligible postgraduate students.'],
                    ['Talent-based awards', 'Selected institutions recognise exceptional achievement in sport, music, arts and other areas.'],
                ],
            ],
            'uk' => [
                'name' => 'United Kingdom', 'flag' => 'gb.png', 'image' => 'assets/transglobe/destinations/uk.jpg', 'image_alt' => 'Student destination in the United Kingdom',
                'tagline' => 'Find university discounts, early-bird awards and prestigious UK scholarship opportunities.',
                'intro' => 'UK institutions commonly offer international academic scholarships, bursaries and course discounts. Some award pathways are automatic, while others require a separate application or an early commitment.',
                'awards' => [
                    ['International academic scholarships', 'Many universities offer awards or bursaries that contribute towards international tuition fees.'],
                    ['Early-bird awards', 'Some providers recognise students who confirm their place early for a chosen academic programme.'],
                    ['Marshall Scholarship', 'A competitive UK Government-funded route for eligible postgraduate study.'],
                    ['Commonwealth Scholarships', 'Prestigious postgraduate scholarships managed through the Commonwealth Scholarship Commission.'],
                ],
            ],
            'australia' => [
                'name' => 'Australia', 'flag' => 'au.png', 'image' => 'assets/transglobe/destinations/australia.jpg', 'image_alt' => 'University campus in Australia',
                'tagline' => 'Discover tuition-fee reductions and merit awards at leading Australian universities.',
                'intro' => 'Australian universities offer a range of international scholarships. These commonly recognise academic merit and may be available as a percentage tuition waiver or a fixed amount across one or more study years.',
                'awards' => [
                    ['ANU Chancellor’s International Scholarship', 'Eligible students may receive a 25% or 50% tuition-fee waiver.'],
                    ['UNSW International Student Award', 'An international tuition-fee contribution for selected eligible students.'],
                    ['Monash International Study Grants', 'Selected students can receive a fixed annual study grant.'],
                    ['UQ International Excellence Scholarship', 'A tuition-fee reduction for eligible high-achieving applicants.'],
                ],
            ],
            'dubai' => [
                'name' => 'Dubai / UAE', 'flag' => 'ae.png', 'image' => 'assets/transglobe/destinations/dubai.jpg', 'image_alt' => 'Student life in Dubai',
                'tagline' => 'Compare merit awards, early-enrolment discounts and university tuition waivers in Dubai.',
                'intro' => 'Dubai universities offer a practical mix of merit scholarships, early-enrolment awards and programme-specific discounts for international students. Award values and conditions differ by intake and institution.',
                'awards' => [
                    ['Canadian University Dubai merit scholarships', 'Academic scholarship awards that can contribute towards tuition fees.'],
                    ['Middlesex University Dubai awards', 'Merit and early-enrolment award routes for eligible applicants.'],
                    ['University of Birmingham Dubai scholarships', 'Merit-based scholarship opportunities for selected international students.'],
                    ['University of Wollongong in Dubai awards', 'Academic merit and early-bird scholarship pathways with varying tuition reductions.'],
                ],
            ],
            'new-zealand' => [
                'name' => 'New Zealand', 'flag' => 'nz.png', 'image' => 'assets/transglobe/destinations/new-zealand.jpg', 'image_alt' => 'New Zealand student destination',
                'tagline' => 'Explore international excellence scholarships and high-achiever awards in New Zealand.',
                'intro' => 'New Zealand universities recognise international student achievement through entry scholarships and excellence awards. Eligibility is commonly tied to academic results, programme choice and the timing of your application.',
                'awards' => [
                    ['University of Auckland India High Achievers Scholarship', 'A scholarship pathway for eligible Indian students beginning undergraduate or postgraduate study.'],
                    ['International Student Excellence Scholarship', 'A contribution toward tuition for selected new international students.'],
                    ['Victoria University of Wellington Tongarewa Scholarship', 'A merit-based award for eligible undergraduate and postgraduate applicants.'],
                    ['University of Waikato International Excellence Scholarship', 'An award designed to recognise eligible international student achievement.'],
                ],
            ],
            'germany' => [
                'name' => 'Germany', 'flag' => 'de.png', 'image' => 'assets/transglobe/destinations/germany.webp', 'image_alt' => 'International study option in Germany',
                'tagline' => 'Understand private-university awards, merit support and programme-specific funding in Germany.',
                'intro' => 'Germany combines comparatively accessible study options with selected merit, need-based and programme-specific support. Scholarship conditions can vary significantly between public and private institutions.',
                'awards' => [
                    ['Women in Tech scholarships', 'Selected technology-focused programmes offer scholarships for eligible women applicants.'],
                    ['Merit and need-based awards', 'Some private universities provide tuition reductions for strong academic profiles or demonstrated need.'],
                    ['Digital and programme scholarships', 'Selected digital, technology and creative programmes provide targeted tuition support.'],
                    ['Mobility and excellence funding', 'University-supported and DAAD-linked routes may support specific study or mobility experiences.'],
                ],
            ],
            'europe' => [
                'name' => 'Europe', 'flag' => 'eu.png', 'image' => 'assets/transglobe/destinations/europe.png', 'image_alt' => 'European university destination',
                'tagline' => 'Explore government, regional, Erasmus+ and university scholarships across Europe.',
                'intro' => 'Funding across Europe depends on your chosen country, university and degree level. Government, regional and institution-specific awards can contribute to tuition and, in selected cases, living costs.',
                'awards' => [
                    ['Spain government and university scholarships', 'Government, Erasmus+ and university awards can support tuition and living costs for eligible students.'],
                    ['Italy regional and merit scholarships', 'Regional, government and university scholarship routes support selected international students.'],
                    ['France excellence programmes', 'Competitive awards such as Eiffel Excellence and Erasmus+ can support eligible postgraduate or mobility applicants.'],
                    ['Erasmus+ funding', 'Mobility and joint-degree funding opportunities are available for selected eligible programmes.'],
                ],
            ],
            'canada' => [
                'name' => 'Canada', 'flag' => 'ca.png', 'image' => 'assets/transglobe/destinations/canada.jpg', 'image_alt' => 'Students preparing for Canada',
                'tagline' => 'Compare entrance, merit, leadership, research and external funding opportunities in Canada.',
                'intro' => 'Canadian universities offer international scholarships based on academic merit, leadership, research, extracurricular achievement and community involvement. Many institutions consider eligible applicants automatically for entry awards.',
                'awards' => [
                    ['Merit-based scholarships', 'Awards that recognise strong academic performance and educational achievement.'],
                    ['Entrance scholarships', 'Funding granted at admission based on your academic record and overall student profile.'],
                    ['Leadership and community awards', 'Scholarships that recognise leadership, volunteering and extracurricular involvement.'],
                    ['Research scholarships', 'Higher-value support may be available to eligible Master’s and PhD students involved in research and innovation.'],
                ],
            ],
            'ireland' => [
                'name' => 'Ireland', 'flag' => 'ie.png', 'image' => 'assets/transglobe/destinations/ireland.jpg', 'image_alt' => 'International student destination in Ireland',
                'tagline' => 'Find academic excellence, international student and course-specific scholarships in Ireland.',
                'intro' => 'Irish universities offer scholarship opportunities for international undergraduate and postgraduate students. Awards can be merit-based, course-specific or linked to areas such as sport, STEM and academic excellence.',
                'awards' => [
                    ['Merit-based scholarships', 'Awards that recognise academic performance and a strong overall student profile.'],
                    ['International student scholarships', 'University scholarship routes designed for eligible global applicants.'],
                    ['Women in STEM scholarships', 'Selected institutions offer targeted awards for eligible STEM applicants.'],
                    ['Course-specific scholarships', 'Funding linked to particular disciplines, degree levels or university schools.'],
                ],
            ],
        ];
    }
}
