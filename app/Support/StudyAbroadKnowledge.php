<?php

namespace App\Support;

/**
 * Curated study-abroad guidance distilled from public consultancy workflows
 * and official education/immigration guidance. This is intentionally kept as
 * principles and checklists rather than copied marketing content.
 */
class StudyAbroadKnowledge
{
    public static function context(): string
    {
        return <<<'KNOWLEDGE'
Planning framework:
- Start with the student's academic history, intended career, budget, preferred start date and destination preferences.
- Compare course fit, entry requirements, total cost, city/lifestyle, employability and realistic admission outcomes before naming universities.
- Build a balanced shortlist: ambitious choices, realistic choices and safer options. Never present admission or visa approval as guaranteed.
- Check each institution's official course page for current fees, prerequisites, accepted English tests, intake dates, portfolio rules and application deadlines.
- Prepare a document checklist: passport, academic transcripts/certificates, English-test result, CV, statement of purpose, references, work evidence and financial documents where required.
- Submit complete applications through the institution's official portal or the authorised application route, then keep confirmation numbers and deadline reminders.
- After an offer, review conditions, tuition deposit, scholarship terms, enrolment confirmation and refund policy before accepting.
- Visa preparation follows the offer: use the destination authority's current checklist, prove funds and genuine study purpose where required, arrange insurance and never rely on future part-time work to meet a financial requirement.
- Finish with accommodation, travel, health cover, document copies, arrival planning and an emergency contact plan.

Profile questions to ask:
- What is your highest qualification, percentage/CGPA and graduation year?
- Which course or career direction are you targeting, and what work experience do you have?
- Which destinations and intake are you considering?
- What is your tuition and living-cost budget, and will you use savings, a loan, sponsor or scholarship?
- Which English/entrance tests have you taken, with what scores and dates?

Scholarship guidance:
- Look first at university, government, regional, external and course-specific awards.
- Track eligibility, amount, separate application requirements and closing date in a shortlist; some awards are automatic consideration while others require a separate application.
- Strong academic results, leadership, research, community work, a focused personal statement and early submission can help, but no award is guaranteed.
- Treat “100% scholarship” or “guaranteed funding” claims as unverified until confirmed on the awarding body’s official page.

Application-quality checks:
- Keep names, dates, grades and passport details identical across every form and document.
- Explain a clear link between previous study, the chosen course and the intended career.
- Use the student's own truthful experience in SOPs and declarations; do not fabricate work, funds, test results or documents.
- Confirm translations, certification, file formats, references and portfolio requirements before upload.

Safe visa guidance:
- Visa rules, funds, work rights, processing times and document lists change by destination, passport, provider and intake.
- Give high-level preparation guidance only, link the student to the official immigration authority and recommend a licensed/authorised adviser for case-specific immigration advice.
- An admission offer, test score or consultancy support does not guarantee a visa.

Reference sources reviewed (for process patterns and verification): IDP's “How do I apply”, AECC's university application process, SI-UK's education guide, EducationUSA's “5 Steps to U.S. Study”, GOV.UK Student visa guidance, Study Australia/Home Affairs, IRCC Canada and Immigration New Zealand.
KNOWLEDGE;
    }

    public static function sources(): array
    {
        return [
            'IDP Education — application steps' => 'https://www.idp.com/stage-details/how-do-i-apply/',
            'AECC — international university application process' => 'https://aeccglobal.ng/student-services/application-process',
            'SI-UK — education guide' => 'https://static.studyin-uk.com/assets/documents/education-guide-2019.pdf',
            'EducationUSA — five steps to U.S. study' => 'https://educationusa.state.gov/your-5-steps-us-study',
            'GOV.UK — Student visa course and money' => 'https://www.gov.uk/student-visa/course',
            'Study Australia — visa application process' => 'https://www.studyaustralia.gov.au/en/plan-your-move/visa-application-process',
            'Canada IRCC — study permit documents' => 'https://www.canada.ca/en/immigration-refugees-citizenship/services/study-canada/study-permit/get-documents.html',
            'Immigration New Zealand — student visa' => 'https://www.immigration.govt.nz/visas/fee-paying-student-visa/',
        ];
    }
}
