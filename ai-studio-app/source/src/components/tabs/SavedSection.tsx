import React, { useState } from 'react';
import { 
  Heart, 
  Briefcase, 
  Calendar, 
  BookOpen, 
  Trash2, 
  ArrowRight, 
  Sparkles, 
  MapPin, 
  Clock, 
  CheckCircle2, 
  ChevronRight,
  Search,
  ExternalLink,
  ShieldCheck,
  Ticket
} from 'lucide-react';
import { useBookmarks } from '../../context/BookmarksContext';
import { GEIC_SERVICES, UPCOMING_EVENTS_LIST, ENGLISH_TESTS } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';

interface SavedSectionProps {
  onSelectService: (serviceId: string) => void;
  onSelectTest: (testId: string) => void;
  onSelectEvent: (eventId: string) => void;
  onOpenBooking: (title?: string) => void;
  onOpenExpo: () => void;
  onSwitchSection: (section: 'services' | 'events' | 'tests') => void;
}

type SavedFilterTab = 'all' | 'services' | 'events' | 'tests';

export const SavedSection: React.FC<SavedSectionProps> = ({
  onSelectService,
  onSelectTest,
  onSelectEvent,
  onOpenBooking,
  onOpenExpo,
  onSwitchSection,
}) => {
  const { savedServices, savedTests, savedEvents, clearAll, totalSavedCount } = useBookmarks();
  const [activeTab, setActiveTab] = useState<SavedFilterTab>('all');
  const [searchQuery, setSearchQuery] = useState('');

  // Resolve saved data objects
  const filteredServices = GEIC_SERVICES.filter((srv) => savedServices.includes(srv.id));
  const filteredEvents = UPCOMING_EVENTS_LIST.filter((ev) => savedEvents.includes(ev.id));
  const filteredTests = ENGLISH_TESTS.filter((t) => savedTests.includes(t.id));

  // Search filter
  const query = searchQuery.toLowerCase().trim();
  const displayedServices = filteredServices.filter(
    (s) => !query || s.title.toLowerCase().includes(query) || s.category.toLowerCase().includes(query)
  );
  const displayedEvents = filteredEvents.filter(
    (e) => !query || e.title.toLowerCase().includes(query) || e.city.toLowerCase().includes(query)
  );
  const displayedTests = filteredTests.filter(
    (t) => !query || t.name.toLowerCase().includes(query) || t.code.toLowerCase().includes(query)
  );

  const totalDisplayed = 
    (activeTab === 'all' || activeTab === 'services' ? displayedServices.length : 0) +
    (activeTab === 'all' || activeTab === 'events' ? displayedEvents.length : 0) +
    (activeTab === 'all' || activeTab === 'tests' ? displayedTests.length : 0);

  return (
    <div className="space-y-4 animate-in fade-in duration-200">
      {/* Top Banner Header */}
      <div className="bg-gradient-to-br from-rose-950 via-slate-900 to-slate-950 text-white rounded-3xl p-4 sm:p-5 shadow-lg border border-rose-900/40 relative overflow-hidden">
        <div className="absolute top-0 right-0 w-40 h-40 bg-rose-500/10 rounded-full blur-2xl pointer-events-none" />

        <div className="flex items-start justify-between relative z-10">
          <div className="space-y-1">
            <span className="text-[10px] font-black uppercase tracking-widest text-rose-300 bg-rose-400/20 px-2.5 py-0.5 rounded-full border border-rose-400/30 inline-flex items-center gap-1">
              <Heart className="w-2.5 h-2.5 fill-rose-300" />
              <span>Personal Shortlist</span>
            </span>
            <h2 className="text-xl font-black text-white mt-1">My Saved Bookmarks</h2>
            <p className="text-xs text-slate-300 max-w-sm">
              Quickly revisit bookmarked services, summits, and test coaching programs saved from anywhere in the app.
            </p>
          </div>

          <div className="text-right shrink-0">
            <span className="text-2xl font-black text-rose-400 block leading-none">
              {totalSavedCount}
            </span>
            <span className="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
              Items Saved
            </span>
          </div>
        </div>

        {/* Clear All Action */}
        {totalSavedCount > 0 && (
          <div className="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span className="text-[11px] text-slate-400">
              Synced with your device's local storage
            </span>
            <button
              onClick={() => {
                if (window.confirm('Are you sure you want to clear all saved bookmarks?')) {
                  clearAll();
                }
              }}
              className="text-[11px] font-bold text-rose-300 hover:text-rose-200 flex items-center gap-1 cursor-pointer transition"
            >
              <Trash2 className="w-3.5 h-3.5" />
              <span>Clear All</span>
            </button>
          </div>
        )}
      </div>

      {/* Filter Tabs & Search Bar */}
      {totalSavedCount > 0 && (
        <div className="space-y-2.5">
          {/* Filter Pills */}
          <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-0.5">
            <button
              onClick={() => setActiveTab('all')}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0 border ${
                activeTab === 'all'
                  ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
                  : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
              }`}
            >
              All Items ({totalSavedCount})
            </button>

            <button
              onClick={() => setActiveTab('services')}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
                activeTab === 'services'
                  ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
                  : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
              }`}
            >
              <Briefcase className="w-3 h-3" />
              <span>Services ({savedServices.length})</span>
            </button>

            <button
              onClick={() => setActiveTab('events')}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
                activeTab === 'events'
                  ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
                  : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
              }`}
            >
              <Calendar className="w-3 h-3" />
              <span>Events ({savedEvents.length})</span>
            </button>

            <button
              onClick={() => setActiveTab('tests')}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer shrink-0 border ${
                activeTab === 'tests'
                  ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
                  : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
              }`}
            >
              <BookOpen className="w-3 h-3" />
              <span>Tests ({savedTests.length})</span>
            </button>
          </div>

          {/* Quick Search */}
          {totalSavedCount > 3 && (
            <div className="relative">
              <Search className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search within your saved items..."
                className="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"
              />
            </div>
          )}
        </div>
      )}

      {/* EMPTY STATE */}
      {totalSavedCount === 0 ? (
        <div className="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xs text-center space-y-4">
          <div className="w-16 h-16 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center shadow-inner">
            <Heart className="w-8 h-8" />
          </div>

          <div className="space-y-1 max-w-sm mx-auto">
            <h3 className="font-black text-base text-slate-900">Your Saved List is Empty</h3>
            <p className="text-xs text-slate-500 leading-relaxed">
              Tap the <span className="font-bold text-rose-600">Heart icon (♥)</span> on any service, summit pass, or test coaching card to bookmark items here for easy reference!
            </p>
          </div>

          <div className="pt-2 flex flex-wrap items-center justify-center gap-2">
            <button
              onClick={() => onSwitchSection('services')}
              className="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer shadow-xs"
            >
              <Briefcase className="w-3.5 h-3.5" />
              <span>Browse Services</span>
            </button>

            <button
              onClick={() => onSwitchSection('events')}
              className="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer shadow-xs"
            >
              <Calendar className="w-3.5 h-3.5" />
              <span>View Events</span>
            </button>

            <button
              onClick={() => onSwitchSection('tests')}
              className="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer shadow-xs"
            >
              <BookOpen className="w-3.5 h-3.5" />
              <span>Explore Tests</span>
            </button>
          </div>
        </div>
      ) : totalDisplayed === 0 ? (
        <div className="bg-white rounded-3xl p-6 border border-slate-200 text-center space-y-2">
          <p className="text-xs font-bold text-slate-700">No saved items found matching "{searchQuery}"</p>
          <button
            onClick={() => {
              setSearchQuery('');
              setActiveTab('all');
            }}
            className="text-xs text-rose-600 font-bold hover:underline cursor-pointer"
          >
            Clear Search & Reset Filter
          </button>
        </div>
      ) : (
        <div className="space-y-4">
          {/* SAVED SERVICES LIST */}
          {(activeTab === 'all' || activeTab === 'services') && displayedServices.length > 0 && (
            <div className="space-y-2.5">
              <div className="flex items-center justify-between">
                <h3 className="font-black text-xs text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                  <Briefcase className="w-3.5 h-3.5 text-red-600" />
                  <span>Saved Services ({displayedServices.length})</span>
                </h3>
              </div>

              <div className="space-y-2.5">
                {displayedServices.map((service) => (
                  <div
                    key={service.id}
                    onClick={() => onSelectService(service.id)}
                    className="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs hover:shadow-md hover:border-red-300 transition cursor-pointer flex items-center space-x-3 group relative"
                  >
                    {/* Thumbnail Image */}
                    <div className="relative w-20 h-20 rounded-xl overflow-hidden bg-slate-900 shrink-0">
                      <img
                        src={service.bannerImage}
                        alt={service.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                      />
                      <div className="absolute top-1 left-1 bg-red-600 text-white text-[8px] font-black px-1.5 py-0.2 rounded">
                        Step {service.stepNumber}
                      </div>
                    </div>

                    {/* Details */}
                    <div className="flex-1 min-w-0 pr-8">
                      <div className="flex items-center space-x-1.5">
                        <span className="text-[9px] font-bold bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded">
                          {service.category}
                        </span>
                      </div>
                      <h4 className="font-extrabold text-xs text-slate-900 truncate mt-0.5 group-hover:text-red-600 transition">
                        {service.title}
                      </h4>
                      <p className="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                        {service.shortDesc}
                      </p>
                      <div className="mt-1.5 flex items-center space-x-2">
                        <span className="text-[10px] font-bold text-red-600 flex items-center gap-0.5">
                          <span>View Details</span>
                          <ChevronRight className="w-3 h-3" />
                        </span>
                      </div>
                    </div>

                    {/* Bookmark Heart Action */}
                    <div className="absolute top-2.5 right-2.5">
                      <BookmarkButton
                        type="service"
                        id={service.id}
                        title={service.title}
                        size="sm"
                        variant="white"
                      />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* SAVED EVENTS LIST */}
          {(activeTab === 'all' || activeTab === 'events') && displayedEvents.length > 0 && (
            <div className="space-y-2.5">
              <div className="flex items-center justify-between">
                <h3 className="font-black text-xs text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                  <Calendar className="w-3.5 h-3.5 text-amber-600" />
                  <span>Saved Summits & Events ({displayedEvents.length})</span>
                </h3>
              </div>

              <div className="space-y-2.5">
                {displayedEvents.map((event) => (
                  <div
                    key={event.id}
                    onClick={() => {
                      if (event.id === 'expo-2026') {
                        onOpenExpo();
                      } else {
                        onSelectEvent(event.id);
                      }
                    }}
                    className="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs hover:shadow-md hover:border-amber-300 transition cursor-pointer flex items-center space-x-3 group relative"
                  >
                    {/* Thumbnail Image */}
                    <div className="relative w-20 h-20 rounded-xl overflow-hidden bg-slate-900 shrink-0">
                      <img
                        src={event.bannerUrl}
                        alt={event.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                      />
                      <div className="absolute top-1 left-1 bg-amber-600 text-white text-[8px] font-black px-1.5 py-0.2 rounded">
                        {event.badge}
                      </div>
                    </div>

                    {/* Details */}
                    <div className="flex-1 min-w-0 pr-8">
                      <div className="flex items-center space-x-1 text-[10px] text-red-600 font-bold">
                        <Calendar className="w-3 h-3" />
                        <span className="truncate">{event.date}</span>
                      </div>
                      <h4 className="font-extrabold text-xs text-slate-900 truncate mt-0.5 group-hover:text-amber-700 transition">
                        {event.title}
                      </h4>
                      <p className="text-[10px] text-slate-500 line-clamp-1 mt-0.5 flex items-center gap-1">
                        <MapPin className="w-3 h-3 text-slate-400" />
                        <span>{event.city} • {event.venue.split(',')[0]}</span>
                      </p>
                      <div className="mt-1.5 flex items-center space-x-2">
                        <span className="text-[10px] font-bold text-amber-700 flex items-center gap-0.5">
                          <span>VIP Pass & Agenda</span>
                          <ChevronRight className="w-3 h-3" />
                        </span>
                      </div>
                    </div>

                    {/* Bookmark Heart Action */}
                    <div className="absolute top-2.5 right-2.5">
                      <BookmarkButton
                        type="event"
                        id={event.id}
                        title={event.title}
                        size="sm"
                        variant="white"
                      />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* SAVED TESTS LIST */}
          {(activeTab === 'all' || activeTab === 'tests') && displayedTests.length > 0 && (
            <div className="space-y-2.5">
              <div className="flex items-center justify-between">
                <h3 className="font-black text-xs text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                  <BookOpen className="w-3.5 h-3.5 text-blue-600" />
                  <span>Saved Test Coaching ({displayedTests.length})</span>
                </h3>
              </div>

              <div className="space-y-2.5">
                {displayedTests.map((test) => (
                  <div
                    key={test.id}
                    onClick={() => onSelectTest(test.id)}
                    className="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs hover:shadow-md hover:border-blue-300 transition cursor-pointer flex items-center space-x-3 group relative"
                  >
                    {/* Thumbnail Image */}
                    <div className="relative w-20 h-20 rounded-xl overflow-hidden bg-slate-900 shrink-0">
                      <img
                        src={test.bannerImage}
                        alt={test.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                      />
                      <div className="absolute top-1 left-1 bg-blue-600 text-white text-[8px] font-black px-1.5 py-0.2 rounded">
                        {test.code}
                      </div>
                    </div>

                    {/* Details */}
                    <div className="flex-1 min-w-0 pr-8">
                      <div className="flex items-center space-x-1.5">
                        <span className="text-[9px] font-bold bg-blue-50 text-blue-700 px-1.5 py-0.2 rounded border border-blue-200">
                          Target: {test.minTargetBand}
                        </span>
                      </div>
                      <h4 className="font-extrabold text-xs text-slate-900 truncate mt-0.5 group-hover:text-blue-600 transition">
                        {test.name}
                      </h4>
                      <p className="text-[10px] text-slate-500 line-clamp-1 mt-0.5">
                        {test.tagline}
                      </p>
                      <div className="mt-1.5 flex items-center space-x-2">
                        <span className="text-[10px] font-bold text-blue-600 flex items-center gap-0.5">
                          <span>Modules & Classes</span>
                          <ChevronRight className="w-3 h-3" />
                        </span>
                      </div>
                    </div>

                    {/* Bookmark Heart Action */}
                    <div className="absolute top-2.5 right-2.5">
                      <BookmarkButton
                        type="test"
                        id={test.id}
                        title={test.name}
                        size="sm"
                        variant="white"
                      />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      )}

      {/* Book Indore Consultation Helper */}
      <div className="p-4 rounded-3xl bg-slate-900 text-white space-y-2 shadow-md">
        <div className="flex items-center space-x-2">
          <Sparkles className="w-4 h-4 text-rose-400" />
          <h4 className="font-extrabold text-xs">Discuss Your Saved Items with a Mentor</h4>
        </div>
        <p className="text-[11px] text-slate-300 leading-relaxed">
          Bring your shortlisted services and exams to our Indore office for a personalized profile assessment and admission timeline.
        </p>
        <button
          onClick={() => onOpenBooking('Saved Items Consultation')}
          className="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer shadow-xs"
        >
          <Sparkles className="w-3.5 h-3.5" />
          <span>Book Free Counselling on Shortlist</span>
        </button>
      </div>
    </div>
  );
};
