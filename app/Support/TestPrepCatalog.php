<?php

namespace App\Support;

class TestPrepCatalog
{
    public static function find(string $slug): ?array
    {
        $test = self::tests()[$slug] ?? null;

        return $test ? array_merge(self::defaults($slug), $test) : null;
    }

    public static function all(): array
    {
        return array_values(array_map(fn (string $slug) => self::find($slug), array_keys(self::tests())));
    }

    private static function defaults(string $slug): array
    {
        return [
            'slug' => $slug,
            'image' => 'assets/services/test-preparation.jpg',
            'image_alt' => 'Students preparing for an international entrance test',
            'support' => [
                ['Start with the right test', 'Match the test, target score and booking window to your course, destination and intake.'],
                ['Build confidence by skill', 'Follow a structured study plan with strategy, feedback and practice for every section.'],
                ['Know when you are ready', 'Use mock-test review to identify what to improve before you book or retake your exam.'],
            ],
            'faqs' => [
                ['Which test should I take?', 'The right test depends on your university, course, destination and visa pathway. We can help you check the requirements before you prepare or book.'],
                ['How early should I start preparing?', 'Start early enough to practise, sit the test and allow time for a second attempt if your preferred university or intake requires it.'],
                ['Can I book a counselling session first?', 'Yes. A free counselling conversation can help you choose the right test and create a practical timeline for your application.'],
            ],
        ];
    }

    private static function tests(): array
    {
        return [
            'ielts' => [
                'number' => '01', 'title' => 'IELTS', 'eyebrow' => 'English language proficiency',
                'summary' => 'Prepare for the International English Language Testing System with a score plan built around your study, work or migration goal.',
                'overview' => 'IELTS is one of the world’s most trusted English-proficiency tests for study, work and migration. It has Academic and General Training versions, so choosing the right route and score target is an important first step.',
                'image' => 'assets/services/test-preparation.jpg', 'image_alt' => 'Students preparing for English-language test coaching',
                'facts' => [['2 hr 45 min', 'Test duration'], ['Band 0–9', 'Score range'], ['2 years', 'Recommended validity']],
                'modules' => [
                    ['Listening', '40 questions across four recordings, using real-life and academic contexts.'],
                    ['Reading', '40 questions in 60 minutes; passages differ for Academic and General Training.'],
                    ['Writing', 'Two tasks in 60 minutes, with task types based on your chosen test version.'],
                    ['Speaking', 'A 11–14 minute, three-part face-to-face speaking test.'],
                ],
                'facts_note' => 'A valid passport is needed to book IELTS and for test-day identification. The source page notes that IELTS is generally not recommended for candidates under 16.',
            ],
            'toefl' => [
                'number' => '02', 'title' => 'TOEFL', 'eyebrow' => 'Academic English for global admissions',
                'summary' => 'Build the academic English skills and test strategy needed for TOEFL iBT university applications.',
                'overview' => 'TOEFL measures reading, listening, speaking and writing in an academic context. It is commonly used by universities and institutions around the world to understand whether you are ready to study in English.',
                'image' => 'assets/services/university-admissions.jpg', 'image_alt' => 'Students preparing for university admissions and English testing',
                'facts' => [['0–120', 'Total score'], ['Under 2 hrs', 'Test time'], ['2 years', 'Score validity']],
                'modules' => [
                    ['Reading', '28 questions in 36 minutes, based on academic passages.'],
                    ['Listening', '20 questions in 35 minutes to assess your understanding of spoken academic English.'],
                    ['Writing', 'Four tasks in 16 minutes that test how clearly you can express an academic response.'],
                    ['Speaking', 'Two tasks in 29 minutes that assess spoken English for an academic setting.'],
                ],
                'facts_note' => 'Trans Globe’s TOEFL page states that scores are typically available in your ETS account within 4–8 days and the test may be taken again after three days.',
            ],
            'pte' => [
                'number' => '03', 'title' => 'PTE', 'eyebrow' => 'Computer-based English proficiency',
                'summary' => 'Prepare for PTE Academic with focused practice for the computer-based tasks used for study, work and migration pathways.',
                'overview' => 'PTE Academic is a computer-based English-proficiency test that evaluates how you use English in realistic academic tasks. Its integrated tasks assess the skills many universities and global pathways require.',
                'image' => 'assets/services/expert-counselling.jpg', 'image_alt' => 'Students receiving focused test-preparation guidance',
                'facts' => [['About 2 hrs', 'Test duration'], ['10–90', 'Score range'], ['2 years', 'Score validity']],
                'modules' => [
                    ['Speaking + Writing', 'Computer-based tasks that assess fluency, pronunciation, grammar, vocabulary and writing structure.'],
                    ['Reading', 'Academic text and sentence tasks focused on meaning, flow and comprehension.'],
                    ['Listening', 'Questions based on spoken English in lectures and conversations.'],
                ],
                'facts_note' => 'The source page says many PTE candidates receive results within approximately 48 hours, though result timing can vary.',
            ],
            'gre' => [
                'number' => '04', 'title' => 'GRE', 'eyebrow' => 'Graduate and business-school admissions',
                'summary' => 'Strengthen the Verbal, Quantitative and Analytical Writing skills assessed by the GRE General Test.',
                'overview' => 'The GRE General Test is used by many graduate, business and law programmes. You can take it at a test centre or, where offered, as a proctored at-home test.',
                'image' => 'assets/services/sop-documentation.jpg', 'image_alt' => 'Student working on graduate entrance-test preparation',
                'facts' => [['About 2 hrs', 'Test duration'], ['5 sections', 'Test format'], ['5 years', 'Score availability']],
                'modules' => [
                    ['Analytical Writing', 'One 30-minute task requiring you to analyse an issue.'],
                    ['Verbal Reasoning', 'Two sections that test reading, vocabulary and reasoning with written material.'],
                    ['Quantitative Reasoning', 'Two sections that test problem-solving and quantitative comparison skills.'],
                ],
                'facts_note' => 'According to the source page, the GRE may be retaken once every 21 days, up to five times within 12 months.',
            ],
            'gmat' => [
                'number' => '05', 'title' => 'GMAT', 'eyebrow' => 'Business-school entrance exam',
                'summary' => 'Prepare for the GMAT with targeted Quantitative, Verbal and Data Insights practice for business-school applications.',
                'overview' => 'GMAT is designed specifically for graduate business education. It is used by business schools to assess the quantitative, verbal and data-analysis skills that support MBA and business master’s study.',
                'image' => 'assets/services/loans-financial-guide.jpg', 'image_alt' => 'Data and financial planning supporting business-school test preparation',
                'facts' => [['2 hr 15 min', 'Exam duration'], ['205–805', 'Total score'], ['5 years', 'Score validity']],
                'modules' => [
                    ['Quantitative Reasoning', '21 questions in 45 minutes on arithmetic and algebra through problem-solving tasks.'],
                    ['Verbal Reasoning', '23 questions in 45 minutes focused on reading comprehension and critical reasoning.'],
                    ['Data Insights', '20 questions in 45 minutes interpreting tables, graphs and real-world business scenarios.'],
                ],
                'facts_note' => 'The current GMAT format has three sections. The source page notes that you can choose the section order and review or edit questions using test-taker tools.',
            ],
            'sat' => [
                'number' => '06', 'title' => 'SAT', 'eyebrow' => 'Undergraduate college admissions',
                'summary' => 'Prepare for the Digital SAT with a clear plan for its Reading & Writing and Math sections.',
                'overview' => 'SAT is a globally recognised undergraduate entrance test. It assesses college-readiness skills and can strengthen applications to universities and support scholarship consideration.',
                'image' => 'assets/services/scholarship-guidance.jpg', 'image_alt' => 'Student studying in a library for undergraduate admissions',
                'facts' => [['2 hr 15 min', 'Exam duration'], ['400–1600', 'Total score'], ['Digital', 'Test format']],
                'modules' => [
                    ['Reading & Writing', '64 minutes of short passages and focused questions on comprehension, grammar, vocabulary and data interpretation.'],
                    ['Math', '70 minutes covering core school-level maths, with the built-in Desmos calculator or an approved calculator available.'],
                ],
                'facts_note' => 'An acceptable photo ID is required under College Board guidelines. Trans Globe’s page notes that SAT scores remain on record, with extra context added when sending scores after five years.',
            ],
            'german' => [
                'number' => '07', 'title' => 'German', 'eyebrow' => 'Language proof for German study',
                'summary' => 'Build German-language confidence for study, professional pathways and recognised examinations such as Goethe, TestDaF, telc and DSH.',
                'overview' => 'German proficiency is often needed for German-taught programmes, certain visa situations and professional paths. The required certificate depends on your goal, university and the language level it specifies.',
                'image' => 'assets/services/pre-post-departure.jpg', 'image_alt' => 'World map and travel planning for a study journey to Germany',
                'facts' => [['A1–C2', 'CEFR levels'], ['B1 or B2', 'Common programme level'], ['4 skills', 'Language areas']],
                'modules' => [
                    ['Goethe-Zertifikat', 'A level-based A1–C2 exam structured for practical, real-life language use.'],
                    ['TestDaF', 'A university-focused exam assessing reading, listening, writing and speaking, with TDN 3–5 results.'],
                    ['telc Deutsch C1 Hochschule', 'A higher-education language test with receptive and productive components.'],
                    ['DSH', 'A German university entrance exam with written and oral elements and DSH I/II/III proficiency levels.'],
                ],
                'facts_note' => 'Trans Globe notes that Goethe certificates are valid indefinitely. We help students choose the exam that matches their institution and intended course.',
            ],
            'duolingo' => [
                'number' => '08', 'title' => 'Duolingo English Test', 'card_title' => 'DET', 'eyebrow' => 'Online adaptive English test',
                'summary' => 'Prepare for the Duolingo English Test, an online adaptive exam that can be taken from home for many study-abroad applications.',
                'overview' => 'The Duolingo English Test (DET) is a computer-adaptive English-proficiency test that you can take online from home. It is accepted by thousands of institutions worldwide, but you should always confirm the policy of each university on your shortlist.',
                'image' => 'assets/services/expert-counselling.jpg', 'image_alt' => 'Student receiving guidance for an online English test',
                'facts' => [['About 1 hr', 'Test duration'], ['10–160', 'Score range'], ['2 years', 'Result validity']],
                'modules' => [
                    ['Adaptive test', 'A computer-adaptive assessment of English skills, followed by a writing and speaking sample.'],
                    ['Literacy', 'Reading and writing tasks covering vocabulary, grammar, spelling, sentence completion and comprehension.'],
                    ['Comprehension', 'A score area reflecting how well you understand written and spoken English.'],
                    ['Conversation + Production', 'Score areas reflecting your ability to communicate and produce English responses.'],
                ],
                'facts_note' => 'The Trans Globe source page reports results usually within 48 hours. Check each university’s current DET acceptance policy before booking.',
            ],
        ];
    }
}
