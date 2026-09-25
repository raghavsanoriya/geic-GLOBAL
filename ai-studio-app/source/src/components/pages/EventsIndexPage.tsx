import React, { useState, useMemo } from 'react';
import { 
  ArrowLeft, 
  Calendar, 
  Clock, 
  MapPin, 
  Ticket, 
  ChevronRight, 
  Sparkles, 
  Building, 
  Menu, 
  CheckCircle2,
  Search,
  X,
  RotateCcw
} from 'lucide-react';
import Fuse from 'fuse.js';
import { UPCOMING_EVENTS_LIST, GEIC_BRAND } from '../../data/liveCatalog';
import { UpcomingEventInfo, StudyDestination } from '../../types';
import { UniversityComparisonData } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';
import { GlobalSearchBar } from '../common/GlobalSearchBar';

interface EventsIndexPageProps {
  onBack: () => void;
  onSelectEvent: (eventId: string) => void;
  onOpenExpo: () => void;
  onOpenBooking: () => void;
  onOpenDrawer?: () => void;
  onSelectService?: (serviceId: string) => void;
  onSelectTest?: (testId: string) => void;
  onSelectDestination?: (destination: StudyDestination) => void;
  onCompareUniversities?: (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
}

export const EventsIndexPage: React.FC<EventsIndexPageProps> = ({
  onBack,
  onSelectEvent,
  onOpenExpo,
  onOpenBooking,
  onOpenDrawer,
  onSelectService,
  onSelectTest,
  onSelectDestination,
  onCompareUniversities,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('All');

  const categories = ['All', 'Summit', 'Masterclass', 'Spot Assessment', 'Webinar', 'Conclave'];

  // Fuse.js index for events fuzzy search
  const eventsFuse = useMemo(() => {
    return new Fuse(UPCOMING_EVENTS_LIST, {
      keys: [
        { name: 'title', weight: 0.4 },
        { name: 'subtitle', weight: 0.2 },
        { name: 'city', weight: 0.2 },
        { name: 'venue', weight: 0.1 },
        { name: 'category', weight: 0.1 },
      ],
      threshold: 0.38,
      ignoreLocation: true,
    });
  }, []);

  const filteredEvents = useMemo(() => {
    const matchingIds = searchQuery.trim()
      ? new Set(eventsFuse.search(searchQuery.trim()).map((r) => r.item.id))
      : null;

    return UPCOMING_EVENTS_LIST.filter((e) => {
      if (matchingIds && !matchingIds.has(e.id)) {
        return false;
      }
      if (selectedCategory !== 'All' && e.category !== selectedCategory) {
        return false;
      }
      return true;
    });
  }, [searchQuery, selectedCategory, eventsFuse]);

  return (
    <div className="pb-28 text-slate-800 space-y-4 animate-in fade-in duration-200">
      {/* Top Header */}
      <div className="sticky top-0 z-30 bg-white/95 backdrop-blur-md px-4 py-2.5 border-b border-slate-200/80 shadow-xs flex items-center justify-between">
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

        <span className="text-xs font-black text-slate-900">
          Upcoming Events ({filteredEvents.length})
        </span>

        <button
          onClick={onOpenExpo}
          className="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1 transition shadow-xs cursor-pointer"
        >
          <Ticket className="w-3.5 h-3.5" />
          <span>Expo 2026</span>
        </button>
      </div>

      {/* Hero Header Card */}
      <div className="px-4 space-y-2">
        <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider block">
          Central India’s Premier Summits
        </span>
        <h1 className="text-xl font-black text-slate-900 leading-tight">
          Admissions Summits & Masterclasses
        </h1>
        <p className="text-xs text-slate-500 leading-relaxed">
          Meet foreign university deans face-to-face in Indore, receive on-spot conditional offers, claim 100% application fee waivers, and apply for merit scholarships.
        </p>
      </div>

      {/* Global Fuzzy Search Bar across events, services, tests, destinations */}
      <div className="px-4">
        <GlobalSearchBar
          placeholder="Global search events, services, tests, universities, destinations..."
          onSelectEvent={onSelectEvent}
          onSelectService={onSelectService}
          onSelectTest={onSelectTest}
          onSelectDestination={onSelectDestination}
          onCompareUniversities={onCompareUniversities}
          onOpenExpo={onOpenExpo}
          defaultFilter="event"
          allowFilterSwitching={true}
          showSuggestions={false}
        />
      </div>

      {/* In-Page Quick Filters */}
      <div className="px-4 space-y-2">
        <div className="flex items-center space-x-2">
          <div className="relative flex-1">
            <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search event title, venue, or speaker..."
              className="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-2xs"
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
        </div>

        {/* Category Filter Pills */}
        <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-1">
          {categories.map((cat) => {
            const isSelected = selectedCategory === cat;
            return (
              <button
                key={cat}
                onClick={() => setSelectedCategory(cat)}
                className={`px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0 border ${
                  isSelected
                    ? 'bg-amber-600 text-white border-amber-600 shadow-xs'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                {cat}
              </button>
            );
          })}
        </div>
      </div>

      {/* Events Grid List */}
      <div className="px-4 space-y-3">
        {filteredEvents.length === 0 ? (
          <div className="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3 shadow-xs">
            <div className="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center">
              <Calendar className="w-6 h-6" />
            </div>
            <h3 className="font-extrabold text-sm text-slate-900">
              No matching events found
            </h3>
            <p className="text-xs text-slate-500 max-w-xs mx-auto">
              We couldn't find any events matching "{searchQuery}" in category "{selectedCategory}".
            </p>
            <button
              onClick={() => {
                setSearchQuery('');
                setSelectedCategory('All');
              }}
              className="bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl text-xs inline-flex items-center space-x-1.5 transition cursor-pointer shadow-xs"
            >
              <RotateCcw className="w-3.5 h-3.5" />
              <span>Reset Search & Filters</span>
            </button>
          </div>
        ) : (
          filteredEvents.map((event) => (
          <div
            key={event.id}
            onClick={() => onSelectEvent(event.id)}
            className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-red-300 transition cursor-pointer flex flex-col justify-between group"
          >
            <div className="relative h-36 w-full bg-slate-900 overflow-hidden">
              <img
                src={event.bannerUrl}
                alt={event.title}
                className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

              <div className="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                <span className="text-[9px] font-black uppercase tracking-wider bg-red-600 text-white px-2 py-0.5 rounded-md shadow-xs">
                  {event.badge}
                </span>
                <span className="text-[9px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 px-2 py-0.5 rounded-md">
                  {event.category}
                </span>
              </div>

              <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                <span className="text-[9px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-950/90 border border-emerald-500/40 px-2 py-0.5 rounded-md">
                  EVENT DETAILS
                </span>
                <BookmarkButton
                  type="event"
                  id={event.id}
                  title={event.title}
                  size="xs"
                  variant="glass"
                />
              </div>

              <div className="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                <h3 className="font-extrabold text-sm leading-tight">
                  {event.title}
                </h3>
              </div>
            </div>

            <div className="p-3.5 space-y-2 text-xs">
              <div className="grid grid-cols-2 gap-2 text-[11px] text-slate-600">
                <div className="flex items-center space-x-1.5">
                  <Calendar className="w-3.5 h-3.5 text-red-500 shrink-0" />
                  <span className="truncate font-semibold">{event.date}</span>
                </div>
                <div className="flex items-center space-x-1.5">
                  <Clock className="w-3.5 h-3.5 text-red-500 shrink-0" />
                  <span className="truncate">{event.time}</span>
                </div>
              </div>

              <div className="flex items-start space-x-1.5 text-[11px] text-slate-500">
                <MapPin className="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" />
                <span className="line-clamp-1">{event.venue}, {event.city}</span>
              </div>

              <p className="text-[11px] text-slate-600 leading-relaxed font-normal line-clamp-2">
                {event.description}
              </p>

              <div className="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span className="text-[10px] font-bold text-slate-500">
                  {event.highlights.length} Key Highlights
                </span>

                <span className="text-xs font-bold text-red-600 flex items-center space-x-0.5 group-hover:translate-x-1 transition-transform">
                  <span>View Details & Pass</span>
                  <ChevronRight className="w-3.5 h-3.5" />
                </span>
              </div>
            </div>
          </div>
          ))
        )}
      </div>
    </div>
  );
};
