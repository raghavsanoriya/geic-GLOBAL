<?php

namespace App\Support;

use App\Models\CmsPage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class CmsPageCatalog
{
    public static function all(): array
    {
        $pages = [
            self::page('home', 'Home page', 'Manage the header, hero, main content, conversion CTA and footer of the landing page.', [
                self::field('header_about_label', 'About link label', 'About GEIC Indore', 'text', 'Header & navigation'),
                self::field('header_contact_label', 'Contact link label', 'Contact', 'text', 'Header & navigation'),
                self::field('header_cta_label', 'Header button label', 'Speak to a Counsellor', 'text', 'Header & navigation'),
                self::field('header_nav_home', 'Home navigation label', 'Home', 'text', 'Header & navigation'),
                self::field('header_nav_destinations', 'Destinations navigation label', 'Destinations', 'text', 'Header & navigation'),
                self::field('header_nav_services', 'Services navigation label', 'Services', 'text', 'Header & navigation'),
                self::field('header_nav_events', 'Events navigation label', 'Events', 'text', 'Header & navigation'),
                self::field('header_nav_scholarships', 'Scholarships navigation label', 'Scholarships', 'text', 'Header & navigation'),
                self::field('header_nav_tests', 'Test-prep navigation label', 'Test Prep', 'text', 'Header & navigation'),
                self::field('hero_eyebrow', 'Hero eyebrow', 'Since 1992', 'text', 'Hero'),
                self::field('hero_trust_line', 'Hero trust line', 'Built on trust. Driven by student success.', 'text', 'Hero'),
                self::field('hero_title', 'Hero title', 'Shape Your Ambition Into International Success', 'text', 'Hero'),
                self::field('hero_copy', 'Hero description', 'At Trans Globe Indore, managed by GEIC, every student and every dream matters. From choosing the right course to securing your visa, our specialists guide you through every step of studying abroad.', 'textarea', 'Hero'),
                self::field('hero_image', 'Hero image URL', 'store/landing_builder/landing_13/371/hero_image_j5t.png', 'image', 'Hero'),
                self::field('hero_primary_cta_label', 'Primary button label', 'Book Free Counselling', 'text', 'Hero CTA'),
                self::field('hero_primary_cta_url', 'Primary button link', '#contact', 'text', 'Hero CTA'),
                self::field('hero_secondary_cta_label', 'Secondary button label', 'Explore Destinations', 'text', 'Hero CTA'),
                self::field('hero_secondary_cta_url', 'Secondary button link', '/destinations', 'text', 'Hero CTA'),
                self::field('hero_proof_title', 'Trust badge headline', '70,250+ students placed worldwide', 'text', 'Hero CTA'),
                self::field('hero_proof_copy', 'Trust badge description', 'Across leading universities in 10+ countries', 'text', 'Hero CTA'),
                self::field('stat_students_value', 'Students placed value', '70,250+', 'text', 'Highlights'),
                self::field('stat_students_label', 'Students placed label', 'Students Placed', 'text', 'Highlights'),
                self::field('stat_universities_value', 'Partner universities value', '800+', 'text', 'Highlights'),
                self::field('stat_universities_label', 'Partner universities label', 'Partner Universities', 'text', 'Highlights'),
                self::field('stat_visas_value', 'Visa success value', '98.7%', 'text', 'Highlights'),
                self::field('stat_visas_label', 'Visa success label', 'Visa Success Rate', 'text', 'Highlights'),
                self::field('stat_branches_value', 'Branches value', '16+', 'text', 'Highlights'),
                self::field('stat_branches_label', 'Branches label', 'Branches in India & Nepal', 'text', 'Highlights'),
                self::field('journey_eyebrow', 'Section eyebrow', 'How it works', 'text', 'Journey section'),
                self::field('journey_title', 'Section title', 'Four Simple Steps to Study Abroad', 'text', 'Journey section'),
                self::field('journey_copy', 'Section description', 'From your first conversation to the day you board your flight, Trans Globe Indore makes the process clear, personal and manageable.', 'textarea', 'Journey section'),
                self::field('journey_step_one_title', 'Step 1 title', 'Tell Us About Yourself', 'text', 'Journey section'),
                self::field('journey_step_one_copy', 'Step 1 description', 'Share your academic background, interests, preferred countries and career goals. This first conversation helps us understand where you want to go.', 'textarea', 'Journey section'),
                self::field('journey_step_two_title', 'Step 2 title', 'Meet a Specialist', 'text', 'Journey section'),
                self::field('journey_step_two_copy', 'Step 2 description', 'Work with a country-and-course specialist to shortlist universities, explore scholarships and build a plan that suits your profile and budget.', 'textarea', 'Journey section'),
                self::field('journey_step_three_title', 'Step 3 title', 'Apply With Confidence', 'text', 'Journey section'),
                self::field('journey_step_three_copy', 'Step 3 description', 'We help with your SOP, recommendations, transcripts, documents and application forms so every submission is complete and compelling.', 'textarea', 'Journey section'),
                self::field('journey_step_four_title', 'Step 4 title', 'Get Your Visa & Go', 'text', 'Journey section'),
                self::field('journey_step_four_copy', 'Step 4 description', 'Our visa team prepares your documents, finances and interview answers so you can travel knowing everything is in order.', 'textarea', 'Journey section'),
                self::field('services_eyebrow', 'Section eyebrow', 'Our services', 'text', 'Services section'),
                self::field('services_title', 'Section title', 'Everything You Need, Under One Roof', 'text', 'Services section'),
                self::field('services_copy', 'Section description', 'Trans Globe Indore supports your complete journey—from your first exam and university application to your arrival in a new country.', 'textarea', 'Services section'),
                self::field('services_cta_label', 'Section button label', 'Discuss Your Study Plan', 'text', 'Services section'),
                self::field('services_cta_url', 'Section button link', '#contact', 'text', 'Services section'),
                self::field('service_one_title', 'Service 1 title', 'Expert Counselling', 'text', 'Service cards'),
                self::field('service_one_copy', 'Service 1 description', 'Find the country, university and course that genuinely fit your goals—without being pushed toward a particular institution.', 'textarea', 'Service cards'),
                self::field('service_two_title', 'Service 2 title', 'University Admissions', 'text', 'Service cards'),
                self::field('service_two_copy', 'Service 2 description', 'Build a strong, error-free application with the right SOP, recommendation letters, documents and submission timeline.', 'textarea', 'Service cards'),
                self::field('service_three_title', 'Service 3 title', 'Scholarship Guidance', 'text', 'Service cards'),
                self::field('service_three_copy', 'Service 3 description', 'Discover scholarships and bursaries you may not know you qualify for. More than 2,000 Trans Globe students receive awards each year.', 'textarea', 'Service cards'),
                self::field('service_four_title', 'Service 4 title', 'IELTS, PTE, TOEFL & More', 'text', 'Service cards'),
                self::field('service_four_copy', 'Service 4 description', 'Prepare for IELTS, PTE, TOEFL, GRE, GMAT and SAT with expert trainers, realistic practice and proven score-improvement support.', 'textarea', 'Service cards'),
                self::field('service_five_title', 'Service 5 title', 'Visa Assistance', 'text', 'Service cards'),
                self::field('service_five_copy', 'Service 5 description', 'Avoid incomplete or inconsistent applications with detailed document checks, financial guidance and interview preparation.', 'textarea', 'Service cards'),
                self::field('service_six_title', 'Service 6 title', 'Pre & Post Departure Support', 'text', 'Service cards'),
                self::field('service_six_copy', 'Service 6 description', 'Get practical help with packing, banking, arrival and settling into your new university city—you are never doing this alone.', 'textarea', 'Service cards'),
                self::field('destinations_eyebrow', 'Section eyebrow', 'Where will you thrive?', 'text', 'Destinations section'),
                self::field('destinations_title', 'Section title', 'Explore the World’s Best Study Destinations', 'text', 'Destinations section'),
                self::field('destinations_copy', 'Section description', 'From research-led universities to affordable public education and strong post-study pathways, discover the destination that best fits your future.', 'textarea', 'Destinations section'),
                self::field('work_visa_badge', 'Section badge', 'Work visa pathways', 'text', 'Work visa pathways'),
                self::field('work_visa_title', 'Section title', 'Build Your Career Abroad', 'text', 'Work visa pathways'),
                self::field('work_visa_copy', 'Section description', 'Explore skilled-work opportunities with practical guidance on eligibility, documentation and the right pathway for your profile.', 'textarea', 'Work visa pathways'),
                self::field('work_visa_cta_label', 'Section button label', 'Free Profile Assessment', 'text', 'Work visa pathways'),
                self::field('work_visa_cta_url', 'Section button link', '#contact', 'text', 'Work visa pathways'),
                self::field('universities_kicker', 'Section badge', 'Global partner network', 'text', 'University network'),
                self::field('universities_title', 'Section title', '800+ University Tie-Ups Worldwide', 'text', 'University network'),
                self::field('universities_copy', 'Section description', 'Explore opportunities across a trusted global network of leading universities and find the institution that fits your ambitions.', 'textarea', 'University network'),
                self::field('universities_cta_label', 'Section button label', 'View All Universities', 'text', 'University network'),
                self::field('universities_cta_url', 'Section button link', '#contact', 'text', 'University network'),
                self::field('why_eyebrow', 'Section eyebrow', 'Why it matters', 'text', 'Why it matters'),
                self::field('why_title', 'Section title', 'Why the Right Consultants in India', 'text', 'Why it matters'),
                self::field('why_title_accent', 'Highlighted title', 'Make All the Difference', 'text', 'Why it matters'),
                self::field('why_scale_title', 'Opportunity card title', 'The Scale of the Opportunity', 'text', 'Why it matters'),
                self::field('why_scale_copy', 'Opportunity card text', "Every year, over 1.3 million Indian students study abroad—to build genuinely global careers, access research facilities that don't exist in India, and earn degrees that open doors everywhere. Visa policies, deadlines and scholarship requirements change every year, making experienced guidance genuinely important.", 'textarea', 'Why it matters'),
                self::field('why_consultant_title', 'Consultant card title', 'What Makes a Good Consultant', 'text', 'Why it matters'),
                self::field('why_consultant_copy', 'Consultant card text', 'Not all study-abroad consultants are the same. Trans Globe counsellors are specialists, not generalists—each focuses on specific countries and visa processes. With 800+ university affiliations, recommendations focus on what is right for the student.', 'textarea', 'Why it matters'),
                self::field('why_india_title', 'India coverage card title', 'Covering All of India', 'text', 'Why it matters'),
                self::field('why_india_copy', 'India coverage card text', 'Trans Globe has 16 offices across India plus an international office in Kathmandu, Nepal and online counselling. This reach helps our counsellors understand students from different cities, academic systems and backgrounds.', 'textarea', 'Why it matters'),
                self::field('why_countries_title', 'Countries card title', 'The Countries We Specialise In', 'text', 'Why it matters'),
                self::field('why_countries_copy', 'Countries card text', 'We help students study in Australia, Canada, the USA, the UK, Germany, New Zealand, Ireland, Singapore, Dubai & the UAE, and across Europe. Our specialists continuously track destination-specific requirements and post-study opportunities.', 'textarea', 'Why it matters'),
                self::field('why_track_title', 'Track record title', 'Our Track Record', 'text', 'Why it matters'),
                self::field('why_track_copy', 'Track record text', 'Since 1992, Trans Globe has placed more than 70,250 students at universities in over 10 countries. Its 98.7% visa success rate and strong scholarship outcomes are built on decades of experience and a clear understanding of what universities and visa officers require.', 'textarea', 'Why it matters'),
                self::field('blog_eyebrow', 'Section eyebrow', 'From the blog', 'text', 'Blog section'),
                self::field('blog_title', 'Section title', 'Fresh Study-Abroad Insights, Without the Jargon', 'text', 'Blog section'),
                self::field('blog_copy', 'Section description', 'Recent guidance from Trans Globe on university admissions, student visas and choosing the right destination for your future.', 'textarea', 'Blog section'),
                self::field('blog_cta_label', 'Section button label', 'Explore all articles', 'text', 'Blog section'),
                self::field('blog_cta_url', 'Section button link', 'https://transglobeedu.com/blogs', 'text', 'Blog section'),
                self::field('reviews_eyebrow', 'Section eyebrow', 'Student experiences', 'text', 'Google reviews'),
                self::field('reviews_title', 'Section title', 'Real Students. Real Google Reviews.', 'text', 'Google reviews'),
                self::field('reviews_copy', 'Section description', 'Hear directly from students who trusted Trans Globe Indore, managed by GEIC, for counselling, admissions, visa support and test preparation.', 'textarea', 'Google reviews'),
                self::field('reviews_score', 'Google rating', '4.8', 'text', 'Google reviews'),
                self::field('reviews_count_label', 'Google review count', '495 Google reviews', 'text', 'Google reviews'),
                self::field('reviews_url', 'Google reviews link', 'https://www.google.com/search?q=geic+indore#lrd=0x3962fd400e5c61eb:0x6db8cf73bcf20625,1,,,,', 'text', 'Google reviews'),
                self::field('faq_eyebrow', 'Section eyebrow', 'Questions, answered', 'text', 'Frequently asked questions'),
                self::field('faq_title', 'Section title', 'Frequently Asked Questions', 'text', 'Frequently asked questions'),
                self::field('faq_copy', 'Section description', 'Clear answers to the questions students and families ask before beginning their international education journey.', 'textarea', 'Frequently asked questions'),
                self::field('faq_one_question', 'Question 1', 'What does a study-abroad consultant do?', 'text', 'Frequently asked questions'),
                self::field('faq_one_answer', 'Answer 1', 'A consultant helps you choose a country and course, shortlist universities, strengthen applications, find scholarships, prepare your student visa and get ready for life abroad. At Trans Globe Indore, this support is free for students.', 'textarea', 'Frequently asked questions'),
                self::field('faq_two_question', 'Question 2', 'How early should I start the application process?', 'text', 'Frequently asked questions'),
                self::field('faq_two_answer', 'Answer 2', 'For most destinations, begin 12 to 18 months before your intended intake. This leaves enough time for language tests, university applications, scholarships and visa processing.', 'textarea', 'Frequently asked questions'),
                self::field('faq_three_question', 'Question 3', 'Can Indian students get scholarships?', 'text', 'Frequently asked questions'),
                self::field('faq_three_answer', 'Answer 3', 'Yes. Universities, governments and private organisations offer awards based on merit, field of study, financial need and other criteria. Trans Globe Indore helps identify and apply for suitable options.', 'textarea', 'Frequently asked questions'),
                self::field('faq_four_question', 'Question 4', 'What is Trans Globe’s visa success rate?', 'text', 'Frequently asked questions'),
                self::field('faq_four_answer', 'Answer 4', 'The Trans Globe network reports a 98.7% visa success rate, built on decades of experience preparing complete, consistent applications for students across 10+ countries.', 'textarea', 'Frequently asked questions'),
                self::field('faq_five_question', 'Question 5', 'Can I get counselling if I cannot visit the Indore office?', 'text', 'Frequently asked questions'),
                self::field('faq_five_answer', 'Answer 5', 'Yes. Trans Globe Indore offers online counselling sessions with the same detailed, specialist guidance available at our Indore office.', 'textarea', 'Frequently asked questions'),
                self::field('contact_eyebrow', 'CTA badge', 'Free, no-pressure guidance', 'text', 'Conversion CTA'),
                self::field('contact_title', 'CTA title', 'Your Journey Starts With One Conversation', 'text', 'Conversion CTA'),
                self::field('contact_copy', 'CTA description', 'You do not need to have everything figured out. Tell us where you are today, and we will explain your options honestly and help you take the next step.', 'textarea', 'Conversion CTA'),
                self::field('contact_button_label', 'CTA button label', 'Speak to Our Indore Counsellor', 'text', 'Conversion CTA'),
                self::field('contact_phone', 'Phone number', '+91 98266 66886', 'text', 'Conversion CTA'),
                self::field('contact_email', 'Email address', 'info@geic.in', 'text', 'Conversion CTA'),
                self::field('contact_address', 'Office address', 'Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001', 'textarea', 'Conversion CTA'),
                self::field('popup_enabled', 'Show homepage popup', '1', 'text', 'Homepage popup'),
                self::field('popup_eyebrow', 'Popup badge', 'Free profile review', 'text', 'Homepage popup'),
                self::field('popup_title', 'Popup heading', 'Your global education plan starts with one clear conversation.', 'text', 'Homepage popup'),
                self::field('popup_copy', 'Popup subheading', 'Tell our Indore counsellors where you are today. We will help you compare destinations, courses, scholarships and visa pathways without pressure.', 'textarea', 'Homepage popup'),
                self::field('popup_image', 'Popup image URL', 'assets/transglobe/services/services-team.avif', 'image', 'Homepage popup'),
                self::field('popup_image_alt', 'Popup image alt text', 'Trans Globe Indore counsellor helping a student plan their study-abroad journey', 'text', 'Homepage popup'),
                self::field('popup_cta_label', 'Popup button label', 'Book Free Counselling', 'text', 'Homepage popup'),
                self::field('popup_cta_url', 'Popup button link', '/contact#enquiry', 'text', 'Homepage popup'),
                self::field('popup_close_label', 'Popup close label', 'Maybe later', 'text', 'Homepage popup'),
                self::field('footer_newsletter_title', 'Newsletter title', 'Stay in the Study-Abroad Loop', 'text', 'Footer'),
                self::field('footer_newsletter_copy', 'Newsletter description', 'Get visa updates, scholarship alerts and honest study-abroad advice in your inbox.', 'textarea', 'Footer'),
                self::field('footer_newsletter_button', 'Newsletter button label', 'Join', 'text', 'Footer'),
                self::field('footer_badge', 'Footer badge text', 'Your journey with Trans Globe Indore starts here', 'text', 'Footer'),
                self::field('footer_title', 'Footer title', 'Start With GEIC Indore', 'text', 'Footer'),
                self::field('footer_cta_label', 'Footer button label', 'Book Free Counselling', 'text', 'Footer'),
                self::field('footer_cta_url', 'Footer button link', '/contact#enquiry', 'text', 'Footer'),
                self::field('footer_contact_heading', 'Contact column heading', 'Contact Us', 'text', 'Footer'),
                self::field('footer_hours', 'Office hours', 'Mon to Sat: 10:00 AM–6:30 PM', 'text', 'Footer'),
                self::field('footer_copyright', 'Footer copyright', '© 2026 Trans Globe Indore, managed by GEIC. Your trusted partner for global education.', 'textarea', 'Footer'),
            ]),
            self::page('destinations', 'Destinations', 'Country listing and destination discovery page', [
                self::field('hero_title', 'Hero title', 'Find the country that fits your ambition'),
                self::field('hero_copy', 'Hero description', 'Compare leading study destinations, understand what makes each one different and choose your next step with guidance from Trans Globe Indore.', 'textarea'),
                self::field('hero_image', 'Primary hero image URL', 'assets/transglobe/destinations/australia.jpg', 'image'),
            ]),
            self::page('pages.about', 'About GEIC Indore', 'Company story, mission, services, proof, process and frequently asked questions.', [
                self::field('hero_eyebrow', 'Hero eyebrow', 'Global Education. Personal Guidance.', 'text', 'Hero'),
                self::field('hero_title', 'Hero title', 'A trusted Indore team for your international education journey.', 'text', 'Hero'),
                self::field('hero_copy', 'Hero description', 'Trans Globe Indore, managed by Global Education and Immigration Consultants, turns complex study-abroad decisions into a clear, supported plan built around your goals.', 'textarea', 'Hero'),
                self::field('hero_image', 'Hero image URL', 'assets/transglobe/services/services-team.avif', 'image', 'Hero'),
                self::field('hero_image_alt', 'Hero image alt text', 'GEIC Indore education counsellors supporting international students', 'text', 'Hero'),
                self::field('hero_primary_cta_label', 'Primary button label', 'Book Free Counselling', 'text', 'Hero'),
                self::field('hero_primary_cta_url', 'Primary button link', '/contact#enquiry', 'text', 'Hero'),
                self::field('hero_secondary_cta_label', 'Secondary button label', 'Explore Our Services', 'text', 'Hero'),
                self::field('hero_secondary_cta_url', 'Secondary button link', '/services', 'text', 'Hero'),
                self::field('page_header_title', 'Page header title', 'About Trans Globe Indore', 'text', 'Page header'),
                self::field('page_header_copy', 'Page header description', 'Education choices are personal. Our role is to make every option, requirement and next step easier to understand.', 'textarea', 'Page header'),
                self::field('story_eyebrow', 'Story eyebrow', 'Welcome to Global Education', 'text', 'Our story'),
                self::field('story_title', 'Story title', 'Big ambitions deserve informed decisions.', 'text', 'Our story'),
                self::field('story_copy', 'Story paragraph 1', 'Choosing education in India or overseas is a major life decision. Our counsellors bring together accurate information, thoughtful profile assessment and practical support so students and families can move forward with confidence.', 'textarea', 'Our story'),
                self::field('story_copy_2', 'Story paragraph 2', 'For us, studying abroad is more than earning a degree. It is a chance to broaden perspective, develop independence and build skills and experiences that can change the direction of a life.', 'textarea', 'Our story'),
                self::field('story_image', 'Story image URL', 'assets/transglobe/about/international-business-award-2023.jpeg', 'image', 'Our story'),
                self::field('story_image_alt', 'Story image alt text', 'Trans Globe Indore representatives receiving the 2023 International Business Award for Best Abroad Education Consultant in Central India', 'text', 'Our story'),
                self::field('who_title', 'Who we are title', 'Who we are', 'text', 'Purpose'),
                self::field('who_copy', 'Who we are description', 'We are experienced study-abroad education consultants who help students understand universities, programs, admission requirements, scholarships, visas and the cultural realities of studying in another country.', 'textarea', 'Purpose'),
                self::field('mission_title', 'Mission title', 'Our mission', 'text', 'Purpose'),
                self::field('mission_copy', 'Mission description', 'Our mission is to make studying abroad simpler, more transparent and less stressful—from choosing a university and preparing applications to test planning, visa documentation and departure.', 'textarea', 'Purpose'),
                self::field('promise_title', 'Promise title', 'Our promise', 'text', 'Purpose'),
                self::field('promise_copy', 'Promise description', 'We listen before we recommend. Every plan is shaped by the student’s academic profile, career direction, finances, preferred destination and readiness—not by a one-size-fits-all shortlist.', 'textarea', 'Purpose'),
                self::field('team_eyebrow', 'Team eyebrow', 'Professional people', 'text', 'Leadership team'),
                self::field('team_title', 'Team title', 'Meet our expert education consultants.', 'text', 'Leadership team'),
                self::field('team_copy', 'Team introduction', 'Meet the people who bring experience, careful listening and practical study-abroad guidance to every student conversation.', 'textarea', 'Leadership team'),
                self::field('team_one_name', 'Team member 1 name', 'Johar Ali', 'text', 'Leadership team'),
                self::field('team_one_role', 'Team member 1 role', 'Leadership team', 'text', 'Leadership team'),
                self::field('team_one_bio', 'Team member 1 description', '', 'textarea', 'Leadership team'),
                self::field('team_one_image', 'Team member 1 portrait', 'assets/transglobe/about/johar-ali.webp', 'image', 'Leadership team'),
                self::field('team_one_image_alt', 'Team member 1 image alt text', 'Johar Ali from the Trans Globe Indore leadership team', 'text', 'Leadership team'),
                self::field('team_two_name', 'Team member 2 name', 'Ali', 'text', 'Leadership team'),
                self::field('team_two_role', 'Team member 2 role', 'Student counsellor', 'text', 'Leadership team'),
                self::field('team_two_bio', 'Team member 2 description', '', 'textarea', 'Leadership team'),
                self::field('team_two_image', 'Team member 2 portrait', 'assets/transglobe/about/ali.webp', 'image', 'Leadership team'),
                self::field('team_two_image_alt', 'Team member 2 image alt text', 'Ali, student counsellor at Trans Globe Indore', 'text', 'Leadership team'),
                self::field('team_three_name', 'Team member 3 name', 'Husain', 'text', 'Leadership team'),
                self::field('team_three_role', 'Team member 3 role', 'Student counsellor', 'text', 'Leadership team'),
                self::field('team_three_bio', 'Team member 3 description', '', 'textarea', 'Leadership team'),
                self::field('team_three_image', 'Team member 3 portrait', 'assets/transglobe/about/husain.webp', 'image', 'Leadership team'),
                self::field('team_three_image_alt', 'Team member 3 image alt text', 'Husain, student counsellor at Trans Globe Indore', 'text', 'Leadership team'),
                self::field('gallery_eyebrow', 'Gallery eyebrow', 'Guidance in action', 'text', 'Photo gallery'),
                self::field('gallery_title', 'Gallery title', 'Real conversations. Practical next steps.', 'text', 'Photo gallery'),
                self::field('gallery_copy', 'Gallery description', 'A closer look at Trans Globe Indore counsellors, students and international university representatives connecting through personal guidance and education events.', 'textarea', 'Photo gallery'),
                self::field('gallery_image_01', 'Gallery image 1', 'assets/transglobe/about/gallery/counselling-event-01.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_01', 'Gallery image 1 alt text', 'Students receiving one-to-one education guidance at a Trans Globe Indore counselling event', 'text', 'Photo gallery'),
                self::field('gallery_image_02', 'Gallery image 2', 'assets/transglobe/about/gallery/counselling-event-02.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_02', 'Gallery image 2 alt text', 'A family speaking with an international university representative at an education event', 'text', 'Photo gallery'),
                self::field('gallery_image_03', 'Gallery image 3', 'assets/transglobe/about/gallery/counselling-event-03.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_03', 'Gallery image 3 alt text', 'Students comparing international study options with university representatives', 'text', 'Photo gallery'),
                self::field('gallery_image_04', 'Gallery image 4', 'assets/transglobe/about/gallery/counselling-event-04.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_04', 'Gallery image 4 alt text', 'Students and families discussing overseas university pathways', 'text', 'Photo gallery'),
                self::field('gallery_image_05', 'Gallery image 5', 'assets/transglobe/about/gallery/counselling-event-05.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_05', 'Gallery image 5 alt text', 'Trans Globe Indore event registration and student support desk', 'text', 'Photo gallery'),
                self::field('gallery_image_06', 'Gallery image 6', 'assets/transglobe/about/gallery/counselling-event-06.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_06', 'Gallery image 6 alt text', 'A Trans Globe Indore counsellor reviewing IELTS preparation with a student', 'text', 'Photo gallery'),
                self::field('gallery_image_07', 'Gallery image 7', 'assets/transglobe/about/gallery/counselling-event-07.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_07', 'Gallery image 7 alt text', 'University of Suffolk representative counselling prospective students', 'text', 'Photo gallery'),
                self::field('gallery_image_08', 'Gallery image 8', 'assets/transglobe/about/gallery/counselling-event-08.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_08', 'Gallery image 8 alt text', 'International education adviser meeting a prospective student', 'text', 'Photo gallery'),
                self::field('gallery_image_09', 'Gallery image 9', 'assets/transglobe/about/gallery/counselling-event-09.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_09', 'Gallery image 9 alt text', 'University representatives explaining study options at the Trans Globe Indore event', 'text', 'Photo gallery'),
                self::field('gallery_image_10', 'Gallery image 10', 'assets/transglobe/about/gallery/counselling-event-10.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_10', 'Gallery image 10 alt text', 'Students checking in at a Trans Globe Indore education fair', 'text', 'Photo gallery'),
                self::field('gallery_image_11', 'Gallery image 11', 'assets/transglobe/about/gallery/counselling-event-11.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_11', 'Gallery image 11 alt text', 'A student discussing application information with an education adviser', 'text', 'Photo gallery'),
                self::field('gallery_image_12', 'Gallery image 12', 'assets/transglobe/about/gallery/counselling-event-12.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_12', 'Gallery image 12 alt text', 'A family exploring international study opportunities with a university representative', 'text', 'Photo gallery'),
                self::field('gallery_image_13', 'Gallery image 13', 'assets/transglobe/about/gallery/counselling-event-13.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_13', 'Gallery image 13 alt text', 'Students and families attending individual international education meetings', 'text', 'Photo gallery'),
                self::field('gallery_image_14', 'Gallery image 14', 'assets/transglobe/about/gallery/counselling-event-14.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_14', 'Gallery image 14 alt text', 'GBS Malta representative discussing courses with prospective students', 'text', 'Photo gallery'),
                self::field('gallery_image_15', 'Gallery image 15', 'assets/transglobe/about/gallery/counselling-event-15.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_15', 'Gallery image 15 alt text', 'Trans Globe Indore counsellors guiding students during an education event', 'text', 'Photo gallery'),
                self::field('gallery_image_16', 'Gallery image 16', 'assets/transglobe/about/gallery/award-event-01.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_16', 'Gallery image 16 alt text', 'Trans Globe Indore representatives at the International Business Awards', 'text', 'Photo gallery'),
                self::field('gallery_image_17', 'Gallery image 17', 'assets/transglobe/about/gallery/award-event-02.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_17', 'Gallery image 17 alt text', 'Trans Globe Indore representative greeting a guest at the International Business Awards', 'text', 'Photo gallery'),
                self::field('gallery_image_18', 'Gallery image 18', 'assets/transglobe/about/gallery/award-event-03.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_18', 'Gallery image 18 alt text', 'Trans Globe Indore representatives on the International Business Awards red carpet', 'text', 'Photo gallery'),
                self::field('gallery_image_19', 'Gallery image 19', 'assets/transglobe/about/gallery/award-event-04.jpg', 'image', 'Photo gallery'),
                self::field('gallery_alt_19', 'Gallery image 19 alt text', 'Trans Globe Indore receiving the 2023 International Business Award for Best Abroad Education Consultant in Central India', 'text', 'Photo gallery'),
                self::field('services_eyebrow', 'Services eyebrow', 'What we do', 'text', 'What we do'),
                self::field('services_title', 'Services title', 'One connected team for every important step.', 'text', 'What we do'),
                self::field('services_copy', 'Services introduction', 'From the first question to arrival overseas, our specialists coordinate the details that make a strong international education plan possible.', 'textarea', 'What we do'),
                self::field('service_one_title', 'Service 1 title', 'University & program selection', 'text', 'What we do'),
                self::field('service_one_copy', 'Service 1 description', 'Compare suitable institutions and courses against your academic background, career goals, budget and preferred student experience.', 'textarea', 'What we do'),
                self::field('service_two_title', 'Service 2 title', 'Applications & documentation', 'text', 'What we do'),
                self::field('service_two_copy', 'Service 2 description', 'Prepare complete applications, supporting documents, statements and timelines with detailed checks before submission.', 'textarea', 'What we do'),
                self::field('service_three_title', 'Service 3 title', 'Test preparation', 'text', 'What we do'),
                self::field('service_three_copy', 'Service 3 description', 'Build a practical score plan for IELTS, PTE, TOEFL, GRE, GMAT or SAT with focused training and realistic practice.', 'textarea', 'What we do'),
                self::field('service_four_title', 'Service 4 title', 'Scholarships & funding', 'text', 'What we do'),
                self::field('service_four_copy', 'Service 4 description', 'Identify relevant awards, understand eligibility and present your academic and financial information clearly.', 'textarea', 'What we do'),
                self::field('service_five_title', 'Service 5 title', 'Visa & immigration support', 'text', 'What we do'),
                self::field('service_five_copy', 'Service 5 description', 'Organise evidence, financial documents, forms and interview preparation for a consistent student-visa application.', 'textarea', 'What we do'),
                self::field('service_six_title', 'Service 6 title', 'Pre-departure & ongoing support', 'text', 'What we do'),
                self::field('service_six_copy', 'Service 6 description', 'Prepare for travel, accommodation, banking, arrival and the practical realities of beginning student life abroad.', 'textarea', 'What we do'),
                self::field('proof_eyebrow', 'Proof eyebrow', 'Experience you can measure', 'text', 'Track record'),
                self::field('proof_title', 'Proof title', 'A global network, grounded in Indore.', 'text', 'Track record'),
                self::field('proof_copy', 'Proof description', 'Students receive local, accessible support backed by the reach and experience of the wider Trans Globe network.', 'textarea', 'Track record'),
                self::field('proof_students_value', 'Students placed value', '70,250+', 'text', 'Track record'),
                self::field('proof_students_label', 'Students placed label', 'students placed worldwide', 'text', 'Track record'),
                self::field('proof_universities_value', 'Universities value', '800+', 'text', 'Track record'),
                self::field('proof_universities_label', 'Universities label', 'partner universities', 'text', 'Track record'),
                self::field('proof_visas_value', 'Visa success value', '98.7%', 'text', 'Track record'),
                self::field('proof_visas_label', 'Visa success label', 'reported visa success rate', 'text', 'Track record'),
                self::field('proof_years_value', 'Experience value', '32+ yrs', 'text', 'Track record'),
                self::field('proof_years_label', 'Experience label', 'of international education expertise', 'text', 'Track record'),
                self::field('process_eyebrow', 'Process eyebrow', 'How we work', 'text', 'Process'),
                self::field('process_title', 'Process title', 'Clear guidance, connected from start to finish.', 'text', 'Process'),
                self::field('process_one_title', 'Step 1 title', 'Listen & assess', 'text', 'Process'),
                self::field('process_one_copy', 'Step 1 description', 'We understand your profile, ambitions, concerns and non-negotiables before suggesting a direction.', 'textarea', 'Process'),
                self::field('process_two_title', 'Step 2 title', 'Compare & plan', 'text', 'Process'),
                self::field('process_two_copy', 'Step 2 description', 'Together we compare destinations, universities, courses, costs, scholarships and timelines.', 'textarea', 'Process'),
                self::field('process_three_title', 'Step 3 title', 'Prepare & apply', 'text', 'Process'),
                self::field('process_three_copy', 'Step 3 description', 'Our specialists coordinate applications, supporting documents, test plans and visa preparation.', 'textarea', 'Process'),
                self::field('process_four_title', 'Step 4 title', 'Depart with confidence', 'text', 'Process'),
                self::field('process_four_copy', 'Step 4 description', 'You receive practical pre-departure guidance and support for a confident transition into student life.', 'textarea', 'Process'),
                self::field('faq_eyebrow', 'FAQ eyebrow', 'Questions students ask', 'text', 'Frequently asked questions'),
                self::field('faq_title', 'FAQ title', 'What to know before choosing a consultant.', 'text', 'Frequently asked questions'),
                self::field('faq_one_question', 'Question 1', 'How much do study-abroad consultants charge?', 'text', 'Frequently asked questions'),
                self::field('faq_one_answer', 'Answer 1', 'Fees vary with the services and destination. Trans Globe Indore offers a free initial counselling conversation so you can understand your options and exactly what support is included before making a commitment.', 'textarea', 'Frequently asked questions'),
                self::field('faq_two_question', 'Question 2', 'Can I work with GEIC if I am not in Indore?', 'text', 'Frequently asked questions'),
                self::field('faq_two_answer', 'Answer 2', 'Yes. Our online counselling process gives students outside Indore access to the same profile review, document support and destination specialists as an in-office appointment.', 'textarea', 'Frequently asked questions'),
                self::field('faq_three_question', 'Question 3', 'Why is a study-abroad consultant useful?', 'text', 'Frequently asked questions'),
                self::field('faq_three_answer', 'Answer 3', 'A good consultant connects admissions, scholarships, tests, visas and practical preparation into one plan. That reduces missed requirements and helps you make decisions using current, destination-specific information.', 'textarea', 'Frequently asked questions'),
                self::field('cta_eyebrow', 'CTA eyebrow', 'Start your journey', 'text', 'Conversion CTA'),
                self::field('cta_title', 'CTA title', 'A powerful collaboration for a prosperous tomorrow.', 'text', 'Conversion CTA'),
                self::field('cta_copy', 'CTA description', 'Bring us your questions, your goals and your current profile. We will help you understand the strongest next step.', 'textarea', 'Conversion CTA'),
                self::field('cta_label', 'CTA button label', 'Speak to Our Indore Counsellor', 'text', 'Conversion CTA'),
                self::field('cta_url', 'CTA button link', '/contact#enquiry', 'text', 'Conversion CTA'),
                self::field('cta_image', 'CTA image URL', 'assets/transglobe/about/student-guidance-session-2023.jpg', 'image', 'Conversion CTA'),
                self::field('cta_image_alt', 'CTA image alt text', 'A Trans Globe Indore counsellor speaking with a student during a guidance session', 'text', 'Conversion CTA'),
            ]),
            self::page('pages.terms', 'Terms & Conditions', 'Legal terms for the Trans Globe Indore website and counselling services.', [
                self::field('hero_eyebrow', 'Hero eyebrow', 'Clear terms. Confident decisions.', 'text', 'Hero'),
                self::field('hero_title', 'Hero title', 'Terms & Conditions', 'text', 'Hero'),
                self::field('hero_copy', 'Hero description', 'These terms explain how you may use the Trans Globe Indore website and what to expect from our study-abroad guidance and support.', 'textarea', 'Hero'),
                self::field('intro_title', 'Introduction title', 'An open, responsible working relationship', 'text', 'Introduction'),
                self::field('intro_copy', 'Introduction', 'Trans Globe Indore, managed by Global Education and Immigration Consultants (GEIC), provides study-abroad information, counselling and application support. By using this website or submitting an enquiry, you agree to these terms.', 'textarea', 'Introduction'),
                self::field('services_title', 'Services section title', 'Scope of our services', 'text', 'Legal sections'),
                self::field('services_copy', 'Services section content', 'Our support may include country, course and university counselling; application and document guidance; and information about admissions, visas, scholarships, funding and timelines. Guidance is advisory and delivered on a best-effort basis.', 'textarea', 'Legal sections'),
                self::field('outcomes_title', 'Outcomes section title', 'No guarantee of outcomes', 'text', 'Legal sections'),
                self::field('outcomes_copy', 'Outcomes section content', 'Educational institutions and immigration authorities make their own admission, scholarship and visa decisions. Requirements, fees, policies and timelines can change. We cannot guarantee an admission, visa, scholarship, interview or any other particular result.', 'textarea', 'Legal sections'),
                self::field('responsibilities_title', 'Responsibilities section title', 'Your responsibilities', 'text', 'Legal sections'),
                self::field('responsibilities_copy', 'Responsibilities section content', 'You must provide information that is complete, accurate and current; submit genuine, verifiable documents; and meet the requirements and deadlines set by institutions and authorities. False or incomplete information can lead to rejection or other consequences.', 'textarea', 'Legal sections'),
                self::field('fees_title', 'Fees section title', 'Fees, payments and refunds', 'text', 'Legal sections'),
                self::field('fees_copy', 'Fees section content', 'If a service has a fee, we will communicate it before you proceed. Payment and refund terms are governed by the relevant service agreement or policy. A payment may be non-refundable unless we confirm otherwise in writing.', 'textarea', 'Legal sections'),
                self::field('content_title', 'Content section title', 'Website content and intellectual property', 'text', 'Legal sections'),
                self::field('content_copy', 'Content section content', 'Website text, graphics, images, forms, logos, design and branding belong to Trans Globe or their respective rights holders. You may use them for personal information only and may not reproduce, modify or distribute them without permission.', 'textarea', 'Legal sections'),
                self::field('conduct_title', 'Conduct section title', 'Acceptable website use', 'text', 'Legal sections'),
                self::field('conduct_copy', 'Conduct section content', 'Do not use this website unlawfully or fraudulently, interfere with its security or availability, upload harmful software, impersonate another person, or misuse our forms and communication channels.', 'textarea', 'Legal sections'),
                self::field('third_party_title', 'Third-party section title', 'Third-party websites and services', 'text', 'Legal sections'),
                self::field('third_party_copy', 'Third-party section content', 'Links to universities, government portals and other third parties are provided for convenience. Their content, availability, privacy practices and terms are controlled by those organisations, not by Trans Globe Indore.', 'textarea', 'Legal sections'),
                self::field('liability_title', 'Liability section title', 'Limitations and service availability', 'text', 'Legal sections'),
                self::field('liability_copy', 'Liability section content', 'To the extent permitted by law, Trans Globe Indore is not responsible for indirect losses, missed opportunities or delays caused by institutions, authorities, service providers, network failures or events outside our reasonable control.', 'textarea', 'Legal sections'),
                self::field('law_title', 'Law section title', 'Changes, termination and governing law', 'text', 'Legal sections'),
                self::field('law_copy', 'Law section content', 'We may update these terms or restrict access when the website is misused, these terms are breached, or the law requires it. Updated terms apply when posted. These terms are governed by the laws of India and disputes are subject to courts with applicable jurisdiction in India.', 'textarea', 'Legal sections'),
                self::field('contact_title', 'Contact title', 'Questions about these terms?', 'text', 'Contact'),
                self::field('contact_copy', 'Contact description', 'Speak with the Trans Globe Indore team if you need help understanding how these terms apply to your enquiry or counselling service.', 'textarea', 'Contact'),
                self::field('contact_cta', 'Contact button label', 'Contact the Indore office', 'text', 'Contact'),
            ]),
            self::page('services', 'Services', 'Service list and counselling support overview', [
                self::field('hero_title', 'Hero title', 'Every expert step for your global future.'),
                self::field('hero_copy', 'Hero description', 'From your first shortlist to your first day abroad, get one joined-up team for every important decision, document and deadline.', 'textarea'),
                self::field('hero_image', 'Primary hero image URL', 'assets/transglobe/services/services-team.avif', 'image'),
                self::field('content_title', 'Content section title', 'How we can help', 'text', 'Main content'),
                self::field('content_copy', 'Content section content', 'Explore practical support tailored to your goals.', 'textarea', 'Main content'),
                self::field('cta_title', 'Call-to-action heading', 'Ready to take the next step?', 'text', 'Call to action'),
                self::field('cta_copy', 'Call-to-action text', 'Speak with our team for clear, personal guidance.', 'textarea', 'Call to action'),
                self::field('cta_label', 'Call-to-action button', 'Speak to a Counsellor', 'text', 'Call to action'),
            ]),
            self::page('events', 'Events', 'University visits, admission days and study-abroad events', [
                self::field('hero_title', 'Hero title', 'Meet universities. Find your next move.'),
                self::field('hero_copy', 'Hero description', 'Discover Trans Globe events, university visits and admission days that turn study-abroad research into useful conversations and clear next steps.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/transglobe/events/meet-eu-business-school-2026.jpg', 'image'),
                self::field('archive_title', 'Archive section title', 'Event highlights from Indore and beyond'),
                self::field('archive_copy', 'Archive section description', 'Explore recent university visits, admission days and expos from the Trans Globe network.', 'textarea'),
                self::field('content_title', 'Content section title', 'Make your next move', 'text', 'Main content'),
                self::field('content_copy', 'Content section content', 'Meet specialists and university representatives at an upcoming event.', 'textarea', 'Main content'),
                self::field('cta_title', 'Call-to-action heading', 'Ready to join us?', 'text', 'Call to action'),
                self::field('cta_copy', 'Call-to-action text', 'Register your interest and our team will share the details.', 'textarea', 'Call to action'),
                self::field('cta_label', 'Call-to-action button', 'Register interest', 'text', 'Call to action'),
            ]),
            self::page('scholarships', 'Scholarships', 'Scholarship listing and funding guidance', [
                self::field('hero_title', 'Hero title', 'Fund your future, without the guesswork.'),
                self::field('hero_copy', 'Hero description', 'Studying abroad does not have to feel out of reach. Discover scholarship opportunities that match your profile, then apply with a clear, well-prepared plan.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/transglobe/destinations/australia-detail-hero.jpg', 'image'),
                self::field('content_title', 'Content section title', 'Find funding that fits', 'text', 'Main content'),
                self::field('content_copy', 'Content section content', 'Discover awards and bursaries that match your profile and study plan.', 'textarea', 'Main content'),
                self::field('cta_title', 'Call-to-action heading', 'Ready to explore your options?', 'text', 'Call to action'),
                self::field('cta_copy', 'Call-to-action text', 'Let our counsellors help you build a realistic funding plan.', 'textarea', 'Call to action'),
                self::field('cta_label', 'Call-to-action button', 'Discuss scholarships', 'text', 'Call to action'),
            ]),
            self::page('tests', 'Test preparation', 'IELTS, PTE, TOEFL and other test-prep overview', [
                self::field('hero_title', 'Hero title', 'Prepare with purpose. Test with confidence.'),
                self::field('hero_copy', 'Hero description', 'Choose the right test for your destination, build the skills it measures and move into your university application with a score plan that makes sense.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/services/university-admissions.jpg', 'image'),
                self::field('content_title', 'Content section title', 'Prepare with purpose', 'text', 'Main content'),
                self::field('content_copy', 'Content section content', 'Build the skills, strategy and confidence needed for your target score.', 'textarea', 'Main content'),
                self::field('cta_title', 'Call-to-action heading', 'Ready to start preparing?', 'text', 'Call to action'),
                self::field('cta_copy', 'Call-to-action text', 'Speak with our test-preparation team about your target and timeline.', 'textarea', 'Call to action'),
                self::field('cta_label', 'Call-to-action button', 'Plan my preparation', 'text', 'Call to action'),
            ]),
            self::page('contact', 'Contact', 'Contact information and enquiry page', [
                self::field('hero_title', 'Hero title', 'Let’s make your next step clear.'),
                self::field('hero_copy', 'Hero description', 'Speak with the Trans Globe Indore team about study destinations, applications, scholarships, test preparation or your student-visa pathway.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/services/expert-counselling.jpg', 'image'),
            ]),
        ];

        foreach (DestinationCatalog::slugs() as $slug) {
            $item = DestinationCatalog::find($slug);
            $pages[] = self::page('destination.'.$slug, 'Destination · '.$item['name'], 'Complete destination details, visuals, requirements, costs, careers, universities and FAQs.', self::destinationFields($item));
        }
        foreach (ServiceCatalog::all() as $item) {
            $pages[] = self::detail('service.'.$item['slug'], 'Service · '.$item['title'], 'Service details page', $item['title'], $item['summary'], $item['image']);
        }
        foreach (EventCatalog::all() as $item) {
            $pages[] = self::detail('event.'.$item['slug'], 'Event · '.$item['title'], 'Event details page', $item['title'], $item['summary'], $item['image']);
        }
        foreach (ScholarshipCatalog::all() as $item) {
            $pages[] = self::detail('scholarship.'.$item['slug'], 'Scholarship · '.$item['name'], 'Scholarship details page', 'Find your '.$item['name'].' funding path.', $item['tagline'], $item['image']);
        }
        foreach (TestPrepCatalog::all() as $item) {
            $pages[] = self::detail('test.'.$item['slug'], 'Test · '.$item['title'], 'Test preparation details page', $item['title'], $item['summary'], $item['image']);
        }

        $pages[] = self::promotionPage(
            'promotion.landing',
            'Study Abroad Guidance',
            'The primary promotional campaign page at /landing.'
        );

        try {
            if (Schema::hasTable('cms_pages')) {
                foreach (CmsPage::query()->orderBy('group')->orderBy('name')->get() as $customPage) {
                    $pages[] = self::customPage($customPage);
                }
            }
        } catch (QueryException) {
            // The fixed catalogue remains available during setup or migration.
        }

        return $pages;
    }

    public static function find(string $key): ?array
    {
        foreach (self::all() as $page) {
            if ($page['key'] === $key) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Return the first canonical page definition for a CMS group.
     *
     * Custom pages use the same editable structure as the group's baseline
     * page, so new slugs can be created without falling back to a tiny hero
     * only schema.
     */
    public static function firstForGroup(string $group): ?array
    {
        // Prefer the group's baseline definition when it exists.  Some
        // groups also contain dotted detail definitions (for example
        // `event.*`); those are intentionally secondary because custom
        // slugs should inherit the complete group-level schema.
        $baseline = self::find($group);

        if ($baseline) {
            return $baseline;
        }

        foreach (self::all() as $page) {
            if (self::groupFor($page['key']) === $group && ! str_contains($page['key'], '.')) {
                return $page;
            }
        }

        return collect(self::all())
            ->first(fn (array $page): bool => self::groupFor($page['key']) === $group);
    }

    /** @return array<string, array{label: string, description: string}> */
    public static function groups(): array
    {
        return [
            'landing' => ['label' => 'Landing pages', 'description' => 'Main website pages and conversion journeys'],
            'promotions' => ['label' => 'Promotional pages', 'description' => 'Reusable campaign pages with the Trans Globe promotional design'],
            'destinations' => ['label' => 'Destinations', 'description' => 'Country-specific study destination pages'],
            'services' => ['label' => 'Services', 'description' => 'Individual counselling and student-support services'],
            'events' => ['label' => 'Events', 'description' => 'University visits, admission days and study-abroad expos'],
            'scholarships' => ['label' => 'Scholarships', 'description' => 'Country scholarship and funding pages'],
            'tests' => ['label' => 'Test preparation', 'description' => 'IELTS, PTE, TOEFL and preparation pages'],
        ];
    }

    public static function groupFor(string $pageKey): string
    {
        return match (true) {
            str_starts_with($pageKey, 'destination.') => 'destinations',
            str_starts_with($pageKey, 'service.') => 'services',
            str_starts_with($pageKey, 'event.') => 'events',
            str_starts_with($pageKey, 'scholarship.') => 'scholarships',
            str_starts_with($pageKey, 'test.') => 'tests',
            str_starts_with($pageKey, 'promotion.') => 'promotions',
            default => 'landing',
        };
    }

    /**
     * Return non-editable visual groups used by a public page.
     *
     * These groups sit alongside image fields in the CMS so editors can see
     * the complete visual footprint of a page, not just its editable hero.
     * `usageCount` is the number of individual assets rendered in that group.
     *
     * @return array<int, array{label: string, section: string, path: string, usageCount: int}>
     */
    public static function mediaGroupsForPage(string $pageKey): array
    {
        if ($pageKey === 'home') {
            return [
                ['label' => 'Hero global-orbit graphic', 'section' => 'Hero', 'path' => 'assets/transglobe/geic-revolver.svg?v=20260825b', 'usageCount' => 1],
                ['label' => 'Hero supporting overlay', 'section' => 'Hero', 'path' => 'store/landing_builder/landing_13/371/hero_overlay_UGc.png', 'usageCount' => 1],
                ['label' => 'Statistics background texture', 'section' => 'Highlights', 'path' => 'store/landing_builder/landing_13/372/statistics_bg_T0k.png', 'usageCount' => 1],
                ['label' => 'Study-destination card gallery', 'section' => 'Destinations section', 'path' => 'assets/transglobe/destinations/australia.jpg', 'usageCount' => 24],
                ['label' => 'Work-visa pathway cards', 'section' => 'Work visa pathways', 'path' => 'assets/transglobe/destinations/canada.jpg', 'usageCount' => 6],
                ['label' => 'Partner-university logo carousel', 'section' => 'Partner universities', 'path' => 'assets/transglobe/universities/australian-national-university.png', 'usageCount' => 10],
                ['label' => 'Study-abroad blog cards', 'section' => 'Blog', 'path' => 'assets/transglobe/destinations/australia/campus-students.jpg', 'usageCount' => 5],
                ['label' => 'Google-review profile images', 'section' => 'Student reviews', 'path' => 'assets/geic/reviewers/arun-rawat.jpg', 'usageCount' => 10],
            ];
        }

        return [];
    }

    private static function page(string $key, string $name, string $description, array $fields): array
    {
        return compact('key', 'name', 'description', 'fields');
    }

    private static function detail(string $key, string $name, string $description, string $title, string $copy, string $image): array
    {
        return self::page($key, $name, $description, [
            self::field('hero_title', 'Hero title', $title),
            self::field('hero_copy', 'Hero description', $copy, 'textarea'),
            self::field('hero_image', 'Hero image URL', $image, 'image'),
        ]);
    }

    private static function destinationFields(array $item): array
    {
        $isAustralia = $item['slug'] === 'australia';
        $name = $item['name'];
        $stats = $item['stats'] ?? [['42', 'Universities nationwide'], ['Go8', 'Plus regional, ATN and IRU networks'], ['2–3 years', 'Common post-study work range'], ['160+', 'Nationalities represented']];
        $benefits = $item['benefits'] ?? [
            ['World-class universities', 'Research-intensive institutions, including the Group of Eight, with globally respected qualifications.'],
            ['Work while studying', 'Eligible international students can work during teaching periods and more during scheduled breaks.'],
            ['Safe and welcoming', 'Strong student protections, multicultural cities and a high standard of living support international students.'],
            ['Wide course choice', 'Flexible pathways across business, engineering, IT, health, hospitality, creative arts and more.'],
            ['Practical learning', 'Internships, industry projects, placements and work-integrated learning build career-ready experience.'],
            ['Strong value', 'Internationally recognised education with scholarships and practical pathways that support long-term outcomes.'],
        ];
        $overview = $item['overview'] ?? 'Australia combines research-focused universities with teaching designed around industry. Students can study across the Group of Eight, technology-focused institutions and strong regional university networks.';
        $overviewTwo = $item['overview_2'] ?? 'Sydney, Melbourne, Brisbane, Perth, Adelaide, Canberra and other student cities offer reliable infrastructure, healthcare, public transport and multicultural communities.';
        $requirements = $isAustralia ? [
            'Valid passport and an updated resume for postgraduate applicants',
            'Class 10, Class 12 and previous degree transcripts and certificates',
            'IELTS, PTE or TOEFL results required by the chosen institution',
            'Academic or professional recommendation letters where applicable',
            'Work experience evidence for selected postgraduate and MBA programs',
        ] : $item['requirements'];
        $costs = $item['costs'] ?? [['Undergraduate tuition', 'AUD 24K–40K'], ['Postgraduate tuition', 'AUD 25K–45K'], ['Living & accommodation', 'AUD 29,710'], ['Airfare planning', 'AUD 2,000']];
        $careers = $item['careers'] ?? ['Commerce & Analytics', 'Machine Learning & AI', 'Nursing & Paramedical', 'Accounting & Finance', 'Hospitality & Tourism', 'Education & Teaching', 'Psychology & Social Sciences', 'Environmental Science'];
        $intakes = $item['intakes'] ?? [['February', 'Main intake with the widest course selection.'], ['July', 'Strong mid-year intake across many institutions.'], ['October', 'Selected programs at participating universities.']];
        $faqs = $item['faqs'];
        $universities = $item['universities'];

        $fields = [
            self::field('destination_name', 'Destination name', $name, 'text', 'Page identity'),
            self::field('hero_label', 'Hero badge', 'Expert guidance from GEIC Indore'),
            self::field('hero_title', 'Hero title', 'Study in '.$name),
            self::field('hero_copy', 'Hero description', $item['tagline'], 'textarea'),
            self::field('hero_image', 'Hero image URL', $item['hero'], 'image'),
            self::field('hero_image_alt', 'Hero image alt text', 'Study in '.$name),
            self::field('hero_image_position', 'Hero image position', $item['hero_position']),
            self::field('flag_image', 'Country flag image URL', 'assets/transglobe/destinations/flags/'.$item['flag'], 'image'),
            self::field('flag_alt', 'Country flag alt text', $name.' flag'),
            self::field('hero_primary_cta_label', 'Primary button label', 'Free consultation'),
            self::field('hero_secondary_cta_label', 'Secondary button label', 'See the complete process'),
        ];
        foreach ($stats as $index => [$value, $label]) {
            $number = $index + 1;
            $fields[] = self::field("stat_{$number}_value", "Statistic {$number} value", $value, 'text', 'Key statistics');
            $fields[] = self::field("stat_{$number}_label", "Statistic {$number} label", $label, 'text', 'Key statistics');
        }
        array_push($fields,
            self::field('overview_kicker', 'Section eyebrow', $name.' at a glance', 'text', 'Overview'),
            self::field('overview_title', 'Section title', $isAustralia ? 'Academic prestige meets real-world learning' : 'A closer look at your study destination', 'text', 'Overview'),
            self::field('overview_copy', 'Overview paragraph 1', $overview, 'textarea', 'Overview'),
            self::field('overview_copy_2', 'Overview paragraph 2', $overviewTwo, 'textarea', 'Overview'),
            self::field('overview_image', 'Overview image URL', $item['card'], 'image', 'Overview'),
            self::field('overview_image_badge', 'Overview image badge', 'Explore '.$name, 'text', 'Overview'),
            self::field('overview_cta_label', 'Overview button label', 'Check requirements', 'text', 'Overview'),
            self::field('benefits_kicker', 'Section eyebrow', 'Why '.$name, 'text', 'Why this destination'),
            self::field('benefits_title', 'Section title', 'A study destination built for ambitious students', 'text', 'Why this destination'),
            self::field('benefits_lead', 'Section description', 'Strong academics, practical experience and an inclusive student environment work together to support both education and employability.', 'textarea', 'Why this destination'),
            self::field('gallery_kicker', 'Section eyebrow', 'Beyond the classroom', 'text', 'Lifestyle gallery'),
            self::field('gallery_title', 'Gallery section title', 'Discover student life in '.$name, 'text', 'Lifestyle gallery'),
            self::field('gallery_lead', 'Gallery section description', 'See the campus, city and student experience behind your study plan.', 'textarea', 'Lifestyle gallery'),
        );
        foreach ($benefits as $index => [$title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("benefit_{$number}_title", "Benefit {$number} title", $title, 'text', 'Why this destination');
            $fields[] = self::field("benefit_{$number}_copy", "Benefit {$number} description", $copy, 'textarea', 'Why this destination');
        }
        foreach ($item['gallery'] as $index => $image) {
            $number = $index + 1;
            $fields[] = self::field("gallery_{$number}_image", "Gallery image {$number} URL", $image['src'], 'image', 'Lifestyle gallery');
            $fields[] = self::field("gallery_{$number}_alt", "Gallery image {$number} alt text", $image['alt'], 'text', 'Lifestyle gallery');
            $fields[] = self::field("gallery_{$number}_label", "Gallery image {$number} caption", $image['label'], 'text', 'Lifestyle gallery');
        }
        $fields[] = self::field('facts_title', 'At-a-glance title', $name.' at a glance', 'text', 'At a glance');
        $fields[] = self::field('facts_intro', 'At-a-glance introduction', $item['facts_intro'], 'textarea', 'At a glance');
        foreach ($item['facts'] as $index => [$label, $value]) {
            $number = $index + 1;
            $fields[] = self::field("fact_{$number}_label", "Fact {$number} label", $label, 'text', 'At a glance');
            $fields[] = self::field("fact_{$number}_value", "Fact {$number} value", $value, 'text', 'At a glance');
        }
        array_push($fields,
            self::field('journey_kicker', 'Section eyebrow', 'The complete journey', 'text', 'Study journey'),
            self::field('journey_title', 'Section title', 'One connected path from counselling to '.$name, 'text', 'Study journey'),
            self::field('journey_lead', 'Section description', 'Follow one clear route through every milestone, document and decision, with Trans Globe Indore supporting you all the way.', 'textarea', 'Study journey'),
            self::field('journey_count_label', 'Milestone count label', 'Guided milestones', 'text', 'Study journey'),
            self::field('journey_outcome_label', 'Outcome eyebrow', 'Destination reached', 'text', 'Study journey'),
            self::field('journey_outcome_copy', 'Outcome message', 'Arrive informed, prepared and ready for student life in '.$name.'.', 'textarea', 'Study journey'),
            self::field('journey_outcome_cta_label', 'Outcome button label', 'Start my journey', 'text', 'Study journey'),
        );
        foreach ($item['journey'] as $index => [$stage, $title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("journey_{$number}_stage", "Step {$number} stage", $stage, 'text', 'Study journey');
            $fields[] = self::field("journey_{$number}_title", "Step {$number} title", $title, 'text', 'Study journey');
            $fields[] = self::field("journey_{$number}_copy", "Step {$number} description", $copy, 'textarea', 'Study journey');
        }
        array_push($fields,
            self::field('requirements_kicker', 'Section eyebrow', 'Prepare with confidence', 'text', 'Requirements & visa'),
            self::field('requirements_title', 'Section title', 'Admission and student visa essentials', 'text', 'Requirements & visa'),
            self::field('requirements_lead', 'Section description', 'Exact requirements vary by program and institution; we help you build a complete, well-organised application.', 'textarea', 'Requirements & visa'),
            self::field('requirements_list_title', 'Admission panel title', 'Typical admission documents', 'text', 'Requirements & visa'),
            self::field('requirements_cta_label', 'Eligibility button label', 'Discuss my eligibility', 'text', 'Requirements & visa'),
        );
        foreach ($requirements as $index => $requirement) {
            $number = $index + 1;
            $fields[] = self::field("requirement_{$number}", "Requirement {$number}", $requirement, 'textarea', 'Requirements & visa');
        }
        array_push($fields,
            self::field('visa_kicker', 'Visa eyebrow', 'Student visa', 'text', 'Requirements & visa'),
            self::field('visa_title', 'Visa heading', $item['visa_title'] ?? 'Subclass 500 essentials', 'text', 'Requirements & visa'),
            self::field('visa_copy', 'Visa guidance', $item['visa_copy'] ?? 'Prepare English proficiency, Genuine Student, financial-capacity, Confirmation of Enrolment and health-cover evidence.', 'textarea', 'Requirements & visa'),
            self::field('visa_note', 'Visa disclaimer', 'Visa rules, work rights and financial thresholds can change. Confirm current requirements with the relevant government authority before applying.', 'textarea', 'Requirements & visa'),
            self::field('costs_kicker', 'Section eyebrow', 'Plan your budget', 'text', 'Costs'),
            self::field('costs_title', 'Section title', 'Indicative financial planning', 'text', 'Costs'),
            self::field('costs_lead', 'Section description', 'Use these planning ranges as a starting point. Your course, city, lifestyle and current government charges determine the final amount.', 'textarea', 'Costs'),
            self::field('costs_note', 'Costs disclaimer', 'Figures are indicative and can change. Confirm current tuition, living-cost evidence and visa charges with your counsellor before applying.', 'textarea', 'Costs'),
        );
        foreach ($costs as $index => [$label, $value]) {
            $number = $index + 1;
            $fields[] = self::field("cost_{$number}_label", "Cost {$number} label", $label, 'text', 'Costs');
            $fields[] = self::field("cost_{$number}_value", "Cost {$number} value", $value, 'text', 'Costs');
        }
        array_push($fields,
            self::field('careers_kicker', 'Section eyebrow', 'Future ready', 'text', 'Careers & intakes'),
            self::field('careers_title', 'Section title', 'Build your course and intake shortlist', 'text', 'Careers & intakes'),
            self::field('careers_list_title', 'Career panel title', 'Popular career-focused fields', 'text', 'Careers & intakes'),
            self::field('intakes_title', 'Intake panel title', 'Common intakes', 'text', 'Careers & intakes'),
        );
        foreach ($careers as $index => $career) {
            $number = $index + 1;
            $fields[] = self::field("career_{$number}", "Career field {$number}", $career, 'text', 'Careers & intakes');
        }
        foreach ($intakes as $index => [$title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("intake_{$number}_title", "Intake {$number} name", $title, 'text', 'Careers & intakes');
            $fields[] = self::field("intake_{$number}_copy", "Intake {$number} description", $copy, 'textarea', 'Careers & intakes');
        }
        $fields[] = self::field('support_image', 'Career and intake image URL', $item['support_image']['src'], 'image', 'Careers & intakes');
        $fields[] = self::field('support_image_alt', 'Career and intake image alt text', $item['support_image']['alt'], 'text', 'Careers & intakes');
        $fields[] = self::field('support_image_caption', 'Career and intake image caption', 'Build experience, networks and career confidence in '.$name.'.', 'text', 'Careers & intakes');
        $fields[] = self::field('universities_kicker', 'Section eyebrow', 'University network', 'text', 'Universities');
        $fields[] = self::field('universities_title', 'Section title', 'Explore institutions in '.$name, 'text', 'Universities');
        $fields[] = self::field('universities_lead', 'Section description', 'Country-specific university options sourced from the Trans Globe destination network.', 'textarea', 'Universities');
        foreach ($universities as $index => $university) {
            $number = $index + 1;
            $fields[] = self::field("university_{$number}_name", "University {$number} name", $university['name'], 'text', 'Universities');
            $fields[] = self::field("university_{$number}_logo", "University {$number} logo URL", $university['logo'], 'image', 'Universities');
        }
        $fields[] = self::field('faq_kicker', 'Section eyebrow', 'Questions, answered', 'text', 'Frequently asked questions');
        $fields[] = self::field('faq_title', 'Section title', 'Study in '.$name.' FAQs', 'text', 'Frequently asked questions');
        foreach ($faqs as $index => [$question, $answer]) {
            $number = $index + 1;
            $fields[] = self::field("faq_{$number}_question", "Question {$number}", $question, 'text', 'Frequently asked questions');
            $fields[] = self::field("faq_{$number}_answer", "Answer {$number}", $answer, 'textarea', 'Frequently asked questions');
        }
        array_push($fields,
            self::field('cta_kicker', 'CTA eyebrow', 'Free counselling', 'text', 'Conversion CTA'),
            self::field('cta_title', 'CTA title', 'Ready to build your '.$name.' shortlist?', 'text', 'Conversion CTA'),
            self::field('cta_copy', 'CTA description', 'Bring your academic history, preferred course and budget. We’ll help you understand realistic university, intake and visa options.', 'textarea', 'Conversion CTA'),
            self::field('cta_label', 'CTA button label', 'Speak to a Counsellor', 'text', 'Conversion CTA'),
            self::field('contact_phone_label', 'Contact phone display', '+91 98266 66886', 'text', 'Conversion CTA'),
            self::field('contact_phone_link', 'Contact phone link', '+919826666886', 'text', 'Conversion CTA'),
            self::field('contact_email', 'Contact email', 'info@geic.in', 'text', 'Conversion CTA'),
            self::field('contact_address', 'Contact address', '503, THE VIEW Tower 1, Yeshwant Niwas Rd, Indore 452001', 'textarea', 'Conversion CTA'),
            self::field('form_disclaimer', 'Form disclaimer', 'University, visa and work-right rules can change; our counsellors will help you verify current official requirements.', 'textarea', 'Conversion CTA'),
        );

        return $fields;
    }

    private static function customPage(CmsPage $page): array
    {
        if ($page->group === 'promotions') {
            return self::promotionPage($page->page_key, $page->name, $page->description ?: 'Promotional campaign page');
        }

        return self::page($page->page_key, $page->name, $page->description ?: 'Custom website page', [
            self::field('hero_title', 'Hero title', $page->name),
            self::field('hero_copy', 'Hero description', $page->description ?: 'Add a clear introduction for this page.', 'textarea'),
            self::field('hero_image', 'Hero image URL', 'assets/services/expert-counselling.jpg', 'image'),
            self::field('content_title', 'Content heading', 'How we can help', 'text', 'Main content'),
            self::field('content_copy', 'Content text', 'Add the key information visitors need to understand this page and confidently take their next step.', 'textarea', 'Main content'),
            self::field('cta_title', 'Call-to-action heading', 'Ready to take the next step?', 'text', 'Call to action'),
            self::field('cta_copy', 'Call-to-action text', 'Speak with our Indore counselling team for clear, personal guidance.', 'textarea', 'Call to action'),
            self::field('cta_label', 'Button label', 'Speak to a Counsellor', 'text', 'Call to action'),
        ]);
    }

    private static function promotionPage(string $key, string $name, string $description): array
    {
        return self::page($key, $name, $description, [
            self::field('meta_title', 'Browser title', 'Trans Globe | Study Abroad with the Right Guidance', 'text', 'Page identity'),
            self::field('meta_description', 'Search description', 'Study abroad guidance for university selection, applications, visas, and pre-departure planning across leading global destinations.', 'textarea', 'Page identity'),
            self::field('logo_image', 'Header and footer logo', '/landing/assets/tg-logo.svg', 'image', 'Header & navigation'),
            self::field('utility_text', 'Top service line', 'University admissions · visas · pre-departure', 'text', 'Header & navigation'),
            self::field('header_cta', 'Header button label', 'Free profile review', 'text', 'Header & navigation'),
            self::field('hero_eyebrow', 'Hero eyebrow', 'Admissions · Visas · Pre-departure', 'text', 'Hero'),
            self::field('hero_title', 'Hero title', 'The right guidance for', 'text', 'Hero'),
            self::field('hero_title_accent', 'Hero highlighted title', 'your global journey.', 'text', 'Hero'),
            self::field('hero_copy', 'Hero description', 'Find the right university, destination, and career pathway with end-to-end guidance for Germany, the UK, Ireland, Italy, Spain, Australia, and beyond.', 'textarea', 'Hero'),
            self::field('hero_image', 'Hero image', '/landing/assets/hero-campus.webp', 'image', 'Hero'),
            self::field('hero_image_alt', 'Hero image alt text', 'International students walking together through a university campus', 'text', 'Hero'),
            self::field('hero_cta', 'Hero button label', 'Book free profile evaluation', 'text', 'Hero'),
            self::field('destinations_eyebrow', 'Destinations eyebrow', 'Explore your options', 'text', 'Destinations'),
            self::field('destinations_title', 'Destinations title', 'Where do you want to study?', 'text', 'Destinations'),
            self::field('destinations_copy', 'Destinations description', 'Compare destinations with your academic profile, budget, career goals, and post-study plans in mind. Choose a country to begin your free evaluation.', 'textarea', 'Destinations'),
            self::field('university_network_eyebrow', 'University network eyebrow', 'Global university network', 'text', 'University network'),
            self::field('university_network_title', 'University network title', 'Study with leading universities worldwide.', 'text', 'University network'),
            self::field('university_network_copy', 'University network description', 'Explore globally respected institutions across Australia, the UK, Europe, Canada, New Zealand, Dubai and beyond.', 'textarea', 'University network'),
            self::field('why_eyebrow', 'Benefits eyebrow', 'Why students choose us', 'text', 'Why choose us'),
            self::field('why_title', 'Benefits title', 'Clear advice for a decision that shapes your future.', 'text', 'Why choose us'),
            self::field('why_copy', 'Benefits description', 'Studying abroad is not one decision—it is a chain of important ones. We help you move through each stage with clarity, context, and a plan built around your profile.', 'textarea', 'Why choose us'),
            self::field('why_cta', 'Benefits button label', 'Talk to a counsellor', 'text', 'Why choose us'),
            self::field('benefit_one_title', 'Benefit 1 title', 'University & course guidance', 'text', 'Why choose us'),
            self::field('benefit_one_copy', 'Benefit 1 description', 'Build a shortlist around your academics, interests, budget, and long-term career goals.', 'textarea', 'Why choose us'),
            self::field('benefit_two_title', 'Benefit 2 title', 'Complete application support', 'text', 'Why choose us'),
            self::field('benefit_two_copy', 'Benefit 2 description', 'Move from profile review to documentation and university applications with a clear checklist.', 'textarea', 'Why choose us'),
            self::field('benefit_three_title', 'Benefit 3 title', 'Multiple study destinations', 'text', 'Why choose us'),
            self::field('benefit_three_copy', 'Benefit 3 description', 'Explore opportunities across Europe, the UK, Australia, the UAE, and other leading destinations.', 'textarea', 'Why choose us'),
            self::field('benefit_four_title', 'Benefit 4 title', 'Visa & documentation guidance', 'text', 'Why choose us'),
            self::field('benefit_four_copy', 'Benefit 4 description', 'Understand the steps, documents, and timelines involved in your destination’s visa process.', 'textarea', 'Why choose us'),
            self::field('benefit_five_title', 'Benefit 5 title', 'Pre-departure preparation', 'text', 'Why choose us'),
            self::field('benefit_five_copy', 'Benefit 5 description', 'Plan accommodation, insurance, travel, and destination-specific financial requirements.', 'textarea', 'Why choose us'),
            self::field('benefit_six_title', 'Benefit 6 title', 'Dedicated counsellor support', 'text', 'Why choose us'),
            self::field('benefit_six_copy', 'Benefit 6 description', 'Have one dependable point of contact throughout your study-abroad journey.', 'textarea', 'Why choose us'),
            self::field('journey_eyebrow', 'Journey eyebrow', 'Your application roadmap', 'text', 'Journey'),
            self::field('journey_title', 'Journey title', 'A complex process, made clear.', 'text', 'Journey'),
            self::field('journey_copy', 'Journey description', 'Every destination is different. Your plan should be too. We organise the moving parts into four clear stages.', 'textarea', 'Journey'),
            self::field('journey_one_title', 'Journey step 1 title', 'Evaluate', 'text', 'Journey'),
            self::field('journey_one_copy', 'Journey step 1 description', 'Review academics, goals, budget, experience, and eligibility.', 'textarea', 'Journey'),
            self::field('journey_two_title', 'Journey step 2 title', 'Shortlist', 'text', 'Journey'),
            self::field('journey_two_copy', 'Journey step 2 description', 'Compare countries, courses, universities, and realistic pathways.', 'textarea', 'Journey'),
            self::field('journey_three_title', 'Journey step 3 title', 'Apply', 'text', 'Journey'),
            self::field('journey_three_copy', 'Journey step 3 description', 'Prepare documents, submit applications, and manage offer decisions.', 'textarea', 'Journey'),
            self::field('journey_four_title', 'Journey step 4 title', 'Prepare', 'text', 'Journey'),
            self::field('journey_four_copy', 'Journey step 4 description', 'Plan finances, visa documentation, travel, and pre-departure steps.', 'textarea', 'Journey'),
            self::field('reviews_eyebrow', 'Reviews eyebrow', 'Google reviews', 'text', 'Reviews'),
            self::field('reviews_title', 'Reviews title', 'Real guidance, in our students’ own words.', 'text', 'Reviews'),
            self::field('reviews_copy', 'Reviews description', 'A selection of recent public reviews for Trans Globe Education Consultants, Indore. Use the arrows to read all ten.', 'textarea', 'Reviews'),
            self::field('team_eyebrow', 'Team eyebrow', 'Professional people', 'text', 'Team'),
            self::field('team_title', 'Team title', 'Meet our expert education consultants.', 'text', 'Team'),
            self::field('team_one_name', 'Team member 1 name', 'Johar Ali', 'text', 'Team'),
            self::field('team_one_role', 'Team member 1 role', 'Leadership team', 'text', 'Team'),
            self::field('team_one_image', 'Team member 1 image', '/landing/assets/johar-ali.webp', 'image', 'Team'),
            self::field('team_two_name', 'Team member 2 name', 'Ali', 'text', 'Team'),
            self::field('team_two_role', 'Team member 2 role', 'Student counsellor', 'text', 'Team'),
            self::field('team_two_image', 'Team member 2 image', '/landing/assets/ali.webp', 'image', 'Team'),
            self::field('team_three_name', 'Team member 3 name', 'Husain', 'text', 'Team'),
            self::field('team_three_role', 'Team member 3 role', 'Student counsellor', 'text', 'Team'),
            self::field('team_three_image', 'Team member 3 image', '/landing/assets/husain.webp', 'image', 'Team'),
            self::field('form_eyebrow', 'Form eyebrow', 'Free profile evaluation', 'text', 'Lead form'),
            self::field('form_title', 'Form title', 'Start with your profile. Build the right plan.', 'text', 'Lead form'),
            self::field('form_copy', 'Form description', 'Tell us where you are today. We’ll use your academics, preferences, and goals to help identify suitable countries, universities, and courses.', 'textarea', 'Lead form'),
            self::field('form_proof_one', 'Form proof point 1', 'No obligation', 'text', 'Lead form'),
            self::field('form_proof_two', 'Form proof point 2', 'Profile-based guidance', 'text', 'Lead form'),
            self::field('form_proof_three', 'Form proof point 3', 'Your information stays private', 'text', 'Lead form'),
            self::field('form_button', 'Form button label', 'Get my free profile evaluation', 'text', 'Lead form'),
            self::field('faq_eyebrow', 'FAQ eyebrow', 'Professional study abroad FAQs', 'text', 'FAQs'),
            self::field('faq_title', 'FAQ title', 'Questions are part of the process.', 'text', 'FAQs'),
            self::field('faq_copy', 'FAQ description', 'Here are clear answers to the questions students ask before they begin.', 'textarea', 'FAQs'),
            self::field('faq_one_question', 'FAQ 1 question', 'How do I determine which country and university suit my goals?', 'text', 'FAQs'),
            self::field('faq_one_answer', 'FAQ 1 answer', 'The right destination depends on your academic profile, preferred course, budget, career objectives, language proficiency, post-study opportunities, and eligibility. A profile-based assessment helps shortlist suitable universities and countries.', 'textarea', 'FAQs'),
            self::field('faq_two_question', 'FAQ 2 question', 'Can I study abroad with a less competitive academic profile?', 'text', 'FAQs'),
            self::field('faq_two_answer', 'FAQ 2 answer', 'Yes. Requirements vary by university, course, and country. Suitable pathways may consider academics, work experience, entrance tests, language scores, and other relevant factors.', 'textarea', 'FAQs'),
            self::field('faq_three_question', 'FAQ 3 question', 'What is the complete university application process?', 'text', 'FAQs'),
            self::field('faq_three_answer', 'FAQ 3 answer', 'It generally includes profile evaluation, course and university selection, eligibility checks, document preparation, applications, offer acceptance, financial planning, and visa processing.', 'textarea', 'FAQs'),
            self::field('faq_four_question', 'FAQ 4 question', 'How much financial planning is required?', 'text', 'FAQs'),
            self::field('faq_four_answer', 'FAQ 4 answer', 'Plan for tuition, living expenses, accommodation, insurance, travel, visa costs, and other fees. Depending on eligibility, scholarships, grants, or education loans may be available.', 'textarea', 'FAQs'),
            self::field('faq_five_question', 'FAQ 5 question', 'Can I apply to multiple countries or universities?', 'text', 'FAQs'),
            self::field('faq_five_answer', 'FAQ 5 answer', 'Yes. A balanced strategy can include ambitious, target, and safer options, provided each application meets the institution’s specific requirements and deadlines.', 'textarea', 'FAQs'),
            self::field('cta_eyebrow', 'Final CTA eyebrow', 'Your next chapter', 'text', 'Final CTA'),
            self::field('cta_title', 'Final CTA title', 'Your international education journey starts here.', 'text', 'Final CTA'),
            self::field('cta_copy', 'Final CTA description', 'Begin with a conversation. Leave with a clearer view of what is possible for your profile.', 'textarea', 'Final CTA'),
            self::field('cta_button', 'Final CTA button label', 'Book free counselling', 'text', 'Final CTA'),
            self::field('cta_whatsapp', 'WhatsApp button label', 'Chat on WhatsApp', 'text', 'Final CTA'),
            self::field('counsellor_one', 'Counsellor 1 contact', 'Counsellor 01 · +91 XXXX XXX XXX', 'text', 'Final CTA'),
            self::field('counsellor_two', 'Counsellor 2 contact', 'Counsellor 02 · +91 XXXX XXX XXX', 'text', 'Final CTA'),
            self::field('footer_copy', 'Footer introduction', 'Thoughtful guidance for students planning an international education.', 'textarea', 'Footer'),
            self::field('footer_address', 'Footer address', 'Office No. 503, The View Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001', 'textarea', 'Footer'),
            self::field('footer_email', 'Footer email', 'info@geic.in', 'text', 'Footer'),
            self::field('footer_phone', 'Footer phone', '+91 98266 66886', 'text', 'Footer'),
            self::field('footer_copyright', 'Footer copyright', 'Trans Globe. All rights reserved.', 'text', 'Footer'),
        ]);
    }

    private static function field(string $key, string $label, string $default, string $type = 'text', string $section = 'Hero'): array
    {
        return compact('key', 'label', 'default', 'type', 'section');
    }
}
