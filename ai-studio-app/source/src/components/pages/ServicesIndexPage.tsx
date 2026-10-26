import React, { useState, useMemo } from 'react';
import { 
  ArrowLeft, 
  Briefcase, 
  ChevronRight, 
  Sparkles, 
  CheckCircle2, 
  ShieldCheck, 
  Phone, 
  MessageCircle, 
  Menu,
  Check,
  Search,
  SlidersHorizontal,
  Flame,
  Layers,
  Gauge,
  X,
  RotateCcw,
  ArrowUpDown
} from 'lucide-react';
import Fuse from 'fuse.js';
import { GEIC_SERVICES } from '../../data/liveCatalog';
import { GeicService, StudyDestination } from '../../types';
import { UniversityComparisonData } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';
import { GlobalSearchBar } from '../common/GlobalSearchBar';

interface ServicesIndexPageProps {
  onBack: () => void;
  onSelectService: (serviceId: string) => void;
  onOpenBooking: (serviceTitle?: string) => void;
  onOpenDrawer?: () => void;
  onSelectTest?: (testId: string) => void;
  onSelectEvent?: (eventId: string) => void;
  onSelectDestination?: (destination: StudyDestination) => void;
  onCompareUniversities?: (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
  onOpenExpo?: () => void;
}

// Meta mappings for services difficulty and popularity
const SERVICE_META: Record<string, { difficulty: string; popularityRank: number; popularityLabel: string; timeEstimate: string }> = {};

type SortOption = 'popularity' | 'category' | 'difficulty' | 'step';

export const ServicesIndexPage: React.FC<ServicesIndexPageProps> = ({
  onBack,
  onSelectService,
  onOpenBooking,
  onOpenDrawer,
  onSelectTest,
  onSelectEvent,
  onSelectDestination,
  onCompareUniversities,
  onOpenExpo,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('All');
  const [selectedDifficulty, setSelectedDifficulty] = useState<string>('All');
  const [sortBy, setSortBy] = useState<SortOption>('popularity');
  const [showFiltersPanel, setShowFiltersPanel] = useState(false);

  const categories = ['All', 'Admissions', 'Visas', 'Finance & Grants', 'Test Preparation', 'Departure & Housing', 'Immigration'];
  const difficulties = ['All', 'Essential', 'Moderate', 'Comprehensive', 'Complex'];

  // Difficulty numeric weight for sorting
  const difficultyWeight: Record<string, number> = {
    Essential: 1,
    Moderate: 2,
    Comprehensive: 3,
    Complex: 4,
  };

  // Fuse.js index for services fuzzy search
  const servicesFuse = useMemo(() => {
    return new Fuse(GEIC_SERVICES, {
      keys: [
        { name: 'title', weight: 0.4 },
        { name: 'category', weight: 0.2 },
        { name: 'shortDesc', weight: 0.2 },
        { name: 'keyBenefits', weight: 0.1 },
        { name: 'whoNeedsThis', weight: 0.1 },
      ],
      threshold: 0.38,
      ignoreLocation: true,
    });
  }, []);

  // Filter and sort services
  const processedServices = useMemo(() => {
    const matchingIds = searchQuery.trim()
      ? new Set(servicesFuse.search(searchQuery.trim()).map((r) => r.item.id))
      : null;

    return GEIC_SERVICES
      .map((service) => {
        const meta = SERVICE_META[service.id] || {
          difficulty: 'Not provided',
          popularityRank: 99,
          popularityLabel: 'GEIC Service',
          timeEstimate: 'Confirm with counsellor',
        };
        return {
          ...service,
          difficultyLevel: service.difficultyLevel || meta.difficulty,
          popularityRank: service.popularityRank || meta.popularityRank,
          popularityLabel: meta.popularityLabel,
          timeEstimate: meta.timeEstimate,
        };
      })
      .filter((service) => {
        // Fuzzy Search query filter
        if (matchingIds && !matchingIds.has(service.id)) {
          return false;
        }

        // Category filter
        if (selectedCategory !== 'All' && service.category !== selectedCategory) {
          return false;
        }

        // Difficulty filter
        if (selectedDifficulty !== 'All' && service.difficultyLevel !== selectedDifficulty) {
          return false;
        }

        return true;
      })
      .sort((a, b) => {
        if (sortBy === 'popularity') {
          return (a.popularityRank || 99) - (b.popularityRank || 99);
        }
        if (sortBy === 'category') {
          return a.category.localeCompare(b.category) || a.title.localeCompare(b.title);
        }
        if (sortBy === 'difficulty') {
          const wA = difficultyWeight[a.difficultyLevel || 'Moderate'] || 2;
          const wB = difficultyWeight[b.difficultyLevel || 'Moderate'] || 2;
          return wA - wB;
        }
        if (sortBy === 'step') {
          return parseInt(a.stepNumber, 10) - parseInt(b.stepNumber, 10);
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
      case 'Essential':
        return 'bg-emerald-50 text-emerald-700 border-emerald-200';
      case 'Moderate':
        return 'bg-blue-50 text-blue-700 border-blue-200';
      case 'Comprehensive':
        return 'bg-purple-50 text-purple-700 border-purple-200';
      case 'Complex':
        return 'bg-amber-50 text-amber-800 border-amber-300';
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
            All Services Hub
          </span>
          <span className="text-[10px] text-slate-500 font-medium">
            {processedServices.length} of {GEIC_SERVICES.length} Available
          </span>
        </div>

        <button
          onClick={() => onOpenBooking()}
          className="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1 transition shadow-xs cursor-pointer"
        >
          <Sparkles className="w-3.5 h-3.5" />
          <span>Book Free</span>
        </button>
      </header>

      {/* Hero Header Card */}
      <div className="px-4 space-y-2">
        <div className="flex items-center space-x-2">
          <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">
            34+ Years of Trust in Indore
          </span>
          <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
            100% Free Guidance
          </span>
        </div>
        <h1 className="text-xl font-black text-slate-900 leading-tight">
          Comprehensive Study Abroad Services
        </h1>
        <p className="text-xs text-slate-500 leading-relaxed">
          From your first career counselling session in Indore to airport arrival, student housing, and permanent residency abroad.
        </p>
      </div>

      {/* Global Fuzzy Search Bar across services, tests, events, destinations */}
      <div className="px-4">
        <GlobalSearchBar
          placeholder="Global search services, tests, events, universities, destinations..."
          onSelectService={onSelectService}
          onSelectTest={onSelectTest}
          onSelectEvent={onSelectEvent}
          onSelectDestination={onSelectDestination}
          onCompareUniversities={onCompareUniversities}
          onOpenExpo={onOpenExpo}
          defaultFilter="service"
          allowFilterSwitching={true}
          showSuggestions={false}
        />
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
                placeholder="Search services (e.g. Visa, SOP, Scholarships, Forex)..."
                className="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500 transition"
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
                  ? 'bg-red-50 text-red-700 border-red-300'
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
                <ArrowUpDown className="w-3 h-3 text-red-600" />
                <span>Sort Services By:</span>
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
                    ? 'bg-red-600 text-white border-red-600 shadow-xs'
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
                    ? 'bg-red-600 text-white border-red-600 shadow-xs'
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
                    ? 'bg-red-600 text-white border-red-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <Gauge className="w-3 h-3 shrink-0" />
                <span className="truncate">Difficulty</span>
              </button>

              <button
                onClick={() => setSortBy('step')}
                className={`py-1.5 px-2 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer ${
                  sortBy === 'step'
                    ? 'bg-red-600 text-white border-red-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                <span className="text-[10px]">1→8</span>
                <span className="truncate">Step Order</span>
              </button>
            </div>
          </div>

          {/* Advanced Difficulty Panel (collapsible or toggleable) */}
          {(showFiltersPanel || selectedDifficulty !== 'All') && (
            <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
              <div className="flex items-center justify-between text-[11px]">
                <span className="font-bold text-slate-700 flex items-center gap-1">
                  <Gauge className="w-3 h-3 text-amber-600" />
                  <span>Filter by Complexity / Scrutiny Level:</span>
                </span>
                {selectedDifficulty !== 'All' && (
                  <button
                    onClick={() => setSelectedDifficulty('All')}
                    className="text-[10px] text-red-600 font-bold hover:underline cursor-pointer"
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
                className="text-red-600 hover:text-red-700 font-bold flex items-center gap-1 cursor-pointer hover:underline"
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
            Filter By Service Domain:
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
                      ? 'bg-red-600 text-white border-red-600 shadow-xs'
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

      {/* Services Grid List */}
      <div className="px-4 space-y-3">
        {processedServices.length === 0 ? (
          <div className="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3 shadow-xs">
            <div className="w-12 h-12 rounded-2xl bg-red-50 text-red-600 mx-auto flex items-center justify-center">
              <Search className="w-6 h-6" />
            </div>
            <h3 className="font-extrabold text-sm text-slate-900">
              No matching services found
            </h3>
            <p className="text-xs text-slate-500 max-w-xs mx-auto">
              We couldn't find any services matching "{searchQuery}" with the current filters.
            </p>
            <button
              onClick={resetAllFilters}
              className="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-xl text-xs inline-flex items-center space-x-1.5 transition cursor-pointer shadow-xs"
            >
              <RotateCcw className="w-3.5 h-3.5" />
              <span>Reset All Filters</span>
            </button>
          </div>
        ) : (
          processedServices.map((service) => (
            <div
              key={service.id}
              onClick={() => onSelectService(service.id)}
              className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-red-300 transition cursor-pointer flex flex-col justify-between group"
            >
              {/* Banner Image with Badges */}
              <div className="relative h-32 w-full bg-slate-900 overflow-hidden">
                <img
                  src={service.bannerImage}
                  alt={service.title}
                  className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

                {/* Top Badges: Step + Category + Popularity Rank */}
                <div className="absolute top-2.5 left-2.5 flex items-center gap-1.5 flex-wrap">
                  <span className="text-[9px] font-black uppercase tracking-wider bg-red-600 text-white px-2 py-0.5 rounded-md shadow-xs">
                    Step {service.stepNumber}
                  </span>
                  <span className="text-[9px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 px-2 py-0.5 rounded-md">
                    {service.category}
                  </span>
                  <span className="text-[9px] font-extrabold bg-amber-400 text-slate-900 px-2 py-0.5 rounded-md shadow-xs flex items-center gap-0.5">
                    <Flame className="w-2.5 h-2.5 fill-slate-900" />
                    <span>Rank #{service.popularityRank}</span>
                  </span>
                </div>

                {/* Top Right Difficulty Tag & Heart Button */}
                <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                  <span className={`text-[9px] font-extrabold px-2 py-0.5 rounded-md border shadow-xs ${getDifficultyBadgeColor(service.difficultyLevel || 'Moderate')}`}>
                    {service.difficultyLevel}
                  </span>
                  <BookmarkButton
                    type="service"
                    id={service.id}
                    title={service.title}
                    size="xs"
                    variant="glass"
                  />
                </div>

                <div className="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                  <h3 className="font-extrabold text-sm leading-tight group-hover:text-amber-300 transition-colors">
                    {service.title}
                  </h3>
                </div>
              </div>

              {/* Service Content Body */}
              <div className="p-3.5 space-y-2.5 text-xs">
                <div className="flex items-center justify-between text-[10px] text-slate-500 font-medium">
                  <span className="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 font-semibold">
                    {service.popularityLabel}
                  </span>
                  <span>Est. Timeline: {service.timeEstimate}</span>
                </div>

                <p className="text-[11px] text-slate-600 leading-relaxed font-normal">
                  {service.shortDesc}
                </p>

                <div className="flex flex-wrap gap-1.5 pt-1">
                  {service.keyBenefits.slice(0, 2).map((b, i) => (
                    <span
                      key={i}
                      className="text-[10px] font-medium bg-slate-50 border border-slate-200/80 text-slate-700 px-2 py-0.5 rounded-md flex items-center gap-1"
                    >
                      <CheckCircle2 className="w-2.5 h-2.5 text-emerald-500" />
                      <span>{b}</span>
                    </span>
                  ))}
                </div>

                <div className="pt-2 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] text-slate-500">
                    {service.detailedProcess.length} Step Workflow • {service.popularDestinations.length} Countries
                  </span>

                  <span className="text-xs font-bold text-red-600 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                    <span>View Dedicated Page</span>
                    <ChevronRight className="w-3.5 h-3.5" />
                  </span>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Bottom CTA Card */}
      <div className="px-4">
        <div className="p-4 rounded-3xl bg-slate-900 text-white space-y-2.5 shadow-md">
          <div className="flex items-center space-x-2">
            <ShieldCheck className="w-5 h-5 text-red-500" />
            <h4 className="font-extrabold text-sm">Need Help Deciding?</h4>
          </div>
          <p className="text-xs text-slate-300 leading-relaxed">
            Our senior mentors in Indore analyze your GPA, budget, and target countries to design a personalized multi-step roadmap.
          </p>
          <div className="grid grid-cols-2 gap-2 pt-1">
            <button
              onClick={() => onOpenBooking('All Services Consultation')}
              className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Book Counselling</span>
            </button>
            <a
              href="tel:+919826666886"
              className="bg-white/10 hover:bg-white/20 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition"
            >
              <Phone className="w-3.5 h-3.5" />
              <span>Call Indore Desk</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};
