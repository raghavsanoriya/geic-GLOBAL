export interface VisaRoadmapStep {
  stepNumber: number;
  title: string;
  shortDesc: string;
  duration: string;
  details: string;
  requiredDocuments: string[];
  indoreCounsellorTip: string;
  isCrucial?: boolean;
}

export type DocumentCategory = 
  | 'Academic & Test Scores' 
  | 'Financial & Sponsorship' 
  | 'Identity & Civil' 
  | 'Health & Insurance' 
  | 'Consular & Legal';

export interface DestinationRequiredDocument {
  id: string;
  name: string;
  category: DocumentCategory;
  isMandatory: boolean;
  notes: string;
  indoreTip?: string;
}

export interface CountryVisaRoadmap {
  country: string;
  flag: string;
  visaName: string;
  processingTime: string;
  transGlobeSuccessRate: string;
  fundsRequirement: string;
  workRights: string;
  overview: string;
  requiredDocumentsChecklist: DestinationRequiredDocument[];
  steps: VisaRoadmapStep[];
}

export const VISA_ROADMAP_DATA: Record<string, CountryVisaRoadmap> = {
  Australia: {
    country: 'Australia',
    flag: '🇦🇺',
    visaName: 'Subclass 500 Student Visa',
    processingTime: '20 - 45 days',
    transGlobeSuccessRate: '99.4%',
    fundsRequirement: 'Living costs (AUD $29,710/yr) + 1 yr tuition + OSHC health cover',
    workRights: '48 hours per fortnight during study; unlimited during breaks',
    overview: 'Australia uses a streamlined risk-tier framework. Success heavily relies on Genuine Student (GS) compliance and authentic financial sponsorship.',
    requiredDocumentsChecklist: [
      {
        id: 'aus_doc_passport',
        name: 'Valid Passport (Current & Old)',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Minimum 6 months validity from planned arrival; color scan of all pages with visas/stamps.',
        indoreTip: 'Include expired previous passports if they contain travel history to Singapore, UAE, or Schengen.'
      },
      {
        id: 'aus_doc_coe',
        name: 'Confirmation of Enrolment (CoE)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Official electronic certificate issued via PRISMS by your registered Australian university.',
        indoreTip: 'CRICOS course code on CoE must precisely match your offer letter.'
      },
      {
        id: 'aus_doc_academic',
        name: 'Academic Transcripts & Certificates',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: '10th, 12th marksheets, semester-wise degree marksheets, consolidated transcript, and degree certificate.',
        indoreTip: 'CBSE / MP Board certificates should be verified; Trans Globe assists with university attestation.'
      },
      {
        id: 'aus_doc_english',
        name: 'IELTS / PTE / TOEFL Scorecard',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'PTE Academic (min 58 overall) or IELTS Academic (min 6.0/6.5 overall) taken within 2 years.',
        indoreTip: 'Online at-home versions are not accepted by Home Affairs for Subclass 500.'
      },
      {
        id: 'aus_doc_gs',
        name: 'Genuine Student (GS) Statement',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Targeted answers explaining home ties, career valuation in India, and course alignment.',
        indoreTip: 'Trans Globe Indore senior visa managers review your GS draft to eliminate generic copy-paste statements that trigger refusals.'
      },
      {
        id: 'aus_doc_funds',
        name: 'Bank Balance & 3-Month Bank Statements',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Liquid funds covering AUD $29,710 living expenses + remaining 1st year tuition fee + AUD $2,500 travel.',
        indoreTip: 'Savings accounts must show steady funds without unexplained sudden large lump-sum deposits.'
      },
      {
        id: 'aus_doc_loan',
        name: 'Approved Education Loan Sanction Letter',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Must be from an approved nationalized bank or reputable NBFC (Credila, Avanse, Auxilo).',
        indoreTip: 'Letter must clearly state disbursement conditions without restrictive unfulfilled clauses.'
      },
      {
        id: 'aus_doc_itr',
        name: 'ITR Returns & Form 16 of Sponsors (3 Years)',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Income Tax Return Acknowledgements of parents/sponsors with computation of income.',
        indoreTip: 'For agricultural families in MP, certified Khasra/Khatoni records with translation are needed.'
      },
      {
        id: 'aus_doc_affidavit',
        name: 'Affidavit of Financial Support & CA Report',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Notarized sponsorship affidavit on stamp paper + Chartered Accountant Net Worth certificate.',
        indoreTip: 'We provide pre-formatted legal templates compliant with Australian Home Affairs standards.'
      },
      {
        id: 'aus_doc_oshc',
        name: 'OSHC Health Insurance Certificate',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Continuous policy covering entire course duration plus 2-3 months buffer from approved provider (Bupa, Allianz, Medibank).',
        indoreTip: 'Ensure policy start date precedes your planned flight date by at least 14 days.'
      },
      {
        id: 'aus_doc_emedical',
        name: 'HAP ID Medical Examination Sheet',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Medical clearance from Australian panel physician (Chest X-ray, Creatinine, Medical exam).',
        indoreTip: 'Complete upfront medicals prior to visa lodgement to fast-track your grant.'
      },
      {
        id: 'aus_doc_workexp',
        name: 'Work Experience & Gap Explanation',
        category: 'Consular & Legal',
        isMandatory: false,
        notes: 'Appointment letters, relieving letters, and 6 months salary pay-slips with bank credit proof if having study gaps.',
        indoreTip: 'Any career gap over 3 months must have formal documentary backing to satisfy GS criteria.'
      }
    ],
    steps: [
      {
        stepNumber: 1,
        title: 'Offer Acceptance & CoE Issuance',
        shortDesc: 'Pay initial deposit & receive Confirmation of Enrolment (CoE)',
        duration: '3 - 7 days',
        details: 'After receiving a conditional/unconditional offer, accept your seat by paying the university tuition deposit (typically AUD $5,000 - $15,000) and purchasing Overseas Student Health Cover (OSHC). The university issues the electronic Confirmation of Enrolment (CoE).',
        requiredDocuments: ['Signed Acceptance Agreement', 'Deposit Wire Receipt (Flywire/Convera)', 'OSHC Certificate'],
        indoreCounsellorTip: 'Always verify CRICOS code on your CoE matches your course exactly before paying.',
      },
      {
        stepNumber: 2,
        title: 'Financial Matrix & Sponsorship',
        shortDesc: 'Organize 3 months savings or approved education loan',
        duration: '7 - 14 days',
        details: 'Prepare proof of liquid funds covering first-year tuition plus living expenses (AUD $29,710) and travel allowance. Funds can be from parents, approved nationalized banks, or sanction letters from Credila/Avanse.',
        requiredDocuments: ['3-6 Months Bank Statements', 'Loan Sanction Letter', 'Affidavit of Sponsorship', 'ITR returns (3 years) of sponsors'],
        indoreCounsellorTip: 'Central India students using agricultural income must provide authentic Patwari records (Khasra/Khatoni) with English translation.',
        isCrucial: true,
      },
      {
        stepNumber: 3,
        title: 'Genuine Student (GS) Statement',
        shortDesc: 'Draft targeted Genuine Student criterion narrative',
        duration: '4 - 6 days',
        details: 'The GS statement replaced GTE in 2024. It requires concise responses detailing: your circumstances in India, why you chose Australia over home study, realistic career value in India post-graduation, and immigration history.',
        requiredDocuments: ['GS Statement (max 150 words per question)', 'Employment letters (if career gap)', 'Course curriculum comparison'],
        indoreCounsellorTip: 'Trans Globe Indore senior visa managers review your GS draft to eliminate generic copy-paste statements that trigger refusals.',
        isCrucial: true,
      },
      {
        stepNumber: 4,
        title: 'HAP ID Generation & Medical Exam',
        shortDesc: 'Complete health checkup at panel physician',
        duration: '2 - 4 days',
        details: 'Generate a HAP ID via ImmiAccount and schedule medical examinations (Chest X-ray, serum creatinine, general physical) with an authorized Australian panel clinic (e.g. in Indore/Bhopal/Mumbai).',
        requiredDocuments: ['Valid Passport', 'HAP Referral Letter', 'Vaccination records'],
        indoreCounsellorTip: 'Complete medicals before lodging visa (upfront medicals) to fast-track your grant by up to 2 weeks.',
      },
      {
        stepNumber: 5,
        title: 'ImmiAccount Filing & Biometrics',
        shortDesc: 'Submit visa file online & provide VFS biometrics',
        duration: '1 - 3 days',
        details: 'Upload verified PDFs to your Home Affairs ImmiAccount, pay the visa fee (AUD $1,600), and schedule your biometrics appointment at VFS Global (Indore or nearest centre).',
        requiredDocuments: ['VFS Appointment Slip', 'Passport', 'Biometric collection letter'],
        indoreCounsellorTip: 'Double check every PDF is color-scanned and under 5MB per upload without password encryption.',
      },
      {
        stepNumber: 6,
        title: 'Visa Assessment & Electronic Grant',
        shortDesc: 'Department assessment & grant notification letter',
        duration: '15 - 35 days',
        details: 'Case officer reviews your application. You receive an electronic Visa Grant Notification letter via email linked to your passport number—no physical stamp needed!',
        requiredDocuments: ['Visa Grant Letter (VEVO checkable)', 'Flight tickets', 'Forex Card'],
        indoreCounsellorTip: 'Celebrate with Trans Globe! Attend our Indore Pre-Departure Briefing for airport transit and accommodation booking tips.',
      },
    ],
  },
  'United Kingdom': {
    country: 'United Kingdom',
    flag: '🇬🇧',
    visaName: 'UK Student Route (Point-Based System)',
    processingTime: '15 - 21 days (Priority: 5 days)',
    transGlobeSuccessRate: '99.1%',
    fundsRequirement: 'Tuition balance + £1,023/mo (outside London) or £1,334/mo (inside London) for 9 months',
    workRights: '20 hours/week during term; 2-year Graduate Route post-study work visa',
    overview: 'The UK points-based system requires 70 points (50 points for CAS, 10 for Financial Requirement, 10 for English). Fast-track priority options are available.',
    requiredDocumentsChecklist: [
      {
        id: 'uk_doc_passport',
        name: 'Valid International Passport',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Must have at least 1 blank page on both sides for visa vignette stamping.',
        indoreTip: 'Ensure name matches CAS exactly—discrepancies between given name and surname cause delays.'
      },
      {
        id: 'uk_doc_cas',
        name: 'Confirmation of Acceptance for Studies (CAS)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Official 14-character alphanumeric reference code issued by your sponsor university.',
        indoreTip: 'Valid for 6 months once issued. Check tuition fees paid and course start dates.'
      },
      {
        id: 'uk_doc_academic',
        name: 'Transcripts & Certificates listed on CAS',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Degree certificates, marksheets, and school completion certificates specifically mentioned in the CAS statement.',
        indoreTip: 'Carry original physical documents to VFS appointment along with scanned copies.'
      },
      {
        id: 'uk_doc_english',
        name: 'English Language Test / CAS Exemption',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'SELT certificate (IELTS for UKVI, PTE Academic UKVI) or explicit English exemption stated in CAS.',
        indoreTip: 'If your university assessed your English through Class 12th marks, verify the exact wording on CAS.'
      },
      {
        id: 'uk_doc_28days',
        name: '28-Day Bank Statement / Certificate',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Living costs (£9,207 or £12,006) + remaining tuition held consecutively for 28 days without dipping.',
        indoreTip: 'Crucial: The closing date of the 28-day period must be within 31 days of your online visa fee payment.'
      },
      {
        id: 'uk_doc_consent',
        name: 'Parental Consent Letter & Birth Certificate',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Required if funds are in parents bank accounts rather than student’s own name.',
        indoreTip: 'Original birth certificate in English (or sworn legal translation) is strictly mandatory.'
      },
      {
        id: 'uk_doc_tb',
        name: 'Tuberculosis (TB) Test Certificate',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Issued by an IOM/UKVI-approved diagnostic clinic following chest X-ray screening.',
        indoreTip: 'Valid for 6 months. Trans Globe coordinates fast-track appointments at approved clinics.'
      },
      {
        id: 'uk_doc_ihs',
        name: 'Immigration Health Surcharge (IHS) Receipt',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Mandatory NHS fee (£776/year) paid during online visa lodgement.',
        indoreTip: 'Keep your IHS reference number handy—it is required to generate the visa appointment pack.'
      },
      {
        id: 'uk_doc_atas',
        name: 'ATAS Clearance Certificate',
        category: 'Consular & Legal',
        isMandatory: false,
        notes: 'Academic Technology Approval Scheme certificate for sensitive postgraduate STEM / Engineering programs.',
        indoreTip: 'ATAS processing can take 4-6 weeks; apply immediately after receiving your conditional offer.'
      },
      {
        id: 'uk_doc_credibility',
        name: 'Credibility Interview Dossier',
        category: 'Consular & Legal',
        isMandatory: false,
        notes: 'Course structure syllabus, modules, tuition comparison, and post-study career trajectory.',
        indoreTip: 'UKVI conducts random automated video credibility interviews. Trans Globe runs prep mocks.'
      },
      {
        id: 'uk_doc_vfs',
        name: 'VFS Document Upload Confirmation & Appointment Sheet',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Confirmation sheet with barcode confirming digital document submission to VFS portal.',
        indoreTip: 'Take printed appointment confirmation and original passport to VFS Indore or Bhopal.'
      }
    ],
    steps: [
      {
        stepNumber: 1,
        title: 'CAS Request & Verification',
        shortDesc: 'Receive Confirmation of Acceptance for Studies (CAS)',
        duration: '5 - 10 days',
        details: 'After meeting academic and financial conditions, your UK university generates a unique 14-digit electronic CAS number. Check course dates, fees paid, and IELTS waiver clauses.',
        requiredDocuments: ['Unconditional Offer Letter', 'Fee deposit receipt (£2,000 - £5,000)', 'Degree Certificate & Transcripts'],
        indoreCounsellorTip: 'Ensure your name on CAS exactly matches your passport format (Given Name vs Surname).',
      },
      {
        stepNumber: 2,
        title: '28-Day Financial Holding Rule',
        shortDesc: 'Keep required balance untouched for 28 consecutive days',
        duration: '28+ days',
        details: 'Maintenance funds (£9,207 outside London, £12,006 inside London) + remaining first-year tuition must be held in personal savings accounts or nationalized bank fixed deposits without dipping below the threshold for even one day.',
        requiredDocuments: ['28-day Bank Statement/Certificate', 'Parental Consent Letter & Birth Certificate (if using parents funds)'],
        indoreCounsellorTip: 'Statements must not be older than 31 days on the date of online fee payment.',
        isCrucial: true,
      },
      {
        stepNumber: 3,
        title: 'Tuberculosis (TB) Screening',
        shortDesc: 'Mandatory test at IOM/UKVI-approved clinic',
        duration: '1 - 2 days',
        details: 'Indian citizens staying in the UK over 6 months must obtain a TB clearance certificate from an accredited UKVI clinic (e.g. Indore/Bhopal/Mumbai).',
        requiredDocuments: ['Passport (original + 2 copies)', 'TB clinic appointment receipt', '2 passport photos'],
        indoreCounsellorTip: 'Book early during peak August-September seasons as appointments fill quickly.',
      },
      {
        stepNumber: 4,
        title: 'Online UKVI Application & IHS Payment',
        shortDesc: 'Submit visa form & pay Immigration Health Surcharge',
        duration: '1 - 2 days',
        details: 'Fill the official UKVI visa form online, pay the visa fee (£490) and the mandatory Immigration Health Surcharge (IHS) giving full NHS healthcare access during your study.',
        requiredDocuments: ['Completed online application form', 'IHS payment reference receipt'],
        indoreCounsellorTip: 'Priority visa option (£500 extra) provides decisions within 5 working days if your intake is close.',
      },
      {
        stepNumber: 5,
        title: 'VFS Biometrics & Document Scanning',
        shortDesc: 'Visit VFS Indore / Bhopal for photo & fingerprints',
        duration: '1 day',
        details: 'Attend your booked appointment at VFS Global to submit biometrics and have your passport retained for vignette affixing.',
        requiredDocuments: ['Appointment Confirmation', 'Checklist sheet', 'Original Passport'],
        indoreCounsellorTip: 'Opt for courier delivery of your passport to avoid a second trip to the visa centre.',
      },
      {
        stepNumber: 6,
        title: 'Vignette Sticker & Digital eVisa',
        shortDesc: '90-day travel vignette issued in passport',
        duration: '10 - 15 days',
        details: 'Receive your stamped passport with 90-day entry vignette, alongside instructions to access your digital eVisa / BRP collection upon UK arrival.',
        requiredDocuments: ['Decision letter with BRP collection point', 'Stamped passport'],
        indoreCounsellorTip: 'Trans Globe Indore coordinates your London/Manchester airport pickup and student accommodation lease.',
      },
    ],
  },
  Germany: {
    country: 'Germany',
    flag: '🇩🇪',
    visaName: 'German National Student Visa (Type D)',
    processingTime: '4 - 8 weeks',
    transGlobeSuccessRate: '98.8%',
    fundsRequirement: 'Blocked Account (€11,904/year in Coracle/Expatrio) + Health Insurance',
    workRights: '140 full days or 280 half days per year; 18-month job search post-study visa',
    overview: 'Germany provides world-renowned tuition-free public universities. The mandatory Academic Evaluation Centre (APS) certificate is the essential prerequisite before visa filing.',
    requiredDocumentsChecklist: [
      {
        id: 'de_doc_passport',
        name: 'Valid Passport (Original + 2 Certified Copies)',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Validity must extend at least 12 months from travel date; 2 copies of data and signature pages.',
        indoreTip: 'German consulates in Mumbai/Delhi require clean copies without staples or punch holes.'
      },
      {
        id: 'de_doc_aps',
        name: 'Original APS Certificate (Akademische Prüfstelle)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Verification certificate from APS India New Delhi authenticating Indian university credentials.',
        indoreTip: 'Mandatory before any German student visa slot can be booked; apply 4 months in advance.'
      },
      {
        id: 'de_doc_zulassung',
        name: 'University Admission Letter (Zulassungsbescheid)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Direct unconditional admission letter (or conditional admission plus preparatory German course confirmation).',
        indoreTip: 'Check whether language of instruction is specified as 100% English.'
      },
      {
        id: 'de_doc_academics',
        name: 'Attested Academic Degrees & Marksheets',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'All university transcripts, 10th, 12th marksheets, and degree passing certificates.',
        indoreTip: 'Trans Globe assists with German consulate notarization and apostille stamp verification.'
      },
      {
        id: 'de_doc_sperrkonto',
        name: 'Blocked Account Confirmation (€11,904 Sperrkonto)',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Official 06-page confirmation letter from approved providers (Expatrio, Coracle, or Fintiba).',
        indoreTip: 'Account holder must be the student. Trans Globe provides direct 0-fee setup support.'
      },
      {
        id: 'de_doc_insurance',
        name: 'Statutory Health Insurance (TK / Barmer) & Travel Cover',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Official confirmation letter from public health fund + incoming travel medical insurance (€30,000).',
        indoreTip: 'Package deals via Expatrio/Coracle bundle health insurance with the blocked account.'
      },
      {
        id: 'de_doc_videx',
        name: '2 Signed VIDEX National Visa Application Forms',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Completed electronically on the VIDEX portal and printed with clear 2D barcode sheets.',
        indoreTip: 'All address dates in Germany and intended stay dates must be consistent.'
      },
      {
        id: 'de_doc_declaration',
        name: 'Declaration of Accuracy (Sections 53 & 54 AufenthG)',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Signed legal declaration regarding truthful statements to consular authorities.',
        indoreTip: 'Available on German consulate portal; must be signed in blue ink.'
      },
      {
        id: 'de_doc_motivation',
        name: 'Detailed Academic Motivation Letter',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Comprehensive letter detailing reasons for chosen German university, curriculum, and post-study career.',
        indoreTip: 'German consuls assess whether your chosen course logically builds on your Indian bachelor degree.'
      },
      {
        id: 'de_doc_europass',
        name: 'Europass Curriculum Vitae (CV)',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Formatted according to official European Europass standard without gaps in chronology.',
        indoreTip: 'Include technical project abstracts, software proficiencies, and internship summaries.'
      },
      {
        id: 'de_doc_language',
        name: 'Language Proficiency Scorecard (IELTS / Goethe)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'IELTS Academic (min 6.5) or German Goethe-Zertifikat B1/B2 if program involves German modules.',
        indoreTip: 'Medium of Instruction (MOI) letters are only accepted if specifically approved by the consulate.'
      },
      {
        id: 'de_doc_photos',
        name: '3 Biometric Photos (German Consulate Specs)',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: '35x45 mm, light gray background, neutral expression, 80% face coverage.',
        indoreTip: 'Trans Globe Indore has sample templates for local photographers to follow exactly.'
      }
    ],
    steps: [
      {
        stepNumber: 1,
        title: 'APS Certificate Issuance (Crucial First Step)',
        shortDesc: 'Document verification by German Embassy Academic Evaluation Centre',
        duration: '3 - 6 weeks',
        details: 'All Indian students wishing to study in Germany must get their degrees and marksheets authenticated by the APS India office in New Delhi before any visa slot can be booked.',
        requiredDocuments: ['Printed APS application', 'Attested marksheets & degree', 'DigiLocker verification or professor verification'],
        indoreCounsellorTip: 'Apply for APS 4 months before your university application deadline—backlogs can occur.',
        isCrucial: true,
      },
      {
        stepNumber: 2,
        title: 'University Admission (Zulassungsbescheid)',
        shortDesc: 'Secure unconditional admit letter from German public/private uni',
        duration: '2 - 6 weeks',
        details: 'Apply via Uni-Assist or direct university portals using your APS certificate to receive your admission letter (Zulassung).',
        requiredDocuments: ['Admission Letter (Zulassung)', 'English proficiency (IELTS/Medium of Instruction certificate)'],
        indoreCounsellorTip: 'Public universities charge zero tuition—only semester tickets (€150 - €350/sem).',
      },
      {
        stepNumber: 3,
        title: 'Sperrkonto (Blocked Account) & Health Cover',
        shortDesc: 'Transfer €11,904 into verified German escrow account',
        duration: '3 - 5 days',
        details: 'Open an escrow blocked account through certified providers (Expatrio, Coracle, or Fintiba) and transfer €11,904 (€992/month living expense). Secure statutory health insurance (TK or Barmer).',
        requiredDocuments: ['Sperrkonto confirmation document', 'Statutory/Private Travel & Health Insurance letter'],
        indoreCounsellorTip: 'Trans Globe provides direct zero-fee blocked account setup with instant remittance tracking.',
        isCrucial: true,
      },
      {
        stepNumber: 4,
        title: 'Videx Portal & Motivation Letter',
        shortDesc: 'Fill national visa Videx form & German motivation letter',
        duration: '3 - 5 days',
        details: 'Complete the official VIDEX national visa form. Prepare a comprehensive Motivation Letter explaining your academic rationale, curriculum synergy, and career vision.',
        requiredDocuments: ['2 Videx printouts', 'Curriculum Vitae (Europass format)', 'Motivation Letter (signed)'],
        indoreCounsellorTip: 'German visa consuls check chronological consistency—explain any academic gaps honestly.',
      },
      {
        stepNumber: 5,
        title: 'VFS Visa Slot & Document Handover',
        shortDesc: 'Attend appointment at VFS German Visa Section',
        duration: '1 day',
        details: 'Book a student visa appointment through VFS Global for Germany (Mumbai or New Delhi jurisdiction). Submit two identical sets of documents in specified order.',
        requiredDocuments: ['2 complete sets of organized documents', 'Original Passport & APS', 'Visa fee (€75 in INR cash/demand draft)'],
        indoreCounsellorTip: 'Never staple documents! Use paper clips as per German consulate guidelines.',
      },
      {
        stepNumber: 6,
        title: 'Consular Processing & Visa Stamping',
        shortDesc: 'Foreigners authority (Ausländerbehörde) clearance & stamping',
        duration: '4 - 6 weeks',
        details: 'Your file is forwarded to the local immigration office in the German city of your university. Once approved, your passport is stamped with a 3-6 month entry visa.',
        requiredDocuments: ['Passport with Type D Visa sticker', 'Health insurance certificate for immigration check'],
        indoreCounsellorTip: 'Upon arrival in Germany, register your address (Anmeldung) within 14 days and convert to residence permit (Aufenthaltstitel).',
      },
    ],
  },
  'United States': {
    country: 'United States',
    flag: '🇺🇸',
    visaName: 'F-1 Academic Student Visa',
    processingTime: '2 - 5 days after in-person consular interview',
    transGlobeSuccessRate: '97.9%',
    fundsRequirement: '1 full academic year tuition + living cost on I-20 Form (liquid funds)',
    workRights: '20 hrs/week on-campus; 12-month OPT + 24-month STEM OPT extension (36 months total)',
    overview: 'The US visa journey centers around the crucial in-person Consular Officer Interview. Success is decided during the 2-3 minute face-to-face conversation.',
    requiredDocumentsChecklist: [
      {
        id: 'us_doc_passport',
        name: 'Valid Passport (Current & Previous)',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Must be valid for at least 6 months beyond your intended period of stay in the US.',
        indoreTip: 'Bring all previous expired passports containing prior international visas.'
      },
      {
        id: 'us_doc_i20',
        name: 'Form I-20 Signed by DSO and Student',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Official Certificate of Eligibility for Nonimmigrant Student Status issued by SEVP school.',
        indoreTip: 'Sign page 1 immediately in blue/black ink; verify your SEVIS ID and program end date.'
      },
      {
        id: 'us_doc_sevis',
        name: 'SEVIS I-901 Fee Payment Receipt ($350)',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Printed receipt confirming $350 payment to the US Department of Homeland Security.',
        indoreTip: 'Keep 3 physical paper printouts—checked at VAC, Consulate security, and US Port of Entry.'
      },
      {
        id: 'us_doc_ds160',
        name: 'DS-160 Confirmation Page with Barcode',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Printed confirmation page with high-resolution alphanumeric barcode.',
        indoreTip: 'Do not fold across the barcode. Barcode must scan instantly at VAC entrance.'
      },
      {
        id: 'us_doc_appointment',
        name: 'US Visa Appointment Confirmation Letter',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: 'Confirms both Day 1 (OFC/VAC biometrics) and Day 2 (Consular interview) appointment dates.',
        indoreTip: 'Includes your MRV fee payment confirmation number and UID barcode.'
      },
      {
        id: 'us_doc_photo',
        name: '2x2 inch (51x51 mm) Color Photos',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'White background, taken within last 6 months, no eyeglasses, ears visible.',
        indoreTip: 'Must match the digital photo uploaded during DS-160 submission.'
      },
      {
        id: 'us_doc_transcripts',
        name: 'Original Academic Transcripts & Degrees',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: '10th, 12th, Bachelor semester marksheets, provisional/original degree certificate.',
        indoreTip: 'Keep arranged in chronological order in an easily accessible accordion folder.'
      },
      {
        id: 'us_doc_standardized',
        name: 'Standardized Test Score Reports (GRE / GMAT / IELTS / TOEFL)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Official score reports matching test scores declared on your university application and DS-160.',
        indoreTip: 'Consular officers frequently inspect score reports when assessing student credibility.'
      },
      {
        id: 'us_doc_loan',
        name: 'Education Loan Sanction Letter',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Clear loan approval from nationalized bank or NBFC covering 1st year cost on I-20.',
        indoreTip: 'Loans from SBI, HDFC Credila, or Bank of Baroda carry high credibility at the consulate.'
      },
      {
        id: 'us_doc_bank',
        name: 'Bank Balance Certificates & 6-Month Statements',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Savings accounts, fixed deposits, or liquid mutual funds demonstrating financial solvency.',
        indoreTip: 'Total liquid proof should ideally show 1.5x to 2x of 1st year cost on Form I-20.'
      },
      {
        id: 'us_doc_affidavit',
        name: 'Form I-134 Affidavit of Support & Sponsor ITRs (3 Years)',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Notarized sponsorship declaration + ITR-V acknowledgements of parents/sponsors.',
        indoreTip: 'Include CA consolidated net worth statement summarizing total family assets.'
      },
      {
        id: 'us_doc_mockprep',
        name: 'Trans Globe 5-Golden-Rules Interview Q&A Dossier',
        category: 'Consular & Legal',
        isMandatory: false,
        notes: 'Custom mock interview preparation sheet outlining 2-sentence concise answers for why USA, why this uni, and career return to India.',
        indoreTip: 'Practice our Indore mock drills until your answers flow naturally without sounding memorized.'
      }
    ],
    steps: [
      {
        stepNumber: 1,
        title: 'Form I-20 Issuance & SEVIS ID',
        shortDesc: 'Receive Form I-20 from SEVP-certified institution',
        duration: '7 - 14 days',
        details: 'Accept your admission offer and submit financial declaration documents to your university DSO. They generate the official Certificate of Eligibility (Form I-20) with your SEVIS ID.',
        requiredDocuments: ['Official I-20 Form (signed)', 'Bank solvency certificate', 'Financial affidavit of support'],
        indoreCounsellorTip: 'Verify the program end date and STEM classification CIP code on page 1 of your I-20.',
      },
      {
        stepNumber: 2,
        title: 'Pay SEVIS I-901 Fee',
        shortDesc: 'Mandatory US Department of Homeland Security registration',
        duration: '1 day',
        details: 'Pay the mandatory $350 SEVIS fee online at FMJfee.com using your SEVIS ID and school code from your I-20.',
        requiredDocuments: ['Form I-901 Payment Confirmation Receipt', 'Passport details'],
        indoreCounsellorTip: 'Keep 3 physical printouts of the SEVIS receipt—it is inspected at VAC, Consulate, and US Port of Entry.',
      },
      {
        stepNumber: 3,
        title: 'DS-160 Non-Immigrant Visa Form',
        shortDesc: 'Complete online application on CEAC portal',
        duration: '2 - 4 days',
        details: 'Carefully complete the comprehensive online DS-160 application form. Every question regarding education history, family, and travel history must match records.',
        requiredDocuments: ['DS-160 Confirmation Page with barcode', '2x2 inch US specification photograph'],
        indoreCounsellorTip: 'Save your Application ID immediately—the portal times out after 15 minutes of inactivity.',
        isCrucial: true,
      },
      {
        stepNumber: 4,
        title: 'Book 2-Stage Visa Appointments',
        shortDesc: 'Schedule Biometrics (OFC/VAC) & Consular Interview',
        duration: '1 day (Scheduling slot)',
        details: 'Create profile on usvisascheduling.com, pay the visa fee ($185), and book 2 dates: Day 1 at Visa Application Center (VAC) for fingerprinting, Day 2 at US Embassy/Consulate for interview.',
        requiredDocuments: ['Appointment Confirmation Letter', 'MRV Fee payment receipt'],
        indoreCounsellorTip: 'Trans Globe Indore monitors slot releases round-the-clock for expedited student emergency slots.',
      },
      {
        stepNumber: 5,
        title: 'Trans Globe Indore Mock Interview Drill',
        shortDesc: 'Rigorous 1-on-1 interview practice with ex-visa specialists',
        duration: '3 - 7 days',
        details: 'Attend our specialized interview training covering the 5 Golden Rules: Strong non-immigrant intent, precise career return plans in India, financial solvency without illegal off-campus work, and deep knowledge of your professor/curriculum.',
        requiredDocuments: ['Full application folder', 'Custom Trans Globe Q&A dossier'],
        indoreCounsellorTip: 'Our Indore centre has trained 5,000+ students with a 98% first-attempt interview clearance record.',
        isCrucial: true,
      },
      {
        stepNumber: 6,
        title: 'Consular Interview & Passport Return',
        shortDesc: 'Face-to-face interview at Mumbai/Delhi/Hyderabad Consulate',
        duration: '2 - 3 minutes (decision on spot)',
        details: 'Appear confident at the consular window. If approved, the officer keeps your passport saying "Your visa is approved!". Passport is delivered to Indore within 3-5 days with 5-year multiple entry F-1 stamp.',
        requiredDocuments: ['Passport', 'I-20', 'SEVIS Receipt', 'DS-160 confirmation', 'Financial transcripts'],
        indoreCounsellorTip: 'Answer concisely in 2 sentences per question. Never present unasked documents unless specifically requested by the officer.',
      },
    ],
  },
  Canada: {
    country: 'Canada',
    flag: '🇨🇦',
    visaName: 'Study Permit (IRCC SDS & Non-SDS)',
    processingTime: '4 - 8 weeks',
    transGlobeSuccessRate: '98.2%',
    fundsRequirement: 'Guaranteed Investment Certificate (GIC $20,635 CAD) + 1st year paid tuition',
    workRights: '24 hours/week off-campus during terms; Post-Graduation Work Permit (PGWP up to 3 yrs)',
    overview: 'Canada requires an official Provincial Attestation Letter (PAL) from your province alongside paid first-year tuition and GIC deposit in a Canadian bank.',
    requiredDocumentsChecklist: [
      {
        id: 'ca_doc_passport',
        name: 'Valid International Passport',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Study permits are issued only up to passport expiry date; renew if validity is under 2 years.',
        indoreTip: 'Scan every single page with stamps, previous visas, and bio pages.'
      },
      {
        id: 'ca_doc_loa',
        name: 'Official Letter of Acceptance (LOA)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'From a Designated Learning Institution (DLI) confirming full-time enrollment and DLI number.',
        indoreTip: 'Confirm that program qualifies for Post-Graduation Work Permit (PGWP).'
      },
      {
        id: 'ca_doc_pal',
        name: 'Provincial Attestation Letter (PAL)',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Official quota allocation letter issued by the province (Ontario, British Columbia, Alberta, etc.).',
        indoreTip: 'Required under 2024 IRCC study permit regulations; your college requests this on your behalf.'
      },
      {
        id: 'ca_doc_gic',
        name: 'Guaranteed Investment Certificate (GIC - CAD $20,635)',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Official certificate from Scotiabank, CIBC, or ICICI Bank Canada proving living funds.',
        indoreTip: 'Trans Globe Indore facilitates fast online GIC account opening and outward remittance.'
      },
      {
        id: 'ca_doc_tuition',
        name: 'Full 1st Year Tuition Fee Payment Official Receipt',
        category: 'Financial & Sponsorship',
        isMandatory: true,
        notes: 'Official university/college receipt and Flywire / CIBC wire transaction reference.',
        indoreTip: 'Must show full 1st year tuition amount paid in full to qualify for SDS stream processing.'
      },
      {
        id: 'ca_doc_medical',
        name: 'Upfront eMedical Information Sheet',
        category: 'Health & Insurance',
        isMandatory: true,
        notes: 'Medical checkup from IRCC panel physician clinic with UMI tracking barcode number.',
        indoreTip: 'Upload upfront medical sheet with initial filing to prevent 4-week medical request delays.'
      },
      {
        id: 'ca_doc_ielts',
        name: 'IELTS Academic (min 6.0 each) / PTE Academic',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: 'Language testing score report meeting SDS requirements taken within 2 years.',
        indoreTip: 'IRCC accepts PTE Academic for SDS when minimum score threshold is achieved.'
      },
      {
        id: 'ca_doc_transcripts',
        name: 'Academic Marksheets & Degree Certificates',
        category: 'Academic & Test Scores',
        isMandatory: true,
        notes: '10th, 12th, and all bachelor semester marksheets plus degree certificate.',
        indoreTip: 'If your college issued backlogs summary, include official backlog certificate.'
      },
      {
        id: 'ca_doc_sop',
        name: 'Comprehensive Study Plan / Letter of Explanation (SOP)',
        category: 'Consular & Legal',
        isMandatory: true,
        notes: '3-4 page structured essay explaining course choice, career progression, ROI, and strong ties to India.',
        indoreTip: 'Most crucial document for Canada! Trans Globe editors refine your study plan to satisfy visa officer criteria.'
      },
      {
        id: 'ca_doc_pcc',
        name: 'Police Clearance Certificate (PCC)',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: 'Issued by Regional Passport Office (RPO) / Passport Seva Kendra (PSK) Indore.',
        indoreTip: 'Valid for 6 months from date of issuance. Apply early on Passport Seva portal.'
      },
      {
        id: 'ca_doc_property',
        name: 'CA Net Worth Certificate & Property Valuation',
        category: 'Financial & Sponsorship',
        isMandatory: false,
        notes: 'Demonstrates economic ties and immovable assets of parents in Madhya Pradesh.',
        indoreTip: 'Reinforces dual-intent credibility by showing substantial family heritage in India.'
      },
      {
        id: 'ca_doc_photos',
        name: 'IRCC Specification Photographs',
        category: 'Identity & Civil',
        isMandatory: true,
        notes: '35mm x 45mm frame size, head height between 31mm and 36mm, white or light background.',
        indoreTip: 'Upload crisp digital scan meeting pixel dimension guidelines.'
      }
    ],
    steps: [
      {
        stepNumber: 1,
        title: 'Letter of Acceptance (LOA) & PAL',
        shortDesc: 'Provincial Attestation Letter + DLI college acceptance',
        duration: '10 - 20 days',
        details: 'Secure your LOA from a Designated Learning Institution (DLI) with PGWP eligibility. The college requests your Provincial Attestation Letter (PAL) from Ontario/BC/Alberta government.',
        requiredDocuments: ['Official LOA with DLI Number', 'Provincial Attestation Letter (PAL)', 'Receipt of 1st year tuition fee'],
        indoreCounsellorTip: 'Verify that your chosen master/post-grad diploma qualifies for the full 3-year PGWP under current IRCC rules.',
        isCrucial: true,
      },
      {
        stepNumber: 2,
        title: 'GIC (Guaranteed Investment Certificate)',
        shortDesc: 'Deposit CAD $20,635 into Canadian bank',
        duration: '3 - 6 days',
        details: 'Open a student GIC account with Scotiabank, CIBC, or ICICI Bank Canada and transfer CAD $20,635 for living expense proof. The bank issues a formal GIC Certificate.',
        requiredDocuments: ['GIC Confirmation Certificate', 'Wire transfer outward remittance receipt (Form A2)'],
        indoreCounsellorTip: 'Trans Globe assists students with paperless same-day GIC account opening from Indore.',
      },
      {
        stepNumber: 3,
        title: 'Upfront Medical Examination',
        shortDesc: 'Medical assessment with eMedical tracking sheet',
        duration: '1 - 2 days',
        details: 'Undergo medical test at an IRCC-approved panel physician clinic and receive an Information Sheet with your UMI number.',
        requiredDocuments: ['Passport', '4 photos', 'eMedical Information Sheet'],
        indoreCounsellorTip: 'Always upload the upfront eMedical sheet to avoid 3-week processing delays from IRCC medical requests.',
      },
      {
        stepNumber: 4,
        title: 'Comprehensive Study Plan (SOP)',
        shortDesc: 'Detailed career statement addressing dual intent',
        duration: '5 - 7 days',
        details: 'Draft an analytical study plan explaining course progression, reasons for choosing Canada over Indian institutes, return on investment (ROI), and economic ties to India.',
        requiredDocuments: ['SOP / Letter of Explanation (3-4 pages)', 'Property valuation report / CA net worth certificate'],
        indoreCounsellorTip: 'Clearly demonstrate how your Indian earnings will multiply post-completion of this specific Canadian qualification.',
        isCrucial: true,
      },
      {
        stepNumber: 5,
        title: 'IRCC Portal Lodgement & Biometrics',
        shortDesc: 'Online filing & VFS appointment for biometric collection',
        duration: '1 - 2 days',
        details: 'Submit your file on the IRCC secure portal, pay study permit ($150 CAD) and biometrics ($85 CAD) fees. Attend VFS Global Indore/Bhopal with Biometric Instruction Letter (BIL).',
        requiredDocuments: ['IRCC Submission Confirmation', 'Biometrics Instruction Letter (BIL)', 'Original Passport at VFS'],
        indoreCounsellorTip: 'Biometrics are valid for 10 years across all Canadian visa applications.',
      },
      {
        stepNumber: 6,
        title: 'Passport Request (PPR) & Port of Entry Letter',
        shortDesc: 'Original passport request letter for visa counterfoil',
        duration: '3 - 6 weeks',
        details: 'Once approved, IRCC issues an official Passport Request (PPR) letter. Submit your passport to VFS for stamping. You also receive the Port of Entry (POE) Letter of Introduction.',
        requiredDocuments: ['Stamped Passport with Student Visa', 'Port of Entry (POE) Introduction Letter'],
        indoreCounsellorTip: 'At Canadian customs, show your POE letter and LOA to receive your printed physical Study Permit.',
      },
    ],
  },
};
