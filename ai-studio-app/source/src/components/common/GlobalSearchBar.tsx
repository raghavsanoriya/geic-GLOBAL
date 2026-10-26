import React, { useState, useMemo, useRef, useEffect } from 'react';
import { 
  Search, 
  X, 
  Briefcase, 
  BookOpen, 
  Calendar, 
  Compass, 
  ChevronRight, 
  Sparkles, 
  RotateCcw,
  SlidersHorizontal,
  Flame,
  CheckCircle2,
  TrendingUp,
  MapPin,
  History,
  Clock,
  Trash2,
  CornerDownLeft,
  GraduationCap,
  ArrowLeftRight,
  Scale
} from 'lucide-react';
import { performFuzzySearch, SearchItemType, GlobalSearchItem } from '../../utils/fuzzySearch';
import { 
  getRecentSearches, 
  saveRecentSearch, 
  removeRecentSearch, 
  clearAllRecentSearches 
} from '../../utils/recentSearches';
import { 
  UniversityComparisonData, 
  GLOBAL_UNIVERSITIES 
} from '../../data/liveCatalog';
import { BookmarkButton } from './BookmarkButton';
import { StudyDestination } from '../../types';

interface GlobalSearchBarProps {
  placeholder?: string;
  onSelectService?: (serviceId: string) => void;
  onSelectTest?: (testId: string) => void;
  onSelectEvent?: (eventId: string) => void;
  onSelectDestination?: (destination: StudyDestination) => void;
  onSelectUniversity?: (university: UniversityComparisonData) => void;
  onCompareUniversities?: (uni1: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
  onOpenExpo?: () => void;
  defaultFilter?: 'all' | SearchItemType;
  allowFilterSwitching?: boolean;
  className?: string;
  autoFocus?: boolean;
  showSuggestions?: boolean;
}

export const GlobalSearchBar: React.FC<GlobalSearchBarProps> = ({
  placeholder = 'Search services, tests, events, universities, destinations (e.g. ASU, Visa, IELTS, TUM)...',
  onSelectService,
  onSelectTest,
  onSelectEvent,
  onSelectDestination,
  onSelectUniversity,
  onCompareUniversities,
  onOpenExpo,
  defaultFilter = 'all',
  allowFilterSwitching = true,
  className = '',
  autoFocus = false,
  showSuggestions = true,
}) => {
  const [query, setQuery] = useState('');
  const [selectedType, setSelectedType] = useState<'all' | SearchItemType>(defaultFilter);
  const [isFocused, setIsFocused] = useState(false);
  const [recentSearches, setRecentSearches] = useState<string[]>([]);
  const [compareQueue, setCompareQueue] = useState<UniversityComparisonData[]>([]);
  const containerRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Load user's recent searches from localStorage on mount
  useEffect(() => {
    setRecentSearches(getRecentSearches());
  }, []);

  // Popular search queries for instant discovery
  const popularSearches = [
    { label: 'Visa Filing', type: 'service' as const, query: 'Visa' },
    { label: 'IELTS Coaching', type: 'test' as const, query: 'IELTS' },
    { label: 'Uni Expo 2026', type: 'event' as const, query: 'Expo' },
    { label: 'Study in Germany', type: 'destination' as const, query: 'Germany' },
    { label: 'Compare Universities', type: 'university' as const, query: 'University' },
    { label: 'Scholarships', type: 'service' as const, query: 'Scholarship' },
    { label: 'PTE Academic', type: 'test' as const, query: 'PTE' },
  ];

  // Perform fuzzy search using Fuse.js
  const searchResults = useMemo(() => {
    return performFuzzySearch(query, selectedType, 24);
  }, [query, selectedType]);

  // Click outside listener to collapse results dropdown when not active
  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setIsFocused(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleInputFocus = () => {
    setIsFocused(true);
    setRecentSearches(getRecentSearches());
  };

  const handleInputClick = () => {
    setIsFocused(true);
    setRecentSearches(getRecentSearches());
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter' && query.trim()) {
      const updated = saveRecentSearch(query.trim());
      setRecentSearches(updated);
    } else if (e.key === 'Escape') {
      setIsFocused(false);
    }
  };

  const handleSelectRecentSearch = (recentQuery: string) => {
    setQuery(recentQuery);
    const updated = saveRecentSearch(recentQuery);
    setRecentSearches(updated);
    inputRef.current?.focus();
  };

  const handleRemoveRecentSearch = (e: React.MouseEvent, recentQuery: string) => {
    e.stopPropagation();
    const updated = removeRecentSearch(recentQuery);
    setRecentSearches(updated);
  };

  const handleClearAllRecent = (e: React.MouseEvent) => {
    e.stopPropagation();
    clearAllRecentSearches();
    setRecentSearches([]);
  };

  const handleToggleCompare = (e: React.MouseEvent, uni: UniversityComparisonData) => {
    e.stopPropagation();
    if (compareQueue.some((u) => u.id === uni.id)) {
      setCompareQueue(compareQueue.filter((u) => u.id !== uni.id));
    } else {
      if (compareQueue.length >= 2) {
        setCompareQueue([compareQueue[1], uni]);
      } else {
        const next = [...compareQueue, uni];
        setCompareQueue(next);
        if (next.length === 2) {
          // If two are selected, automatically launch side-by-side comparison
          setTimeout(() => {
            setIsFocused(false);
            onCompareUniversities?.(next[0], next[1]);
          }, 250);
        }
      }
    }
  };

  const handleSelectItem = (item: GlobalSearchItem) => {
    setIsFocused(false);
    
    // Save to recent searches
    if (query.trim()) {
      const updated = saveRecentSearch(query.trim());
      setRecentSearches(updated);
    } else {
      const cleanTitle = item.title.replace(/^[^\w\s]+/, '').trim();
      const updated = saveRecentSearch(cleanTitle);
      setRecentSearches(updated);
    }

    if (item.type === 'service') {
      onSelectService?.(item.id);
    } else if (item.type === 'test') {
      onSelectTest?.(item.id);
    } else if (item.type === 'event') {
      if (item.id === 'expo-2026' && onOpenExpo) {
        onOpenExpo();
      } else {
        onSelectEvent?.(item.id);
      }
    } else if (item.type === 'destination') {
      onSelectDestination?.(item.rawItem as StudyDestination);
    } else if (item.type === 'university') {
      const uni = item.rawItem as UniversityComparisonData;
      if (compareQueue.length === 1 && compareQueue[0].id !== uni.id) {
        onCompareUniversities?.(compareQueue[0], uni);
        setCompareQueue([]);
      } else {
        onCompareUniversities?.(uni);
      }
    }
  };

  const getTypeIcon = (type: SearchItemType) => {
    switch (type) {
      case 'service':
        return <Briefcase className="w-3.5 h-3.5 text-red-500" />;
      case 'test':
        return <BookOpen className="w-3.5 h-3.5 text-blue-500" />;
      case 'event':
        return <Calendar className="w-3.5 h-3.5 text-amber-500" />;
      case 'destination':
        return <Compass className="w-3.5 h-3.5 text-emerald-500" />;
      case 'university':
        return <GraduationCap className="w-3.5 h-3.5 text-purple-600" />;
    }
  };

  const getTypeBadge = (type: SearchItemType) => {
    switch (type) {
      case 'service':
        return 'bg-red-50 text-red-700 border-red-200';
      case 'test':
        return 'bg-blue-50 text-blue-700 border-blue-200';
      case 'event':
        return 'bg-amber-50 text-amber-800 border-amber-200';
      case 'destination':
        return 'bg-emerald-50 text-emerald-700 border-emerald-200';
      case 'university':
        return 'bg-purple-50 text-purple-700 border-purple-200';
    }
  };

  const getScoreBadge = (score?: number) => {
    if (score === undefined || score === 0) return null;
    if (score < 0.2) {
      return (
        <span className="text-[9px] font-extrabold text-emerald-600 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded">
          Top Match
        </span>
      );
    }
    if (score < 0.35) {
      return (
        <span className="text-[9px] font-bold text-slate-500 bg-slate-50 border border-slate-200 px-1.5 py-0.2 rounded">
          Fuzzy Match
        </span>
      );
    }
    return null;
  };

  const totalResultsCount = searchResults.length;
  const showResultsDropdown = isFocused && (query.trim().length > 0 || isFocused);

  return (
    <div ref={containerRef} className={`relative z-40 ${className}`}>
      {/* Search Bar Input Container */}
      <div className="relative">
        <div className="absolute left-3.5 top-3 text-slate-400 pointer-events-none flex items-center">
          <Search className="w-4 h-4 text-slate-400" />
        </div>

        <input
          ref={inputRef}
          type="text"
          value={query}
          autoFocus={autoFocus}
          onClick={handleInputClick}
          onFocus={handleInputFocus}
          onKeyDown={handleKeyDown}
          onChange={(e) => {
            setQuery(e.target.value);
            setIsFocused(true);
          }}
          placeholder={placeholder}
          className="w-full pl-9 pr-20 py-2.5 rounded-2xl border border-slate-200/90 text-xs font-medium bg-white shadow-xs focus:outline-none focus:ring-2 focus:ring-red-500 transition text-slate-900 placeholder:text-slate-400"
        />

        {/* Clear & Quick status indicator */}
        <div className="absolute right-2.5 top-2 flex items-center space-x-1">
          {query && (
            <button
              onClick={() => {
                setQuery('');
                inputRef.current?.focus();
              }}
              className="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer"
              title="Clear search"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}

          <span className="text-[10px] font-black uppercase text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md hidden sm:inline">
            Fuzzy
          </span>
        </div>
      </div>

      {/* FILTER PILLS (When filter switching is allowed) */}
      {allowFilterSwitching && (
        <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar pt-2 pb-0.5">
          <button
            type="button"
            onClick={() => setSelectedType('all')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'all'
                ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            All Categories
          </button>

          <button
            type="button"
            onClick={() => setSelectedType('service')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'service'
                ? 'bg-red-600 text-white border-red-600 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <Briefcase className="w-3 h-3" />
            <span>Services</span>
          </button>

          <button
            type="button"
            onClick={() => setSelectedType('test')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'test'
                ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <BookOpen className="w-3 h-3" />
            <span>Tests</span>
          </button>

          <button
            type="button"
            onClick={() => setSelectedType('event')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'event'
                ? 'bg-amber-600 text-white border-amber-600 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <Calendar className="w-3 h-3" />
            <span>Events</span>
          </button>

          <button
            type="button"
            onClick={() => setSelectedType('destination')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'destination'
                ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <Compass className="w-3 h-3" />
            <span>Destinations</span>
          </button>

          <button
            type="button"
            onClick={() => setSelectedType('university')}
            className={`px-2.5 py-1 rounded-xl text-[11px] font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
              selectedType === 'university'
                ? 'bg-purple-600 text-white border-purple-600 shadow-xs'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <GraduationCap className="w-3 h-3" />
            <span>Universities</span>
          </button>
        </div>
      )}

      {/* EXPANDABLE SEARCH RESULTS DROPDOWN / PANEL */}
      {showResultsDropdown && (
        <div className="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden max-h-[75vh] flex flex-col z-50 animate-in fade-in slide-in-from-top-2 duration-150">
          {/* Header Bar inside dropdown */}
          <div className="px-3.5 py-2 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between text-[11px]">
            <span className="font-bold text-slate-700 flex items-center gap-1">
              <Sparkles className="w-3 h-3 text-red-500" />
              <span>
                {query.trim()
                  ? `Found ${totalResultsCount} results for "${query}"`
                  : 'Quick Search & Recent Queries'}
              </span>
            </span>

            <button
              onClick={() => setIsFocused(false)}
              className="text-slate-400 hover:text-slate-600 text-[10px] font-semibold cursor-pointer"
            >
              Close [ESC]
            </button>
          </div>

          {/* FLOATING COMPARE UNIVERSITIES DOCK (when 1 or 2 selected) */}
          {compareQueue.length > 0 && (
            <div className="m-2.5 p-3 rounded-2xl bg-stone-900 text-white flex items-center justify-between shadow-lg border border-stone-750 animate-in slide-in-from-top-2">
              <div className="flex items-center space-x-2.5 min-w-0 pr-2">
                <div className="w-8 h-8 rounded-xl bg-rose-600/30 text-rose-400 flex items-center justify-center shrink-0">
                  <ArrowLeftRight className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="flex items-center space-x-1.5">
                    <span className="font-extrabold text-white text-xs truncate">
                      {compareQueue.length === 1
                        ? `1 Selected: ${compareQueue[0].shortName}`
                        : `Ready: ${compareQueue[0].shortName} vs ${compareQueue[1].shortName}`}
                    </span>
                    <span className="text-[10px] bg-rose-950 text-rose-300 border border-rose-800/60 font-bold px-1.5 py-0.2 rounded-md">
                      {compareQueue.length}/2
                    </span>
                  </div>
                  <p className="text-[10px] text-stone-400 truncate">
                    {compareQueue.length === 1
                      ? 'Tap "+ Compare" on another university'
                      : 'Compare fees, acceptance & IELTS side-by-side'}
                  </p>
                </div>
              </div>

              <div className="flex items-center space-x-1.5 shrink-0">
                <button
                  type="button"
                  onClick={() => {
                    setIsFocused(false);
                    if (compareQueue.length >= 2) {
                      onCompareUniversities?.(compareQueue[0], compareQueue[1]);
                    } else {
                      const fallback = GLOBAL_UNIVERSITIES.find((u) => u.id !== compareQueue[0].id) || GLOBAL_UNIVERSITIES[1];
                      onCompareUniversities?.(compareQueue[0], fallback);
                    }
                  }}
                  className="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1 shadow-xs cursor-pointer"
                >
                  <Sparkles className="w-3 h-3" />
                  <span>Compare</span>
                </button>

                <button
                  type="button"
                  onClick={() => setCompareQueue([])}
                  className="p-1.5 rounded-lg text-stone-400 hover:text-white hover:bg-stone-800 transition cursor-pointer"
                  title="Clear comparison queue"
                >
                  <X className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          )}

          {/* RECENT SEARCHES SECTION (Last 5 searches stored in localStorage) */}
          {recentSearches.length > 0 && query.trim().length === 0 && (
            <div className="p-3 border-b border-slate-100 bg-slate-50/70 space-y-2">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-1.5 text-xs font-bold text-slate-800">
                  <History className="w-3.5 h-3.5 text-slate-500" />
                  <span>Recent Searches</span>
                  <span className="text-[10px] bg-slate-200 text-slate-700 font-extrabold px-1.5 py-0.2 rounded-full">
                    {recentSearches.length} / 5
                  </span>
                </div>

                <button
                  type="button"
                  onClick={handleClearAllRecent}
                  className="text-[10px] font-bold text-red-600 hover:text-red-700 hover:underline flex items-center gap-1 cursor-pointer transition"
                  title="Clear recent searches"
                >
                  <Trash2 className="w-3 h-3" />
                  <span>Clear History</span>
                </button>
              </div>

              {/* Last 5 searches chips */}
              <div className="flex flex-wrap gap-1.5">
                {recentSearches.map((term, idx) => (
                  <div
                    key={idx}
                    onClick={() => handleSelectRecentSearch(term)}
                    className="group inline-flex items-center space-x-1.5 pl-2.5 pr-1.5 py-1 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-medium hover:border-red-400 hover:text-red-600 hover:bg-red-50/30 transition cursor-pointer shadow-2xs"
                  >
                    <Clock className="w-3 h-3 text-slate-400 group-hover:text-red-500 transition shrink-0" />
                    <span className="truncate max-w-[170px]">{term}</span>
                    <button
                      type="button"
                      onClick={(e) => handleRemoveRecentSearch(e, term)}
                      className="p-0.5 rounded-md hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition cursor-pointer"
                      title={`Remove "${term}"`}
                    >
                      <X className="w-3 h-3" />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Matching Recent Search Pill if query is typed */}
          {query.trim().length > 0 && recentSearches.some((s) => s.toLowerCase().includes(query.toLowerCase())) && (
            <div className="px-3 py-1.5 bg-slate-50 border-b border-slate-100 flex items-center gap-2 overflow-x-auto no-scrollbar">
              <span className="text-[10px] text-slate-400 font-bold shrink-0 flex items-center gap-1">
                <History className="w-3 h-3 text-slate-400" />
                <span>Recent History:</span>
              </span>
              {recentSearches
                .filter((s) => s.toLowerCase().includes(query.toLowerCase()))
                .map((term, i) => (
                  <button
                    key={i}
                    onClick={() => handleSelectRecentSearch(term)}
                    className="text-[11px] font-bold text-slate-700 hover:text-red-600 bg-white px-2 py-0.5 rounded-md border border-slate-200 shrink-0 cursor-pointer shadow-2xs"
                  >
                    {term}
                  </button>
                ))}
            </div>
          )}

          {/* Quick Popular Searches (when query is short or empty) */}
          {showSuggestions && query.trim().length === 0 && (
            <div className="p-3 border-b border-slate-100 bg-slate-50/40 space-y-1.5">
              <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                <TrendingUp className="w-3 h-3 text-amber-500" />
                <span>Trending Searches in Indore:</span>
              </span>
              <div className="flex flex-wrap gap-1.5">
                {popularSearches.map((s, i) => (
                  <button
                    key={i}
                    onClick={() => {
                      setQuery(s.query);
                      setSelectedType(s.type);
                    }}
                    className="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-medium hover:border-red-400 hover:text-red-600 transition cursor-pointer shadow-2xs"
                  >
                    {s.label}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Results List */}
          <div className="overflow-y-auto divide-y divide-slate-100 flex-1 p-1">
            {totalResultsCount === 0 ? (
              <div className="p-6 text-center space-y-2">
                <div className="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                  <Search className="w-5 h-5" />
                </div>
                <h4 className="text-xs font-bold text-slate-800">
                  No matches found for "{query}"
                </h4>
                <p className="text-[11px] text-slate-500 max-w-xs mx-auto">
                  Try fuzzy keywords like "Visa", "IELTS", "Germany", "Masterclass", or switch category filter to "All".
                </p>
                <button
                  onClick={() => {
                    setQuery('');
                    setSelectedType('all');
                  }}
                  className="text-xs font-bold text-red-600 hover:underline cursor-pointer"
                >
                  Reset Search
                </button>
              </div>
            ) : (
              searchResults.map(({ item, score }) => (
                <div
                  key={`${item.type}-${item.id}`}
                  onClick={() => handleSelectItem(item)}
                  className="p-2.5 hover:bg-slate-50/90 rounded-xl transition cursor-pointer flex items-center justify-between group gap-2.5"
                >
                  {/* Thumbnail / Flag */}
                  <div className="w-12 h-12 rounded-xl overflow-hidden bg-slate-900 shrink-0 relative border border-slate-200 shadow-2xs">
                    {item.imageUrl ? (
                      <img
                        src={item.imageUrl}
                        alt={item.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                      />
                    ) : (
                      <div className="w-full h-full flex items-center justify-center text-white text-xs font-black">
                        {item.badge}
                      </div>
                    )}
                    <div className="absolute bottom-0 inset-x-0 bg-slate-950/70 text-[8px] text-white font-bold text-center py-0.2">
                      {item.type.toUpperCase()}
                    </div>
                  </div>

                  {/* Body Content */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center space-x-1.5 flex-wrap">
                      <span className={`text-[9px] font-bold px-1.5 py-0.2 rounded border ${getTypeBadge(item.type)} flex items-center gap-1`}>
                        {getTypeIcon(item.type)}
                        <span className="capitalize">{item.type}</span>
                      </span>

                      {getScoreBadge(score)}

                      <span className="text-[10px] text-slate-400 font-medium truncate">
                        {item.subtitle}
                      </span>
                    </div>

                    <h4 className="font-extrabold text-xs text-slate-900 group-hover:text-red-600 transition truncate mt-0.5">
                      {item.title}
                    </h4>

                    <p className="text-[11px] text-slate-500 line-clamp-1">
                      {item.description}
                    </p>

                    {item.metaInfo && (
                      <span className="text-[10px] text-slate-400 font-medium block mt-0.5">
                        {item.metaInfo}
                      </span>
                    )}
                  </div>

                  {/* Bookmark Button + Compare + Navigation Arrow */}
                  <div className="flex items-center space-x-1 shrink-0">
                    {item.type === 'university' && (
                      <button
                        type="button"
                        onClick={(e) => handleToggleCompare(e, item.rawItem as UniversityComparisonData)}
                        className={`px-2.5 py-1 rounded-xl text-[10px] font-bold transition flex items-center gap-1 cursor-pointer shrink-0 border ${
                          compareQueue.some((u) => u.id === item.id)
                            ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
                            : 'bg-stone-100 hover:bg-rose-50 text-stone-700 hover:text-rose-700 border-stone-200'
                        }`}
                        title="Add to side-by-side comparison"
                      >
                        <ArrowLeftRight className="w-3 h-3" />
                        <span>{compareQueue.some((u) => u.id === item.id) ? 'Selected' : '+ Compare'}</span>
                      </button>
                    )}

                    {item.type !== 'destination' && item.type !== 'university' && (
                      <BookmarkButton
                        type={item.type as 'service' | 'test' | 'event'}
                        id={item.id}
                        title={item.title}
                        size="xs"
                        variant="white"
                      />
                    )}

                    <div className="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-red-600 group-hover:text-white text-slate-500 flex items-center justify-center transition">
                      <ChevronRight className="w-3.5 h-3.5" />
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>

          {/* Footer Bar */}
          <div className="px-3.5 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span>Powered by fuzzy ranking engine</span>
            <span className="font-semibold text-slate-600">
              Tap item to navigate directly
            </span>
          </div>
        </div>
      )}
    </div>
  );
};
