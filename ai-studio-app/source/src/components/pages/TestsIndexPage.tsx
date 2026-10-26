import React, { useState, useMemo } from 'react';
import { 
  ArrowLeft, 
  BookOpen, 
  ChevronRight, 
  Sparkles, 
  CheckCircle2, 
  Target, 
  Clock, 
  Menu, 
  Check,
  Search,
  SlidersHorizontal,
  Flame,
  Layers,
  Gauge,
  X,
  RotateCcw,
  ArrowUpDown,
  Award
} from 'lucide-react';
import Fuse from 'fuse.js';
import { ENGLISH_TESTS, GEIC_BRAND } from '../../data/liveCatalog';
import { EnglishTestInfo, StudyDestination } from '../../types';
import { UniversityComparisonData } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';
import { GlobalSearchBar } from '../common/GlobalSearchBar';

interface TestsIndexPageProps {
  onBack: () => void;
  onSelectTest: (testId: string) => void;
  onBookConsultation: (testName?: string) => void;
  onOpenDrawer?: () => void;
  onSelectService?: (serviceId: string) => void;
  onSelectEvent?: (eventId: string) => void;
  onSelectDestination?: (destination: StudyDestination) => void;
  onCompareUniversities?: (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
  onOpenExpo?: () => void;
}

// Meta mappings for test difficulty, popularity, and duration
const TEST_META: Record<string, { difficulty: 'Moderate' | 'Intermediate' | 'Advanced'; popularityRank: number; popularityLabel: string; durationMinutes: number }> = {
  ielts: { difficulty: 'Intermediate', popularityRank: 1, popularityLabel: '#1 Top Choice for UK, AU & CA', durationMinutes: 165 },
  pte: { difficulty: 'Intermediate', popularityRank: 2, popularityLabel: '#2 Fast AI Results in 24h', durationMinutes: 125 },
  gre: { difficulty: 'Advanced', popularityRank: 3, popularityLabel: '#3 Top MS & STEM in USA', durationMinutes: 115 },
  toefl: { difficulty: 'Intermediate', popularityRank: 4, popularityLabel: '#4 Preferred by US Universities', durationMinutes: 116 },
  duolingo: { difficulty: 'Moderate', popularityRank: 5, popularityLabel: '#5 Fast 1-Hr Online Exam', durationMinutes: 60 },
  gmat: { difficulty: 'Advanced', popularityRank: 6, popularityLabel: '#6 Global Elite MBA Standard', durationMinutes: 135 },
  sat: { difficulty: 'Intermediate', popularityRank: 7, popularityLabel: '#7 Top Undergrad USA Exam', durationMinutes: 134 },
  german: { difficulty: 'Advanced', popularityRank: 8, popularityLabel: '#8 Key to Free German Public Unis', durationMinutes: 180 },
};

type SortOption = 'popularity' | 'category' | 'difficulty' | 'duration';

export const TestsIndexPage: React.FC<TestsIndexPageProps> = ({
  onBack,
  onSelectTest,
  onBookConsultation,
  onOpenDrawer,
  onSelectService,
  onSelectEvent,
  onSelectDestination,
  onCompareUniversities,
  onOpenExpo,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('All');
  const [selectedDifficulty, setSelectedDifficulty] = useState<string>('All');
  const [sortBy, setSortBy] = useState<SortOption>('popularity');
  const [showMatrix, setShowMatrix] = useState(false);
  const [showFiltersPanel, setShowFiltersPanel] = useState(false);

  const categories = ['All', 'English Proficiency', 'Graduate Aptitude', 'Undergraduate', 'Foreign Language'];
  const difficulties = ['All', 'Moderate', 'Intermediate', 'Advanced'];

  const difficultyWeight: Record<string, number> = {
    Moderate: 1,
    Intermediate: 2,
    Advanced: 3,
  };

  const testEquivalencies = [
    { ielts: '8.0 – 9.0', pte: '79 – 90', toefl: '110 – 120', det: '135 – 160', level: 'Expert / Ivy League' },
    { ielts: '7.5', pte: '73 – 78', toefl: '102 – 109', det: '125 – 130', level: 'Top 50 Global Unis' },
    { ielts: '7.0', pte: '65 – 72', toefl: '94 – 101', det: '115 – 120', level: 'Most Master’s Programs' },
    { ielts: '6.5', pte: '58 – 64', toefl: '79 – 93', det: '105 – 110', level: 'Standard Bachelor/PG' },
    { ielts: '6.0', pte: '50 – 57', toefl: '60 – 78', det: '95 – 100', level: 'Foundation / Diploma' },
  ];

  // Fuse.js index for tests fuzzy search
  const testsFuse = useMemo(() => {
    return new Fuse(ENGLISH_TESTS, {
      keys: [
        { name: 'name', weight: 0.4 },
        { name: 'code', weight: 0.3 },
        { name: 'category', weight: 0.2 },
        { name: 'tagline', weight: 0.1 },
        { name: 'acceptedBy', weight: 0.1 },
      ],
      threshold: 0.38,
      ignoreLocation: true,
    });
  }, []);

  // Processed tests with search, filter, and sort
  const processedTests = useMemo(() => {
    const matchingIds = searchQuery.trim()
      ? new Set(testsFuse.search(searchQuery.trim()).map((r) => r.item.id))
      : null;

    return ENGLISH_TESTS
      .map((test) => {
        const meta = TEST_META[test.id] || {
          difficulty: 'Intermediate' as const,
          popularityRank: 99,
          popularityLabel: 'Standardized Test',
          durationMinutes: 120,
        };
        return {
          ...test,
          difficultyLevel: test.difficultyLevel || meta.difficulty,
          popularityRank: test.popularityRank || meta.popularityRank,
          popularityLabel: meta.popularityLabel,
          durationMinutes: meta.durationMinutes,
        };
      })
      .filter((test) => {
        // Fuzzy search query filter
        if (matchingIds && !matchingIds.has(test.id)) {
          return false;
        }

        // Category filter
        if (selectedCategory !== 'All' && test.category !== selectedCategory) {
          return false;
        }

        // Difficulty filter
        if (selectedDifficulty !== 'All' && test.difficultyLevel !== selectedDifficulty) {
          return false;
        }

        return true;
      })
      .sort((a, b) => {
        if (sortBy === 'popularity') {
          return (a.popularityRank || 99) - (b.popularityRank || 99);
        }
        if (sortBy === 'category') {
          return a.category.localeCompare(b.category) || a.name.localeCompare(b.name);
        }
        if (sortBy === 'difficulty') {
          const wA = difficultyWeight[a.difficultyLevel || 'Intermediate'] || 2;
          const wB = difficultyWeight[b.difficultyLevel || 'Intermediate'] || 2;
          return wA - wB;
        }
        if (sortBy === 'duration') {
          return a.durationMinutes - b.durationMinutes;
        }
        return 0;
      });
  }, [searchQuery, selectedCategory, selectedDifficulty, sortBy]);

  const hasActiveFilters = searchQuery.trim() !== '' || selectedCategory !== 'All' || selectedDifficulty !== 'All' || sortBy !== 'popularity';

  const resetAllFilters = () => {
    setSearchQuery('');
    setSelectedCategory('All');
    setSelectedDifficulty('All');
    setSortBy('popularity');
  };

  const getDifficultyBadgeColor = (diff: string) => {
    switch (diff) {
      case 'Moderate':
        return 'bg-emerald-50 text-emerald-700 border-emerald-200';
      case 'Intermediate':
        return 'bg-blue-50 text-blue-700 border-blue-200';
      case 'Advanced':
        return 'bg-purple-50 text-purple-700 border-purple-200';
      default:
        return 'bg-slate-50 text-slate-700 border-slate-200';
    }
  };

  return (
    <div className="pb-28 text-slate-800 space-y-4 animate-in fade-in duration-200">
      {/* Top Header */}
      <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-md px-4 py-2.5 border-b border-slate-200/80 shadow-xs flex items-center justify-between">
        <div className="flex items-center space-x-2">
          {onOpenDrawer && (
            <button
              onClick={onOpenDrawer}
              className="p-1.5 -ml-1 text-slate-700 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition cursor-pointer"
              aria-label="Open menu"
            >
              <Menu className="w-5 h-5" />
            </button>
          )}
          <button
            onClick={onBack}
            className="flex items-center space-x-1.5 text-xs font-bold text-slate-700 hover:text-red-600 transition cursor-pointer"
          >
            <ArrowLeft className="w-4 h-4" />
            <span>Home</span>
          </button>
        </div>

        <div className="text-center">
          <span className="text-xs font-black text-slate-900 block leading-tight">
            Coaching & Tests Hub
          </span>
          <span className="text-[10px] text-slate-500 font-medium">
            {processedTests.length} of {ENGLISH_TESTS.length} Available
          </span>
        </div>

        <button
          onClick={() => onBookConsultation('IELTS / PTE')}
          className="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1 transition shadow-xs cursor-pointer"
        >
          <Sparkles className="w-3.5 h-3.5" />
          <span>Free Demo</span>
        </button>
      </header>

      {/* Hero Header Card */}
      <div className="px-4 space-y-2">
        <div className="flex items-center space-x-2">
          <span className="text-[10px] font-bold text-blue-700 uppercase tracking-wider bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">
            British Council & IDP Certified
          </span>
          <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
            7.5+ Band Guarantee
          </span>
        </div>
        <h1 className="text-xl font-black text-slate-900 leading-tight">
          Standardized Test Coaching & Preparation
        </h1>
        <p className="text-xs text-slate-500 leading-relaxed">
          Master trainers certified by British Council & IDP, modern computer testing labs in Indore, AI diagnostic evaluations, and guaranteed score improvement.
        </p>
      </div>

      {/* Global Fuzzy Search Bar across tests, services, events, destinations */}
      <div className="px-4">
        <GlobalSearchBar
          placeholder="Global search tests, services, events, universities, destinations..."
          onSelectTest={onSelectTest}
          onSelectService={onSelectService}
          onSelectEvent={onSelectEvent}
          onSelectDestination={onSelectDestination}
          onCompareUniversities={onCompareUniversities}
          onOpenExpo={onOpenExpo}
          defaultFilter="test"
          allowFilterSwitching={true}
          showSuggestions={false}
        />
      </div>

      {/* Score Equivalency Converter Card */}
      <div className="px-4">
        <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-2.5">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <Target className="w-4 h-4 text-red-600" />
              <h3 className="font-extrabold text-xs text-slate-900">
                Score Equivalency Chart (IELTS vs PTE vs TOEFL vs DET)
              </h3>
            </div>
            <button
              onClick={() => setShowMatrix(!showMatrix)}
              className="text-[10px] font-bold text-red-600 underline cursor-pointer"
            >
              {showMatrix ? 'Hide Details' : 'Show Score Table'}
            </button>
          </div>

          <p className="text-[11px] text-slate-500">
            Compare target scores across IELTS Academic, PTE Academic, TOEFL iBT, and Duolingo.
          </p>

          {showMatrix && (
            <div className="overflow-x-auto no-scrollbar pt-1">
              <table className="w-full text-[10px] text-left border-collapse">
                <thead>
                  <tr className="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                    <th className="p-1.5 rounded-l-lg">IELTS</th>
                    <th className="p-1.5">PTE</th>
                    <th className="p-1.5">TOEFL</th>
                    <th className="p-1.5">DET</th>
                    <th className="p-1.5 rounded-r-lg">Acceptance Level</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100 font-medium">
                  {testEquivalencies.map((row, i) => (
                    <tr key={i} className="hover:bg-slate-50">
                      <td className="p-1.5 font-bold text-red-600">{row.ielts}</td>
                      <td className="p-1.5 font-bold text-slate-800">{row.pte}</td>
                      <td className="p-1.5">{row.toefl}</td>
                      <td className="p-1.5">{row.det}</td>
                      <td className="p-1.5 text-slate-500">{row.level}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {/* FILTER & SORT APPLICATION CONTROL BAR */}
      <div className="px-4 space-y-3">
        {/* Search Bar + Sort Dropdown */}
        <div className="bg-white rounded-2xl p-3 border border-slate-200/90 shadow-xs space-y-3">
          <div className="flex items-center space-x-2">
            <div className="relative flex-1">
              <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search test name, code, or degree (e.g. PTE, GRE, German)..."
                className="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              />
              {searchQuery && (
                <button
                  onClick={() => setSearchQuery('')}
                  className="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                  aria-label="Clear search"
                >
                  <X className="w-3.5 h-3.5" />
                </button>
              )}
            </div>

            <button
              onClick={() => setShowFiltersPanel(!showFiltersPanel)}
              className={`p-2 rounded-xl border text-xs font-bold flex items-center gap-1 transition cursor-pointer shrink-0 ${
                showFiltersPanel || selectedDifficulty !== 'All'
                  ? 'bg-blue-50 text-blue-700 border-blue-300'
                  : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
              }`}
              title="Toggle Advanced Filter Panel"
            >
              <SlidersHorizontal className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">Filters</span>
            </button>
          </div>

          {/* Sort By Segmented Selector */}
          <div className="space-y-1.5 pt-1 border-t border-slate-100">
            <div className="flex items-center justify-between text-[11px]">
              <span className="font-bold text-slate-700 flex items-center gap-1">
                <ArrowUpDown className="w-3 h-3 text-blue-600" />
                <span>Sort Tests By:</span>
              </span>
              <span className="text-[10px] text-slate-400">
                Sorted by {sortBy.charAt(0).toUpperCase() + sortBy.slice(1)}
              </span>
            </div>

            <div className="grid grid-cols-4 gap-1.5">
              <button
                onClick={() => setSortBy('popularity')}
                className={`py-1.5 px-2 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer ${
                  sortBy === 'popularity'
                    ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <Flame className="w-3 h-3 shrink-0" />
                <span className="truncate">Popularity</span>
              </button>

              <button
                onClick={() => setSortBy('category')}
                className={`py-1.5 px-2 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer ${
                  sortBy === 'category'
                    ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <Layers className="w-3 h-3 shrink-0" />
                <span className="truncate">Category</span>
              </button>

              <button
                onClick={() => setSortBy('difficulty')}
                className={`py-1.5 px-2 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer ${
                  sortBy === 'difficulty'
                    ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <Gauge className="w-3 h-3 shrink-0" />
                <span className="truncate">Difficulty</span>
              </button>

              <button
                onClick={() => setSortBy('duration')}
                className={`py-1.5 px-2 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer ${
                  sortBy === 'duration'
                    ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <Clock className="w-3 h-3 shrink-0" />
                <span className="truncate">Duration</span>
              </button>
            </div>
          </div>

          {/* Advanced Difficulty Panel */}
          {(showFiltersPanel || selectedDifficulty !== 'All') && (
            <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
              <div className="flex items-center justify-between text-[11px]">
                <span className="font-bold text-slate-700 flex items-center gap-1">
                  <Gauge className="w-3 h-3 text-blue-600" />
                  <span>Filter by Exam Difficulty Level:</span>
                </span>
                {selectedDifficulty !== 'All' && (
                  <button
                    onClick={() => setSelectedDifficulty('All')}
                    className="text-[10px] text-blue-600 font-bold hover:underline cursor-pointer"
                  >
                    Reset
                  </button>
                )}
              </div>

              <div className="flex flex-wrap gap-1.5">
                {difficulties.map((diff) => {
                  const isSelected = selectedDifficulty === diff;
                  return (
                    <button
                      key={diff}
                      onClick={() => setSelectedDifficulty(diff)}
                      className={`px-2.5 py-1 rounded-lg text-[10px] font-bold transition border cursor-pointer ${
                        isSelected
                          ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                          : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                      }`}
                    >
                      {diff}
                    </button>
                  );
                })}
              </div>
            </div>
          )}

          {/* Active Filter Chips / Reset Bar */}
          {hasActiveFilters && (
            <div className="flex items-center justify-between pt-1 border-t border-slate-100 text-[11px]">
              <span className="text-slate-500 font-medium">
                Active: {selectedCategory !== 'All' ? `${selectedCategory} • ` : ''}
                {selectedDifficulty !== 'All' ? `${selectedDifficulty} • ` : ''}
                Sorted by {sortBy}
              </span>
              <button
                onClick={resetAllFilters}
                className="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1 cursor-pointer hover:underline"
              >
                <RotateCcw className="w-3 h-3" />
                <span>Reset All</span>
              </button>
            </div>
          )}
        </div>

        {/* Category Horizontal Filter Pills */}
        <div className="space-y-1">
          <span className="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
            Filter By Exam Category:
          </span>
          <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-1">
            {categories.map((cat) => {
              const isSelected = selectedCategory === cat;
              return (
                <button
                  key={cat}
                  onClick={() => setSelectedCategory(cat)}
                  className={`px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0 border ${
                    isSelected
                      ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                      : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                  }`}
                >
                  {cat}
                </button>
              );
            })}
          </div>
        </div>
      </div>

      {/* Tests Grid List */}
      <div className="px-4 space-y-3">
        {processedTests.length === 0 ? (
          <div className="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3 shadow-xs">
            <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
              <Search className="w-6 h-6" />
            </div>
            <h3 className="font-extrabold text-sm text-slate-900">
              No matching test programs found
            </h3>
            <p className="text-xs text-slate-500 max-w-xs mx-auto">
              We couldn't find any test programs matching "{searchQuery}" with the current filters.
            </p>
            <button
              onClick={resetAllFilters}
              className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs inline-flex items-center space-x-1.5 transition cursor-pointer shadow-xs"
            >
              <RotateCcw className="w-3.5 h-3.5" />
              <span>Reset All Filters</span>
            </button>
          </div>
        ) : (
          processedTests.map((test) => (
            <div
              key={test.id}
              onClick={() => onSelectTest(test.id)}
              className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-blue-300 transition cursor-pointer flex flex-col justify-between group"
            >
              {/* Banner with Badges */}
              <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
                <img
                  src={test.bannerImage}
                  alt={test.name}
                  className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

                {/* Top Left Badges: Code + Category + Popularity Rank */}
                <div className="absolute top-2.5 left-2.5 flex items-center gap-1.5 flex-wrap">
                  <span className="text-[9px] font-black uppercase tracking-wider bg-blue-600 text-white px-2 py-0.5 rounded-md shadow-xs">
                    {test.code}
                  </span>
                  <span className="text-[9px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 px-2 py-0.5 rounded-md">
                    {test.category}
                  </span>
                  <span className="text-[9px] font-extrabold bg-amber-400 text-slate-900 px-2 py-0.5 rounded-md shadow-xs flex items-center gap-0.5">
                    <Flame className="w-2.5 h-2.5 fill-slate-900" />
                    <span>Rank #{test.popularityRank}</span>
                  </span>
                </div>

                {/* Top Right Difficulty Tag & Heart Button */}
                <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                  <span className={`text-[9px] font-extrabold px-2 py-0.5 rounded-md border shadow-xs ${getDifficultyBadgeColor(test.difficultyLevel || 'Intermediate')}`}>
                    {test.difficultyLevel}
                  </span>
                  <BookmarkButton
                    type="test"
                    id={test.id}
                    title={test.name}
                    size="xs"
                    variant="glass"
                  />
                </div>

                <div className="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                  <h3 className="font-extrabold text-sm leading-tight group-hover:text-blue-300 transition-colors">
                    {test.name}
                  </h3>
                </div>
              </div>

              {/* Content Body */}
              <div className="p-3.5 space-y-2.5 text-xs">
                <div className="flex items-center justify-between text-[10px] text-slate-500 font-medium">
                  <span className="text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200 font-semibold">
                    {test.popularityLabel}
                  </span>
                  <span className="flex items-center gap-1">
                    <Clock className="w-3 h-3 text-slate-400" />
                    {test.duration}
                  </span>
                </div>

                <p className="text-[11px] text-slate-600 leading-relaxed font-normal">
                  {test.tagline}
                </p>

                <div className="grid grid-cols-2 gap-2 p-2 rounded-xl bg-slate-50 border border-slate-100 text-[10px]">
                  <div>
                    <span className="text-slate-400 block font-medium">Target Score:</span>
                    <strong className="text-blue-700 font-bold">{test.minTargetBand}</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block font-medium">Test Format:</span>
                    <strong className="text-slate-800 font-bold truncate block">{test.testFormat.split(':')[0]}</strong>
                  </div>
                </div>

                <div className="pt-1 flex flex-wrap gap-1.5">
                  {test.transGlobeHighlights.slice(0, 2).map((h, i) => (
                    <span
                      key={i}
                      className="text-[10px] font-medium bg-slate-50 border border-slate-200/80 text-slate-700 px-2 py-0.5 rounded-md flex items-center gap-1"
                    >
                      <CheckCircle2 className="w-2.5 h-2.5 text-blue-500" />
                      <span className="truncate max-w-[240px]">{h}</span>
                    </span>
                  ))}
                </div>

                <div className="pt-2 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] text-slate-500">
                    {test.modules.length} Modules • Indore Offline / Online
                  </span>

                  <span className="text-xs font-bold text-blue-600 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                    <span>View Preparation Details</span>
                    <ChevronRight className="w-3.5 h-3.5" />
                  </span>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Bottom Consultation Box */}
      <div className="px-4">
        <div className="p-4 rounded-3xl bg-slate-900 text-white space-y-2.5 shadow-md">
          <div className="flex items-center space-x-2">
            <BookOpen className="w-5 h-5 text-red-500" />
            <h4 className="font-extrabold text-sm">Need a Free Diagnostic Mock Test?</h4>
          </div>
          <p className="text-xs text-slate-300 leading-relaxed">
            Take a free full-length IELTS / PTE / GRE mock test at Trans Globe Indore center with instant section-by-section AI scoring.
          </p>
          <div className="grid grid-cols-2 gap-2 pt-1">
            <button
              onClick={() => onBookConsultation('Diagnostic Mock Test')}
              className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Book Free Mock</span>
            </button>
            <a
              href="tel:+919826666886"
              className="bg-white/10 hover:bg-white/20 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition"
            >
              <Clock className="w-3.5 h-3.5" />
              <span>Class Timings</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};
