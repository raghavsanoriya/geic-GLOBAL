<?php

namespace App\Support;

/**
 * Content for the public planning tools linked from the site footer.
 *
 * The values are intentionally kept in a small catalog so the pages remain
 * easy to maintain and can later be promoted to CMS-managed fields without
 * changing their public URLs.
 */
class StudyToolsCatalog
{
    public static function all(): array
    {
        return [
            'compare-destinations' => [
                'slug' => 'compare-destinations',
                'cms_key' => 'tools.compare-destinations',
                'eyebrow' => 'Plan with confidence',
                'title' => 'Compare destinations before you choose your next chapter.',
                'copy' => 'Put tuition, living costs and the shape of each study-abroad experience side by side. Use this first view to shortlist countries, then let our counsellors validate the details for your course and intake.',
                'hero_image' => 'assets/transglobe/destinations/australia-detail-hero.jpg',
                'hero_foreground' => 'assets/transglobe/tools/compare-counsellor.png',
                'hero_note' => 'Planning estimates are indicative. University fees, exchange rates, visa rules and work rights vary by course, provider and year.',
                'gallery' => [
                    ['image' => 'assets/transglobe/destinations/australia/campus.webp', 'alt' => 'Australian university campus', 'label' => 'Australia'],
                    ['image' => 'assets/transglobe/destinations/canada/students.webp', 'alt' => 'Students in Canada', 'label' => 'Canada'],
                    ['image' => 'assets/transglobe/destinations/uk/city.webp', 'alt' => 'City life in the United Kingdom', 'label' => 'United Kingdom'],
                    ['image' => 'assets/transglobe/destinations/usa/students.webp', 'alt' => 'Students in the United States', 'label' => 'USA'],
                    ['image' => 'assets/transglobe/destinations/germany.webp', 'alt' => 'Study destination in Germany', 'label' => 'Germany'],
                ],
                'stats' => [
                    ['value' => '98.7%', 'label' => 'visa success rate'],
                    ['value' => '32+', 'label' => 'years of experience'],
                    ['value' => '70,250+', 'label' => 'students placed worldwide'],
                ],
                'rows' => [
                    ['country' => 'Australia', 'total' => '₹40L', 'tuition' => '₹25L', 'living' => '₹12L', 'misc' => '₹24L', 'fit' => 'Research-led learning'],
                    ['country' => 'Canada', 'total' => '₹35L', 'tuition' => '₹22L', 'living' => '₹10L', 'misc' => '₹22L', 'fit' => 'Study, work and grow'],
                    ['country' => 'USA', 'total' => '₹60L', 'tuition' => '₹45L', 'living' => '₹15L', 'misc' => '₹25L', 'fit' => 'Limitless academic choice'],
                    ['country' => 'United Kingdom', 'total' => '₹45L', 'tuition' => '₹32L', 'living' => '₹9L', 'misc' => '₹28L', 'fit' => 'Prestigious education'],
                    ['country' => 'New Zealand', 'total' => '₹38L', 'tuition' => '₹24L', 'living' => '₹8L', 'misc' => '₹21L', 'fit' => 'Quality with balance'],
                    ['country' => 'Germany', 'total' => '₹18L', 'tuition' => '₹5L', 'living' => '₹9L', 'misc' => '₹26L', 'fit' => 'Engineering excellence'],
                    ['country' => 'Singapore', 'total' => '₹50L', 'tuition' => '₹35L', 'living' => '₹10L', 'misc' => '₹22L', 'fit' => 'Asia’s education hub'],
                    ['country' => 'Dubai & UAE', 'total' => '₹30L', 'tuition' => '₹20L', 'living' => '₹15L', 'misc' => '₹20L', 'fit' => 'Global degrees, global city'],
                    ['country' => 'Europe', 'total' => '₹30L', 'tuition' => '₹20L', 'living' => '₹15L', 'misc' => '₹20L', 'fit' => 'Many cultures, one journey'],
                    ['country' => 'Ireland', 'total' => '₹30L', 'tuition' => '₹20L', 'living' => '₹15L', 'misc' => '₹20L', 'fit' => 'Europe’s technology hub'],
                ],
                'factors' => [
                    ['title' => 'Course and career fit', 'copy' => 'A country is only a good choice when its universities, teaching style and graduate outcomes match your target career.'],
                    ['title' => 'Budget and funding', 'copy' => 'Plan for tuition, accommodation, insurance, visa costs, travel and a buffer—not only the advertised course fee.'],
                    ['title' => 'Intakes and timeline', 'copy' => 'Starting 12–18 months early gives you room for tests, applications, scholarships and visa preparation.'],
                    ['title' => 'After-study pathway', 'copy' => 'Work rights and post-study options depend on current rules, eligibility, course level and the destination you choose.'],
                ],
            ],
            'emi-calculator' => [
                'slug' => 'emi-calculator',
                'cms_key' => 'tools.emi-calculator',
                'eyebrow' => 'Fund your future',
                'title' => 'Know your monthly education-loan commitment before you apply.',
                'copy' => 'Adjust the loan amount, interest rate and repayment period to get an indicative monthly EMI, total interest and total repayment. Use the result to compare funding options with a counsellor.',
                'hero_image' => 'assets/transglobe/services/services-team.avif',
                'hero_foreground' => 'assets/transglobe/tools/emi-calculator-student.png',
                'gallery' => [
                    ['image' => 'assets/transglobe/services/services-team.avif', 'alt' => 'Education advisors discussing study plans', 'label' => 'Talk to an advisor'],
                    ['image' => 'assets/transglobe/services/services-journey.avif', 'alt' => 'Students planning their education journey', 'label' => 'Plan your journey'],
                    ['image' => 'assets/transglobe/destinations/australia/students.webp', 'alt' => 'Students studying abroad', 'label' => 'Study abroad'],
                ],
                'stats' => [
                    ['value' => '₹', 'label' => 'Indian rupee inputs'],
                    ['value' => '3', 'label' => 'simple assumptions'],
                    ['value' => 'Live', 'label' => 'instant estimate'],
                ],
                'tips' => [
                    ['title' => 'Keep a buffer', 'copy' => 'Borrow only what your offer, living budget and realistic contingency require.'],
                    ['title' => 'Compare the full cost', 'copy' => 'Look beyond EMI: check processing fees, moratorium, insurance, margin money and prepayment terms.'],
                    ['title' => 'Ask about moratorium', 'copy' => 'Many student loans begin repayment after study plus a grace period; lender terms differ.'],
                ],
            ],
            'education-loans' => [
                'slug' => 'education-loans',
                'cms_key' => 'tools.education-loans',
                'eyebrow' => 'Funding guidance',
                'title' => 'No collateral confusion. Just a clearer route to your education loan.',
                'copy' => 'Get practical help comparing lenders, checking eligibility, preparing documents and timing your application around your university offer and fee deadlines.',
                'hero_image' => 'assets/transglobe/about/student-guidance-session-2023.jpg',
                'hero_foreground' => 'assets/transglobe/tools/education-loan-advisor.png',
                'hero_note' => 'Loan approval, interest rates, collateral and disbursal depend on the lender, course, destination and your complete financial profile.',
                'gallery' => [
                    ['image' => 'assets/transglobe/about/student-guidance-session-2023.jpg', 'alt' => 'Student guidance session', 'label' => 'Guided applications'],
                    ['image' => 'assets/transglobe/services/services-campus.avif', 'alt' => 'International university campus', 'label' => 'University costs'],
                    ['image' => 'assets/transglobe/destinations/canada/students.webp', 'alt' => 'Students preparing together', 'label' => 'Ready your file'],
                ],
                'stats' => [
                    ['value' => '20+', 'label' => 'lender relationships'],
                    ['value' => '4', 'label' => 'loan stages'],
                    ['value' => '1:1', 'label' => 'advisor support'],
                ],
                'lender_types' => [
                    ['title' => 'Private banks', 'copy' => 'Faster processing and digital tracking can suit students who need quick approvals and smooth disbursals.'],
                    ['title' => 'NBFCs', 'copy' => 'Flexible eligibility and co-applicant options may help with higher-ticket loans; approval terms vary.'],
                    ['title' => 'International lenders', 'copy' => 'Some lenders assess course, university and future earning potential for selected programs and countries.'],
                    ['title' => 'Nationalised banks', 'copy' => 'Structured schemes and wide acceptance can suit a stable, long-term repayment plan.'],
                ],
                'checklists' => [
                    ['title' => 'University file', 'items' => ['Offer or admission letter and fee structure', 'Course details, invoice or cost estimate', 'Scholarship letter, if applicable']],
                    ['title' => 'Student file', 'items' => ['KYC, academic records and exam scores', 'Passport for overseas study', 'Co-applicant details and completed forms']],
                    ['title' => 'Parent or sponsor file', 'items' => ['KYC, income proof and bank statements', 'Existing loan details, if applicable', 'Collateral documents when required by policy']],
                ],
                'steps' => [
                    ['number' => '01', 'title' => 'Share your plan', 'copy' => 'Tell us your destination, intake, course, budget, co-applicant details and collateral preference.'],
                    ['number' => '02', 'title' => 'Check eligibility', 'copy' => 'We map your profile to a best-fit lender category and loan structure.'],
                    ['number' => '03', 'title' => 'Build the application', 'copy' => 'Prepare clean financials, supporting proof and correctly completed forms.'],
                    ['number' => '04', 'title' => 'Sanction and disbursal', 'copy' => 'After sanction, funds are usually released in stages aligned to university deadlines.'],
                ],
                'factors' => ['Study level and program', 'Rate of interest', 'Margin money', 'Expenses covered', 'Moratorium and repayment terms'],
                'loan_notes' => [
                    ['title' => 'CIBIL and credit profile', 'copy' => 'A CIBIL score is a three-digit summary of credit history, usually from 300 to 900. A 700+ score is generally considered good, while lenders also assess the co-applicant and overall profile.'],
                    ['title' => 'Collateral clarity', 'copy' => 'Under India’s Model Education Loan Scheme, loans up to ₹7.5L may be available without tangible collateral with parents or guardians as joint borrowers. Above that, lender policy may require security.'],
                    ['title' => 'Partner guidance', 'copy' => 'Compare banks, NBFCs and international lenders with an advisor who can explain rates, margin money, eligible expenses, moratorium and staged disbursal.'],
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}
