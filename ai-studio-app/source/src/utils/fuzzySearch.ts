import Fuse, { IFuseOptions } from 'fuse.js';
import { 
  GEIC_SERVICES, 
  ENGLISH_TESTS, 
  UPCOMING_EVENTS_LIST, 
  STUDY_DESTINATIONS 
} from '../data/liveCatalog';
import { 
  GLOBAL_UNIVERSITIES,
  UniversityComparisonData
} from '../data/liveCatalog';
import { 
  GeicService, 
  EnglishTestInfo, 
  UpcomingEventInfo, 
  StudyDestination 
} from '../types';

export type SearchItemType = 'service' | 'test' | 'event' | 'destination' | 'university';

export interface GlobalSearchItem {
  id: string;
  type: SearchItemType;
  title: string;
  subtitle: string;
  description: string;
  badge?: string;
  category: string;
  tags: string[];
  imageUrl?: string;
  metaInfo?: string;
  linkHint?: string;
  rawItem: GeicService | EnglishTestInfo | UpcomingEventInfo | StudyDestination | UniversityComparisonData;
}

// Convert all our data sets into a unified searchable index
export const buildSearchIndex = (): GlobalSearchItem[] => {
  const items: GlobalSearchItem[] = [];

  // 1. Services
  GEIC_SERVICES.forEach((srv) => {
    items.push({
      id: srv.id,
      type: 'service',
      title: srv.title,
      subtitle: `${srv.category} • Step ${srv.stepNumber}`,
      description: srv.shortDesc,
      badge: `Step ${srv.stepNumber}`,
      category: srv.category,
      tags: [
        'service',
        'counselling',
        srv.category,
        srv.whoNeedsThis,
        ...srv.keyBenefits,
        ...srv.popularDestinations,
      ],
      imageUrl: srv.bannerImage,
      metaInfo: `${srv.detailedProcess.length} steps • ${srv.popularDestinations.length} countries`,
      linkHint: 'Service Guide',
      rawItem: srv,
    });
  });

  // 2. Tests
  ENGLISH_TESTS.forEach((test) => {
    items.push({
      id: test.id,
      type: 'test',
      title: `${test.name} (${test.code})`,
      subtitle: `${test.category} • Target ${test.minTargetBand}`,
      description: test.tagline,
      badge: test.code,
      category: test.category,
      tags: [
        'test',
        'exam',
        'coaching',
        'english',
        test.code,
        test.name,
        test.category,
        test.acceptedBy,
        test.duration,
        ...test.modules.map((m) => m.title),
        ...test.transGlobeHighlights,
      ],
      imageUrl: test.bannerImage,
      metaInfo: `Duration: ${test.duration} • Min Band: ${test.minTargetBand}`,
      linkHint: 'Test Coaching',
      rawItem: test,
    });
  });

  // 3. Events
  UPCOMING_EVENTS_LIST.forEach((ev) => {
    items.push({
      id: ev.id,
      type: 'event',
      title: ev.title,
      subtitle: `${ev.date} • ${ev.city}`,
      description: ev.subtitle,
      badge: ev.badge,
      category: ev.category,
      tags: [
        'event',
        'expo',
        'summit',
        'fair',
        'masterclass',
        ev.city,
        ev.venue,
        ev.badge,
        ev.category,
        ev.targetAudience,
        ...ev.highlights,
      ],
      imageUrl: ev.bannerUrl,
      metaInfo: `${ev.city} • ${ev.venue.split(',')[0]}`,
      linkHint: 'Free VIP Pass',
      rawItem: ev,
    });
  });

  // 4. Destinations
  STUDY_DESTINATIONS.forEach((dest) => {
    items.push({
      id: dest.id,
      type: 'destination',
      title: `${dest.flagEmoji} Study in ${dest.name}`,
      subtitle: `${dest.avgTuitionYear} • ${dest.popularCourses.slice(0, 3).join(', ')}`,
      description: dest.tagline,
      badge: dest.flagEmoji,
      category: 'Destinations',
      tags: [
        'destination',
        'country',
        dest.name,
        dest.code,
        dest.avgTuitionYear,
        dest.prPathway,
        ...dest.popularCourses,
        ...dest.highlights,
        ...dest.topUniversities,
      ],
      imageUrl: dest.imageUrl,
      metaInfo: `${dest.topUniversities.length} Top Unis • ${dest.postStudyWorkVisa}`,
      linkHint: 'Country Guide',
      rawItem: dest,
    });
  });

  // 5. Universities
  GLOBAL_UNIVERSITIES.forEach((uni) => {
    items.push({
      id: uni.id,
      type: 'university',
      title: `${uni.flagEmoji} ${uni.name}`,
      subtitle: `${uni.country} • ${uni.globalRank.split('(')[0].trim()}`,
      description: `Fees: ${uni.annualTuition} • Acceptance: ${uni.acceptanceRate.split('(')[0].trim()} • IELTS: ${uni.ieltsRequirement.split('(')[0].trim()}`,
      badge: uni.shortName,
      category: 'Universities',
      tags: [
        'university',
        'college',
        'compare',
        'admissions',
        uni.name,
        uni.shortName,
        uni.country,
        uni.city,
        uni.globalRank,
        uni.annualTuition,
        uni.ieltsRequirement,
        uni.pteRequirement,
        ...uni.popularPrograms,
      ],
      imageUrl: uni.campusImage,
      metaInfo: `Acceptance: ${uni.acceptanceRate.split('(')[0]} • ${uni.annualTuition}`,
      linkHint: 'Compare University',
      rawItem: uni,
    });
  });

  return items;
};



const fuseOptions: IFuseOptions<GlobalSearchItem> = {
  keys: [
    { name: 'title', weight: 0.4 },
    { name: 'tags', weight: 0.25 },
    { name: 'badge', weight: 0.15 },
    { name: 'category', weight: 0.1 },
    { name: 'subtitle', weight: 0.05 },
    { name: 'description', weight: 0.05 },
  ],
  threshold: 0.38, // Balance between typo tolerance and precision
  distance: 100,
  ignoreLocation: true,
  minMatchCharLength: 2,
  includeScore: true,
  includeMatches: true,
};



export interface FuzzySearchResult {
  item: GlobalSearchItem;
  score?: number;
  matches?: readonly any[];
}

export const performFuzzySearch = (
  query: string,
  filterType: 'all' | SearchItemType = 'all',
  limit: number = 20
): FuzzySearchResult[] => {
  const ALL_SEARCH_ITEMS = buildSearchIndex();
  const globalFuse = new Fuse(ALL_SEARCH_ITEMS, fuseOptions);
  const trimmed = query.trim();
  if (!trimmed) {
    let source = ALL_SEARCH_ITEMS;
    if (filterType !== 'all') {
      source = source.filter((item) => item.type === filterType);
    }
    return source.slice(0, limit).map((item) => ({ item, score: 0 }));
  }

  const results = globalFuse.search(trimmed);

  let filtered = results;
  if (filterType !== 'all') {
    filtered = filtered.filter((r) => r.item.type === filterType);
  }

  return filtered.slice(0, limit).map((r) => ({
    item: r.item,
    score: r.score,
    matches: r.matches,
  }));
};
