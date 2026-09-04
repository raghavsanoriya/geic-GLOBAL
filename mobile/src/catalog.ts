export type QuickAction = {
  id: string;
  title: string;
  subtitle: string;
  icon: string;
  tab?: 'Explore' | 'Events' | 'Services' | 'Apply';
  detail?: { kind: 'service'; id: string };
};

export type Destination = {
  id: string;
  name: string;
  tagline: string;
  detail: string;
  image: number;
  stat: string;
  highlights: string[];
};

export type Service = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  image: number;
  icon: string;
  cta: string;
};

export type EventItem = {
  id: string;
  title: string;
  type: string;
  date: string;
  day: string;
  month: string;
  time: string;
  location: string;
  description: string;
  image: number;
};

export const destinations: Destination[] = [
  {
    id: 'australia', name: 'Australia', tagline: 'Study, work and grow', detail: 'World-class universities with flexible pathways and a lifestyle that feels welcoming from day one.',
    image: require('../assets/australia.jpg'), stat: '12k+ courses', highlights: ['Top-ranked universities', 'Flexible pathways', 'Strong student support'],
  },
  {
    id: 'canada', name: 'Canada', tagline: 'Build your next chapter', detail: 'Career-focused study routes, diverse communities and practical post-study opportunities.',
    image: require('../assets/canada.jpg'), stat: '800+ partners', highlights: ['Work-friendly routes', 'Great student cities', 'Clear study plans'],
  },
  {
    id: 'uk', name: 'United Kingdom', tagline: 'One year, big momentum', detail: 'Shorter master’s options, globally recognised universities and a rich academic culture.',
    image: require('../assets/uk.jpg'), stat: '1-year masters', highlights: ['Globally recognised', 'Fast-track degrees', 'Creative communities'],
  },
];

export const services: Service[] = [
  {
    id: 'destination-planning', title: 'Destination planning', subtitle: 'Compare your best-fit countries', description: 'Shortlist destinations by tuition, living costs, course fit and the kind of future you want to build.',
    image: require('../assets/choose-study-destination.jpg'), icon: 'map-marker-radius-outline', cta: 'Compare destinations',
  },
  {
    id: 'university-admissions', title: 'University admissions', subtitle: 'Build a stronger shortlist', description: 'Get practical guidance on courses, applications, documents, deadlines and offer decisions.',
    image: require('../assets/university-admissions.jpg'), icon: 'school-outline', cta: 'Explore admissions',
  },
  {
    id: 'visa-guidance', title: 'Visa guidance', subtitle: 'Prepare with confidence', description: 'Understand your document checklist and prepare for the next step with an experienced counsellor.',
    image: require('../assets/student-visa-mistakes.jpg'), icon: 'passport', cta: 'View visa support',
  },
  {
    id: 'test-preparation', title: 'Test preparation', subtitle: 'Target the right score', description: 'Plan IELTS, PTE, TOEFL and other tests around your course, destination and intake.',
    image: require('../assets/student-guidance.jpg'), icon: 'head-lightbulb-outline', cta: 'Plan test prep',
  },
  {
    id: 'loans-funding', title: 'Loans and funding', subtitle: 'Make the numbers clearer', description: 'Explore education loans, EMI planning and scholarship conversations for your study journey.',
    image: require('../assets/canada.jpg'), icon: 'cash-multiple', cta: 'Explore funding',
  },
  {
    id: 'ai-study-guidance', title: 'AI study guidance', subtitle: 'Ask your next question', description: 'Get a useful starting point for destinations, tests, scholarships, admissions and visas.',
    image: require('../assets/student-guidance.jpg'), icon: 'robot-outline', cta: 'Ask the AI guide',
  },
];

export const events: EventItem[] = [
  {
    id: 'global-uni-expo', title: 'Global Uni Expo', type: 'In-person counselling day', date: '24 Aug 2026', day: '24', month: 'AUG', time: '11:00 AM – 5:00 PM', location: 'Trans Globe House, Indore', description: 'Meet university representatives, compare courses and get practical next-step advice in one afternoon.', image: require('../assets/student-guidance.jpg'),
  },
  {
    id: 'application-clinic', title: 'Application Clinic', type: 'Small-group workshop', date: '06 Sep 2026', day: '06', month: 'SEP', time: '12:00 PM – 2:00 PM', location: 'Online · Live with counsellors', description: 'Bring your shortlist and leave with a clearer application checklist for your preferred intake.', image: require('../assets/university-admissions.jpg'),
  },
  {
    id: 'visa-ready-session', title: 'Visa Ready Session', type: 'Document workshop', date: '19 Sep 2026', day: '19', month: 'SEP', time: '3:00 PM – 4:30 PM', location: 'Trans Globe House, Indore', description: 'Learn how to organise your evidence, avoid common mistakes and prepare for a confident submission.', image: require('../assets/student-visa-mistakes.jpg'),
  },
];

export const quickActions: QuickAction[] = [
  { id: 'compare', title: 'Compare destinations', subtitle: 'Find your fit', icon: 'earth', tab: 'Explore' },
  { id: 'emi', title: 'EMI calculator', subtitle: 'Plan your budget', icon: 'calculator-variant-outline', detail: { kind: 'service', id: 'loans-funding' } },
  { id: 'loans', title: 'Education loans', subtitle: 'Explore funding', icon: 'cash-multiple', detail: { kind: 'service', id: 'loans-funding' } },
  { id: 'tests', title: 'Test preparation', subtitle: 'Target your score', icon: 'notebook-edit-outline', detail: { kind: 'service', id: 'test-preparation' } },
  { id: 'agents', title: 'AI study agents', subtitle: 'Ask a question', icon: 'robot-outline', detail: { kind: 'service', id: 'ai-study-guidance' } },
  { id: 'counsellor', title: 'Talk to a counsellor', subtitle: 'Get a clear plan', icon: 'message-text-outline', tab: 'Apply' },
];

export const storyCards = [
  { id: 'story-destination', eyebrow: 'START HERE', title: 'Choose your destination', copy: 'Compare costs, courses and next steps.', image: require('../assets/australia.jpg'), tab: 'Explore' as const },
  { id: 'story-admissions', eyebrow: 'MAKE IT REAL', title: 'Build your shortlist', copy: 'Find a university path that fits.', image: require('../assets/university-admissions.jpg'), tab: 'Services' as const },
  { id: 'story-visa', eyebrow: 'FEEL READY', title: 'Prepare your visa file', copy: 'Know what to do before you submit.', image: require('../assets/student-visa-mistakes.jpg'), tab: 'Services' as const },
];

export const exploreFilters = ['All', 'Destinations', 'Compare', 'Study tools', 'Scholarships', 'Visa'];
