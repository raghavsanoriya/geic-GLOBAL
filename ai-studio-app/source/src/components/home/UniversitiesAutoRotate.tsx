import React, { useState, useEffect, useRef } from 'react';
import { 
  GraduationCap, 
  Pause, 
  Play, 
  ExternalLink, 
  Award, 
  ArrowRight,
  Sparkles
} from 'lucide-react';
import { UNIVERSITY_PARTNERS } from '../../data/liveCatalog';
import { AppTab, UniversityPartner } from '../../types';

interface UniversitiesAutoRotateProps {
  onNavigateTab: (tab: AppTab) => void;
  onOpenBooking: (universityName?: string) => void;
}

export const UniversitiesAutoRotate: React.FC<UniversitiesAutoRotateProps> = ({
  onNavigateTab,
  onOpenBooking,
}) => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isAutoPlay, setIsAutoPlay] = useState(true);
  const [isHovered, setIsHovered] = useState(false);
  const touchStartX = useRef<number | null>(null);
  const touchEndX = useRef<number | null>(null);

  const total = UNIVERSITY_PARTNERS.length;

  // Auto-rotate every 3.5 seconds
  useEffect(() => {
    if (!isAutoPlay || isHovered || total < 2) return;

    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % total);
    }, 3500);

    return () => clearInterval(interval);
  }, [isAutoPlay, isHovered, total]);

  const handleNext = () => {
    setCurrentIndex((prev) => (prev + 1) % total);
  };

  const handlePrev = () => {
    setCurrentIndex((prev) => (prev - 1 + total) % total);
  };

  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.targetTouches[0].clientX;
  };

  const handleTouchMove = (e: React.TouchEvent) => {
    touchEndX.current = e.targetTouches[0].clientX;
  };

  const handleTouchEnd = () => {
    if (!touchStartX.current || !touchEndX.current) return;
    const diff = touchStartX.current - touchEndX.current;
    if (diff > 40) {
      handleNext();
    } else if (diff < -40) {
      handlePrev();
    }
    touchStartX.current = null;
    touchEndX.current = null;
  };

  const activeUni = UNIVERSITY_PARTNERS[currentIndex];
  const nextUni = UNIVERSITY_PARTNERS[(currentIndex + 1) % total];

  if (!activeUni) return <p className="p-4 text-sm text-slate-600">No universities published yet.</p>;

  return (
    <div 
      className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4 select-none"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
    >
      {/* App Section Header with Auto-Rotate Status Indicator */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-xs">
            <GraduationCap className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                Top Partner Universities
              </h3>
              <span className="flex items-center gap-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                Auto-Rotating
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              {UNIVERSITY_PARTNERS.length} institutions listed in the GEIC catalogue
            </p>
          </div>
        </div>

        {/* Play/Pause & Arrow Controls */}
        <div className="flex items-center space-x-1.5 shrink-0">
          <button
            onClick={() => setIsAutoPlay(!isAutoPlay)}
            className="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition cursor-pointer border border-slate-200 shadow-xs"
            title={isAutoPlay ? 'Pause Auto-Rotation' : 'Resume Auto-Rotation'}
            aria-label={isAutoPlay ? 'Pause Auto-Rotation' : 'Resume Auto-Rotation'}
          >
            {isAutoPlay ? <Pause className="w-3.5 h-3.5" /> : <Play className="w-3.5 h-3.5 text-emerald-600" />}
          </button>
        </div>
      </div>

      {/* Featured Auto-Rotating University Card Spotlight */}
      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 text-white p-4 sm:p-5 shadow-md border border-slate-700/60">
        <div className="absolute top-0 right-0 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none" />

        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 relative z-10">
          <div className="flex items-center space-x-3.5">
            {/* University Logo Avatar */}
            <div className="w-14 h-14 rounded-2xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow-md border border-white/20">
              <img
                src={activeUni.logoUrl}
                alt={activeUni.name}
                className="w-full h-full object-contain"
                onError={(e) => {
                  // Fallback icon if logo image fails
                  (e.target as HTMLElement).style.display = 'none';
                }}
              />
            </div>

            <div>
              <div className="flex items-center space-x-2">
                <span className="text-[10px] font-bold bg-white/10 text-emerald-300 px-2 py-0.5 rounded-md border border-emerald-400/30">
                  {activeUni.country}
                </span>
                {activeUni.globalRank && (
                  <span className="text-[10px] font-bold bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded-md border border-amber-400/30 flex items-center gap-1">
                    <Award className="w-3 h-3" />
                    {activeUni.globalRank}
                  </span>
                )}
              </div>
              <h4 className="font-black text-base text-white mt-1 leading-snug">
                {activeUni.name}
              </h4>
              <p className="text-[11px] text-slate-300">
                {activeUni.location}
              </p>
            </div>
          </div>

          <div className="shrink-0 w-full sm:w-auto flex sm:flex-col items-center sm:items-end justify-between gap-1 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/10">
            <span className="text-[10px] uppercase font-bold text-slate-400">Est. Tuition</span>
            <span className="font-extrabold text-xs text-amber-300 bg-amber-400/10 px-2.5 py-1 rounded-xl border border-amber-400/20">
              {activeUni.tuitionRange.split(' - ')[0]}
            </span>
          </div>
        </div>

        {/* Popular Degrees & Scholarship Badge */}
        <div className="mt-3.5 pt-3 border-t border-white/10 space-y-2 text-xs">
          <div className="flex flex-wrap items-center gap-1.5">
            <span className="text-[10px] font-semibold text-slate-400">Top Degrees:</span>
            {activeUni.popularDegrees.slice(0, 2).map((deg, i) => (
              <span
                key={i}
                className="text-[10px] bg-white/10 text-slate-200 px-2 py-0.5 rounded-md font-medium"
              >
                {deg}
              </span>
            ))}
          </div>

          {activeUni.scholarshipsAvailable && (
            <div className="text-[11px] text-emerald-300 flex items-center gap-1.5 font-medium">
              <Sparkles className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
              <span className="line-clamp-1">{activeUni.scholarshipsAvailable}</span>
            </div>
          )}
        </div>

        {/* Direct Action Bar */}
        <div className="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between">
          <button
            onClick={() => onOpenBooking(activeUni.name)}
            className="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 cursor-pointer transition"
          >
            <span>Apply to {activeUni.name.split(' ')[0]}</span>
            <ArrowRight className="w-3 h-3" />
          </button>

          <span className="text-[10px] text-slate-400 font-medium">
            {currentIndex + 1} of {total} Institutions
          </span>
        </div>
      </div>

      {/* Mini Auto-Scroll University Ticker Preview */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
        {UNIVERSITY_PARTNERS.slice(0, 4).map((uni, idx) => (
          <div
            key={uni.id}
            onClick={() => {
              setCurrentIndex(idx);
              setIsAutoPlay(false);
            }}
            className={`p-2.5 rounded-xl border transition-all cursor-pointer text-left flex items-center space-x-2.5 ${
              currentIndex === idx
                ? 'bg-emerald-50/80 border-emerald-400 shadow-xs'
                : 'bg-slate-50 border-slate-200 hover:border-slate-300'
            }`}
          >
            <div className="w-8 h-8 rounded-lg bg-white p-1 flex items-center justify-center shrink-0 border border-slate-200">
              <img
                src={uni.logoUrl}
                alt={uni.name}
                className="w-full h-full object-contain"
                onError={(e) => {
                  (e.target as HTMLElement).style.display = 'none';
                }}
              />
            </div>
            <div className="min-w-0">
              <h5 className="text-[11px] font-bold text-slate-900 truncate leading-tight">
                {uni.name}
              </h5>
              <span className="text-[9px] text-slate-500 block truncate">
                {uni.country}
              </span>
            </div>
          </div>
        ))}
      </div>

      {/* Progress Dots Indicator */}
      <div className="flex items-center gap-1.5 pt-1 overflow-x-auto max-w-full">
        {UNIVERSITY_PARTNERS.map((_, i) => (
          <button
            key={i}
            onClick={() => {
              setCurrentIndex(i);
              setIsAutoPlay(false);
            }}
            className={`shrink-0 h-1.5 rounded-full transition-all duration-300 cursor-pointer ${
              currentIndex === i
                ? 'w-6 bg-emerald-600'
                : 'w-1.5 bg-slate-300 hover:bg-slate-400'
            }`}
            aria-label={`Jump to university ${i + 1}`}
          />
        ))}
      </div>

      {/* Section Footer */}
      <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
        <span className="text-[11px] text-slate-500 font-medium">
          Browse universities by destination
        </span>
        <button
          onClick={() => onNavigateTab('explore')}
          className="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 cursor-pointer hover:underline"
        >
          <span>Explore all universities</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </button>
      </div>
    </div>
  );
};
