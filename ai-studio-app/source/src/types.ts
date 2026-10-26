export type AppTab = 'home' | 'explore' | 'counsellor' | 'evaluator' | 'expo' | 'more';

export type MoreSection = 'services' | 'events' | 'tests' | 'saved' | 'about' | 'contact';

export type ActivePageView = 
  | { type: 'tab'; tab: AppTab }
  | { type: 'service'; serviceId: string }
  | { type: 'test'; testId: string }
  | { type: 'event'; eventId: string }
  | { type: 'services-list' }
  | { type: 'tests-list' }
  | { type: 'events-list' };

export interface TestModuleDetail {
  title: string;
  duration: string;
  description: string;
  scoringTips: string;
}

export interface EnglishTestInfo {
  id: string;
  name: string;
  code: string;
  category: 'English Proficiency' | 'Graduate Aptitude' | 'Undergraduate' | 'Foreign Language';
  tagline: string;
  acceptedBy: string;
  testFormat: string;
  duration: string;
  scoringScale: string;
  minTargetBand: string;
  transGlobeHighlights: string[];
  batchTimings: string;
  courseFeeINR?: string;
  testCenterIndore: string;
  bannerImage: string;
  overview: string;
  modules: TestModuleDetail[];
  preparationTips: string[];
  officialRegistrationUrl?: string;
  faqs: { question: string; answer: string }[];
  difficultyLevel?: 'Moderate' | 'Intermediate' | 'Advanced';
  popularityRank?: number;
  popularityScore?: number;
}

export interface EventAgendaItem {
  time: string;
  title: string;
  description: string;
  speaker?: string;
}

export interface UpcomingEventInfo {
  registrationOpen?: boolean;
  id: string;
  title: string;
  subtitle?: string;
  date: string;
  time: string;
  venue: string;
  city: string;
  badge: string;
  description: string;
  highlights: string[];
  bannerUrl: string;
  category: 'Summit' | 'Masterclass' | 'Spot Assessment' | 'Webinar' | 'Conclave';
  participatingUniversitiesCount?: number;
  participatingUniversitiesList?: string[];
  freeEntry: boolean;
  agenda?: EventAgendaItem[];
  registrationPerks: string[];
  targetAudience: string;
  faqs: { question: string; answer: string }[];
}

export interface StudyDestination {
  id: string;
  name: string;
  code: string;
  tagline: string;
  description: string;
  highlights: string[];
  flagEmoji: string;
  flagUrl: string;
  imageUrl: string;
  popularCourses: string[];
  avgTuitionYear: string;
  livingExpensesYear: string;
  postStudyWorkVisa: string;
  prPathway: string;
  intakes: string[];
  topUniversities: string[];
  languageRequirements: string;
}

export interface UniversityPartner {
  id: string;
  name: string;
  country: string;
  location: string;
  logoUrl: string;
  heroImage?: string;
  globalRank?: string;
  programsOffered: string[];
  popularDegrees: string[];
  tuitionRange: string;
  intakes: string[];
  ieltsMin: string;
  greGmatRequired: boolean;
  scholarshipsAvailable: string;
  description: string;
}

export interface ServiceProcessStep {
  step: string;
  title: string;
  description: string;
}

export interface GeicService {
  id: string;
  stepNumber: string;
  title: string;
  category: 'Admissions' | 'Visas' | 'Test Preparation' | 'Finance & Grants' | 'Departure & Housing' | 'Immigration';
  tagline: string;
  shortDesc: string;
  fullDesc: string;
  iconName: string;
  bannerImage: string;
  keyBenefits: string[];
  detailedProcess: ServiceProcessStep[];
  deliverables: string[];
  whoNeedsThis: string;
  popularDestinations: string[];
  faqs: { question: string; answer: string }[];
  difficultyLevel?: 'Essential' | 'Moderate' | 'Comprehensive' | 'Complex';
  popularityRank?: number;
  popularityScore?: number;
}

export interface StudentReview {
  id: string;
  studentName: string;
  destination: string;
  university: string;
  course: string;
  rating: number;
  reviewDate: string;
  comment: string;
  verified: boolean;
  avatarLetter: string;
}

export interface AdmissionSummitEvent {
  id: string;
  title: string;
  subtitle: string;
  date: string;
  time: string;
  location: string;
  city: string;
  venue: string;
  highlights: string[];
  participatingUniversities: number;
  freeEntry: boolean;
  bannerImage: string;
}

export interface BlogPost {
  id: string;
  title: string;
  slug: string;
  category: 'Visa & Immigration' | 'Admissions & Intakes' | 'Scholarships & Grants' | 'Test Preparation' | 'Student Experience';
  coverImage: string;
  readTime: string;
  publishedDate: string;
  author: {
    name: string;
    role: string;
    avatar: string;
  };
  excerpt: string;
  tags: string[];
  content: string[];
  keyTakeaways: string[];
}

export interface ProfileEvaluationRequest {
  fullName: string;
  email: string;
  phone: string;
  currentEducation: string;
  scorePercentage: number;
  intendedLevel: 'Bachelors' | 'Masters' | 'Diploma' | 'Doctorate';
  targetCountries: string[];
  intendedCourse: string;
  englishTest: 'IELTS' | 'PTE' | 'TOEFL' | 'Duolingo' | 'Not Taken Yet';
  testScore?: string;
  budgetPerYearINR: string;
  workExperienceYears: number;
}

export interface ProfileEvaluationResult {
  overallScore: number;
  visaSuccessProbability: number | null;
  summary: string;
  ambitiousMatches: string[];
  targetMatches: string[];
  safeMatches: string[];
  scholarshipEligibility: string;
  keyStrengths: string[];
  recommendedActionItems: string[];
}

export interface ChatMessage {
  id: string;
  sender: 'user' | 'assistant';
  text: string;
  timestamp: number;
  suggestedActions?: string[];
  universityMatches?: string[];
}

export interface BookingAppointment {
  id: string;
  name: string;
  email: string;
  phone: string;
  date: string;
  timeSlot: string;
  mode: 'Indore Office (In-Person)' | 'Online (Video Call)';
  destinationInterest: string;
  studyLevel: string;
  notes?: string;
}
