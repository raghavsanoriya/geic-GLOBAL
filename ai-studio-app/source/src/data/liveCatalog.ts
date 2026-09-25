import type * as Zip from './geicData';
import type { UniversityComparisonData as ZipUniversity } from './universitiesData';
import type { CountryVisaRoadmap } from './visaRoadmapData';
import type { AppNotification } from '../components/modals/NotificationsModal';

export type { CountryVisaRoadmap, VisaRoadmapStep, DestinationRequiredDocument, DocumentCategory } from './visaRoadmapData';
export type UniversityComparisonData = Omit<ZipUniversity, 'globalRankNumber' | 'acceptanceRatePercentage' | 'type'> & {
  globalRankNumber: number | null;
  acceptanceRatePercentage: number | null;
  type: string;
};

export type StudioCatalog = {
  schemaVersion: 1;
  source: 'laravel';
  loadedAt: string;
  GEIC_BRAND: typeof Zip.GEIC_BRAND;
  GEIC_STATS: typeof Zip.GEIC_STATS;
  STUDY_DESTINATIONS: typeof Zip.STUDY_DESTINATIONS;
  UNIVERSITY_PARTNERS: typeof Zip.UNIVERSITY_PARTNERS;
  GLOBAL_UNIVERSITIES: UniversityComparisonData[];
  GEIC_SERVICES: typeof Zip.GEIC_SERVICES;
  ENGLISH_TESTS: typeof Zip.ENGLISH_TESTS;
  STUDENT_REVIEWS: typeof Zip.STUDENT_REVIEWS;
  UPCOMING_EXPO: typeof Zip.UPCOMING_EXPO & { registrationOpen: boolean };
  UPCOMING_EVENTS_LIST: (typeof Zip.UPCOMING_EVENTS_LIST[number] & { registrationOpen: boolean })[];
  GEIC_BLOGS: typeof Zip.GEIC_BLOGS;
  HERO_SLIDES: typeof Zip.HERO_SLIDES;
  ABOUT_INFO: typeof Zip.ABOUT_INFO;
  FOUR_STEPS: typeof Zip.FOUR_STEPS;
  FAQS: typeof Zip.FAQS;
  VISA_ROADMAP_DATA: Record<string, CountryVisaRoadmap>;
  NOTIFICATIONS: AppNotification[];
};

// The imported ZIP is retained for reference/types only; no seed content is loaded.
export let GEIC_BRAND: StudioCatalog['GEIC_BRAND'];
export let ABOUT_INFO: StudioCatalog['ABOUT_INFO'];
export let UPCOMING_EXPO: StudioCatalog['UPCOMING_EXPO'];
export let GEIC_STATS: StudioCatalog['GEIC_STATS'] = [];
export let STUDY_DESTINATIONS: StudioCatalog['STUDY_DESTINATIONS'] = [];
export let UNIVERSITY_PARTNERS: StudioCatalog['UNIVERSITY_PARTNERS'] = [];
export let GLOBAL_UNIVERSITIES: StudioCatalog['GLOBAL_UNIVERSITIES'] = [];
export let GEIC_SERVICES: StudioCatalog['GEIC_SERVICES'] = [];
export let ENGLISH_TESTS: StudioCatalog['ENGLISH_TESTS'] = [];
export let STUDENT_REVIEWS: StudioCatalog['STUDENT_REVIEWS'] = [];
export let UPCOMING_EVENTS_LIST: StudioCatalog['UPCOMING_EVENTS_LIST'] = [];
export let GEIC_BLOGS: StudioCatalog['GEIC_BLOGS'] = [];
export let HERO_SLIDES: StudioCatalog['HERO_SLIDES'] = [];
export let FOUR_STEPS: StudioCatalog['FOUR_STEPS'] = [];
export let FAQS: StudioCatalog['FAQS'] = [];
export let VISA_ROADMAP_DATA: StudioCatalog['VISA_ROADMAP_DATA'] = {};
export let NOTIFICATIONS: StudioCatalog['NOTIFICATIONS'] = [];

export function applyCatalog(data: StudioCatalog): void {
  const lists = ['GEIC_STATS', 'STUDY_DESTINATIONS', 'UNIVERSITY_PARTNERS', 'GLOBAL_UNIVERSITIES',
    'GEIC_SERVICES', 'ENGLISH_TESTS', 'STUDENT_REVIEWS', 'UPCOMING_EVENTS_LIST', 'GEIC_BLOGS',
    'HERO_SLIDES', 'FOUR_STEPS', 'FAQS', 'NOTIFICATIONS'] as const;
  if (!data || data.schemaVersion !== 1 || data.source !== 'laravel' ||
      lists.some(key => !Array.isArray(data[key])) || !data.GEIC_BRAND || !data.ABOUT_INFO ||
      !data.UPCOMING_EXPO || !data.VISA_ROADMAP_DATA ||
      !data.STUDY_DESTINATIONS.length || !data.GEIC_SERVICES.length ||
      !data.ENGLISH_TESTS.length || !data.FOUR_STEPS.length) {
    throw new Error('The current catalogue could not be loaded. Please try again.');
  }
  ({ GEIC_BRAND, ABOUT_INFO, UPCOMING_EXPO, GEIC_STATS, STUDY_DESTINATIONS,
    UNIVERSITY_PARTNERS, GLOBAL_UNIVERSITIES, GEIC_SERVICES, ENGLISH_TESTS,
    STUDENT_REVIEWS, UPCOMING_EVENTS_LIST, GEIC_BLOGS, HERO_SLIDES,
    FOUR_STEPS, FAQS, VISA_ROADMAP_DATA, NOTIFICATIONS } = data);
}
