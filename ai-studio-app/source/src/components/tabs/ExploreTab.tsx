import React, { useState, useEffect } from 'react';
import { 
  Search, 
  MapPin, 
  Filter, 
  GraduationCap, 
  Bookmark, 
  BookmarkCheck, 
  Award, 
  DollarSign, 
  ChevronRight, 
  Sparkles,
  Building2,
  Clock,
  Compass,
  ArrowLeftRight
} from 'lucide-react';
import { STUDY_DESTINATIONS, UNIVERSITY_PARTNERS } from '../../data/liveCatalog';
import { StudyDestination, UniversityPartner } from '../../types';
import { UniversityComparisonData, GLOBAL_UNIVERSITIES } from '../../data/liveCatalog';

interface ExploreTabProps {
  onSelectDestination: (dest: StudyDestination) => void;
  onOpenBooking: (countryOrUni?: string) => void;
  onCompareUniversities?: (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
}

export const ExploreTab: React.FC<ExploreTabProps> = ({
  onSelectDestination,
  onOpenBooking,
  onCompareUniversities,
}) => {
  const [viewMode, setViewMode] = useState<'destinations' | 'universities'>('destinations');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCountryFilter, setSelectedCountryFilter] = useState<string>('All');
  const [shortlistedUniIds, setShortlistedUniIds] = useState<string[]>(() => {
    try {
      const saved = localStorage.getItem('geic_shortlisted_unis');
      return saved ? JSON.parse(saved) : ['anu', 'york'];
    } catch {
      return ['anu', 'york'];
    }
  });

  const toggleShortlist = (id: string) => {
    setShortlistedUniIds((prev) => {
      const updated = prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id];
      try {
        localStorage.setItem('geic_shortlisted_unis', JSON.stringify(updated));
      } catch {}
      return updated;
    });
  };

  const countriesList = ['All', 'Australia', 'United Kingdom', 'Germany', 'Canada', 'United States', 'New Zealand', 'Dubai & UAE'];

  const filteredDestinations = STUDY_DESTINATIONS.filter((d) => {
    const matchesSearch =
      d.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      d.tagline.toLowerCase().includes(searchQuery.toLowerCase()) ||
      d.popularCourses.some((c) => c.toLowerCase().includes(searchQuery.toLowerCase()));

    const matchesCountry = selectedCountryFilter === 'All' || d.name.toLowerCase().includes(selectedCountryFilter.toLowerCase());
    return matchesSearch && matchesCountry;
  });

  const filteredUniversities = UNIVERSITY_PARTNERS.filter((u) => {
    const matchesSearch =
      u.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.country.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.popularDegrees.some((deg) => deg.toLowerCase().includes(searchQuery.toLowerCase()));

    const matchesCountry = selectedCountryFilter === 'All' || u.country.toLowerCase().includes(selectedCountryFilter.toLowerCase());
    return matchesSearch && matchesCountry;
  });

  return (
    <div className="pb-24 text-slate-800 space-y-4">
      {/* Top Header */}
      <div className="pt-2 px-4 space-y-3">
        <div>
          <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider">
            Official Directory
          </span>
          <h2 className="text-xl font-black text-slate-900 leading-tight">
            Explore Countries & Universities
          </h2>
          <p className="text-xs text-slate-500 mt-0.5">
            Discover {STUDY_DESTINATIONS.length} destinations and {UNIVERSITY_PARTNERS.length} listed universities
          </p>
        </div>

        {/* View Switcher Tabs */}
        <div className="bg-slate-100 p-1 rounded-2xl flex border border-slate-200">
          <button
            onClick={() => setViewMode('destinations')}
            className={`flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 cursor-pointer ${
              viewMode === 'destinations'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            <Compass className="w-3.5 h-3.5 text-red-600" />
            <span>Study Destinations ({STUDY_DESTINATIONS.length})</span>
          </button>

          <button
            onClick={() => setViewMode('universities')}
            className={`flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 cursor-pointer ${
              viewMode === 'universities'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            <GraduationCap className="w-3.5 h-3.5 text-red-600" />
            <span>Universities ({UNIVERSITY_PARTNERS.length})</span>
          </button>
        </div>

        {/* Search Bar */}
        <div className="relative">
          <Search className="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder={
              viewMode === 'destinations'
                ? 'Search country, courses, intakes...'
                : 'Search university, degree, location...'
            }
            className="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium bg-white shadow-xs focus:outline-none focus:ring-2 focus:ring-red-500 transition"
          />
        </div>

        {/* Country Filter Chips */}
        <div className="flex overflow-x-auto gap-1.5 pb-1 scrollbar-none">
          {countriesList.map((country) => (
            <button
              key={country}
              onClick={() => setSelectedCountryFilter(country)}
              className={`px-3 py-1.5 rounded-xl text-[11px] font-semibold whitespace-nowrap transition cursor-pointer border ${
                selectedCountryFilter === country
                  ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                  : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
              }`}
            >
              {country}
            </button>
          ))}
        </div>
      </div>

      {/* Content Feed */}
      <div className="px-4">
        {viewMode === 'destinations' ? (
          <div className="space-y-3">
            {filteredDestinations.map((dest) => (
              <div
                key={dest.id}
                onClick={() => onSelectDestination(dest)}
                className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition cursor-pointer group"
              >
                {/* Hero Header */}
                <div className="relative h-32 w-full bg-slate-900 overflow-hidden">
                  <img
                    src={dest.imageUrl}
                    alt={dest.name}
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-300 opacity-90"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
                  
                  <div className="absolute top-2.5 left-3 flex items-center space-x-1.5 bg-slate-900/80 backdrop-blur-xs px-2 py-0.5 rounded-full border border-slate-700/50 text-white text-xs font-bold">
                    <span>{dest.flagEmoji}</span>
                    <span>{dest.code}</span>
                  </div>

                  <div className="absolute bottom-2.5 left-3 right-3">
                    <h3 className="text-white font-black text-base flex items-center gap-1.5">
                      <span>{dest.name}</span>
                    </h3>
                    <p className="text-xs text-slate-200 line-clamp-1 font-medium">{dest.tagline}</p>
                  </div>
                </div>

                {/* Body Details */}
                <div className="p-3.5 space-y-2.5 text-xs">
                  <div className="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-100">
                    <div>
                      <span className="text-[10px] text-slate-500 font-medium block">Avg Tuition / Yr</span>
                      <span className="font-bold text-slate-900 text-[11px] block truncate">{dest.avgTuitionYear}</span>
                    </div>
                    <div>
                      <span className="text-[10px] text-slate-500 font-medium block">Post-Study Work</span>
                      <span className="font-bold text-slate-900 text-[11px] block truncate">{dest.postStudyWorkVisa.split('(')[0]}</span>
                    </div>
                  </div>

                  {/* Highlights Bullet */}
                  <div className="space-y-1">
                    {dest.highlights.slice(0, 2).map((h, i) => (
                      <p key={i} className="text-[11px] text-slate-600 flex items-start gap-1.5 leading-snug">
                        <span className="text-red-500 font-bold">•</span>
                        <span>{h}</span>
                      </p>
                    ))}
                  </div>

                  {/* Action CTA */}
                  <div className="pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span className="text-[11px] font-bold text-slate-500">
                      {dest.topUniversities.length}+ Represented Unis
                    </span>
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        onSelectDestination(dest);
                      }}
                      className="text-xs font-bold text-red-600 flex items-center gap-0.5 hover:underline"
                    >
                      <span>Explore Destination</span>
                      <ChevronRight className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            ))}

            {filteredDestinations.length === 0 && (
              <div className="p-8 text-center text-slate-500 space-y-2">
                <Compass className="w-8 h-8 mx-auto text-slate-300" />
                <p className="text-xs font-semibold">No destinations matched your search.</p>
              </div>
            )}
          </div>
        ) : (
          <div className="space-y-3">
            {/* Compare Universities Decision Banner */}
            <div className="p-4 rounded-3xl bg-stone-900 text-white border border-stone-800 shadow-xs flex items-center justify-between gap-3">
              <div className="space-y-0.5">
                <div className="flex items-center space-x-2 text-[10px] text-stone-400">
                  <span className="font-bold text-rose-400 bg-rose-950/80 border border-rose-800/60 px-2 py-0.5 rounded-full uppercase tracking-wider">
                    Interactive Tool
                  </span>
                  <span>·</span>
                  <span>Side-by-Side</span>
                </div>
                <h4 className="text-xs font-bold text-white">Compare Any 2 Universities</h4>
                <p className="text-[11px] text-stone-300 font-normal">
                  View fees in INR, acceptance rates, IELTS cut-offs & post-study visa rights.
                </p>
              </div>

              <button
                type="button"
                onClick={() => onCompareUniversities?.(GLOBAL_UNIVERSITIES[0], GLOBAL_UNIVERSITIES[1])}
                className="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 shrink-0 cursor-pointer"
              >
                <ArrowLeftRight className="w-3.5 h-3.5" />
                <span>Compare</span>
              </button>
            </div>

            {filteredUniversities.map((uni) => {
              const isShortlisted = shortlistedUniIds.includes(uni.id);

              return (
                <div
                  key={uni.id}
                  className="p-3.5 rounded-3xl bg-white border border-slate-200 shadow-xs hover:border-red-300 transition space-y-3"
                >
                  <div className="flex items-start justify-between gap-2">
                    <div className="flex items-start space-x-2.5">
                      <div className="w-11 h-11 rounded-2xl border border-slate-100 p-1 bg-white shadow-xs shrink-0 flex items-center justify-center overflow-hidden">
                        <img
                          src={uni.logoUrl}
                          alt={uni.name}
                          className="w-full h-full object-contain"
                          onError={(e) => {
                            (e.target as HTMLElement).style.display = 'none';
                          }}
                        />
                      </div>
                      <div>
                        <div className="flex items-center space-x-1.5">
                          <span className="text-[10px] bg-red-50 text-red-700 font-bold px-1.5 py-0.2 rounded-md">
                            {uni.country}
                          </span>
                          {uni.globalRank && (
                            <span className="text-[10px] bg-amber-50 text-amber-800 font-bold px-1.5 py-0.2 rounded-md border border-amber-200">
                              {uni.globalRank}
                            </span>
                          )}
                        </div>
                        <h4 className="font-bold text-xs text-slate-900 mt-0.5 leading-tight">{uni.name}</h4>
                        <p className="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                          <MapPin className="w-3 h-3 text-slate-400" />
                          <span>{uni.location}</span>
                        </p>
                      </div>
                    </div>

                    <button
                      onClick={() => toggleShortlist(uni.id)}
                      className={`p-2 rounded-xl transition cursor-pointer ${
                        isShortlisted
                          ? 'bg-red-50 text-red-600'
                          : 'bg-slate-50 text-slate-400 hover:text-slate-700'
                      }`}
                      title={isShortlisted ? 'Remove from shortlist' : 'Shortlist university'}
                    >
                      {isShortlisted ? (
                        <BookmarkCheck className="w-4 h-4 fill-red-600" />
                      ) : (
                        <Bookmark className="w-4 h-4" />
                      )}
                    </button>
                  </div>

                  {/* University Quick Specs */}
                  <div className="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-100 text-[11px]">
                    <div>
                      <span className="text-[9px] text-slate-500 font-medium block">Estimated Tuition</span>
                      <span className="font-bold text-slate-800 block truncate">{uni.tuitionRange}</span>
                    </div>
                    <div>
                      <span className="text-[9px] text-slate-500 font-medium block">Minimum IELTS</span>
                      <span className="font-bold text-slate-800 block truncate">{uni.ieltsMin}</span>
                    </div>
                  </div>

                  {/* Popular Degrees */}
                  <div>
                    <span className="text-[10px] font-bold text-slate-500 block mb-1">High-Enrollment Programs:</span>
                    <div className="flex flex-wrap gap-1">
                      {uni.popularDegrees.map((deg, i) => (
                        <span key={i} className="text-[10px] bg-slate-100 text-slate-700 font-medium px-2 py-0.5 rounded-md">
                          {deg}
                        </span>
                      ))}
                    </div>
                  </div>

                  {/* Scholarship Pill */}
                  <div className="p-2 rounded-xl bg-purple-50/70 border border-purple-100 text-[10px] text-purple-900 flex items-center gap-1.5">
                    <Award className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="font-semibold truncate">{uni.scholarshipsAvailable}</span>
                  </div>

                  {/* Apply / Book Session CTA */}
                  <div className="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span className="text-[10px] text-slate-500 truncate">
                      Intakes: <strong className="text-slate-800">{uni.intakes.join(', ')}</strong>
                    </span>
                    <div className="flex items-center space-x-1.5 shrink-0">
                      <button
                        type="button"
                        onClick={() => {
                          const matched = GLOBAL_UNIVERSITIES.find((u) => u.id === uni.id || u.name.toLowerCase().includes(uni.name.toLowerCase())) || GLOBAL_UNIVERSITIES[0];
                          onCompareUniversities?.(matched);
                        }}
                        className="bg-stone-100 hover:bg-rose-50 text-stone-700 hover:text-rose-700 font-bold py-1.5 px-2.5 rounded-xl text-[11px] border border-stone-200 transition cursor-pointer flex items-center gap-1"
                        title="Compare fees, acceptance & requirements"
                      >
                        <ArrowLeftRight className="w-3 h-3" />
                        <span>Compare</span>
                      </button>

                      <button
                        onClick={() => onOpenBooking(uni.name)}
                        className="bg-slate-900 hover:bg-slate-800 text-white font-bold py-1.5 px-3 rounded-xl text-[11px] transition shadow-xs cursor-pointer"
                      >
                        Apply
                      </button>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
};
