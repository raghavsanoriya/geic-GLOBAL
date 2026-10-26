import React, { useState, useEffect, useRef } from 'react';
import { ChevronLeft, ChevronRight, Sparkles, Calendar, Award, ArrowRight } from 'lucide-react';
import { HERO_SLIDES } from '../../data/geicData';
import { AppTab, MoreSection } from '../../types';

interface HeroSliderBannerProps {
  onOpenBooking: () => void;
  onNavigateTab: (tab: AppTab) => void;
  onNavigateMoreSection?: (section: MoreSection) => void;
}

export const HeroSliderBanner: React.FC<HeroSliderBannerProps> = ({
  onOpenBooking,
  onNavigateTab,
  onNavigateMoreSection,
}) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const touchStartX = useRef<number | null>(null);
  const touchEndX = useRef<number | null>(null);

  // Auto slide every 5 seconds
  useEffect(() => {
    if (isPaused) return;

    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % HERO_SLIDES.length);
    }, 5000);

    return () => clearInterval(timer);
  }, [isPaused]);

  const handleNext = () => {
    setCurrentSlide((prev) => (prev + 1) % HERO_SLIDES.length);
  };

  const handlePrev = () => {
    setCurrentSlide((prev) => (prev - 1 + HERO_SLIDES.length) % HERO_SLIDES.length);
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
    if (diff > 45) {
      // Swiped left -> next
      handleNext();
    } else if (diff < -45) {
      // Swiped right -> prev
      handlePrev();
    }
    touchStartX.current = null;
    touchEndX.current = null;
  };

  const handleCtaAction = (action: string) => {
    if (action === 'booking') {
      onOpenBooking();
    } else if (action === 'evaluator') {
      onNavigateTab('evaluator');
    } else if (action === 'expo') {
      onNavigateTab('expo');
    } else if (action === 'explore') {
      onNavigateTab('explore');
    } else if (action === 'tests') {
      if (onNavigateMoreSection) {
        onNavigateMoreSection('tests');
      } else {
        onNavigateTab('more');
      }
    } else if (action === 'services') {
      if (onNavigateMoreSection) {
        onNavigateMoreSection('services');
      } else {
        onNavigateTab('more');
      }
    }
  };

  return (
    <div
      className="relative rounded-3xl overflow-hidden shadow-xl border border-slate-800 bg-slate-950 select-none group"
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
    >
      {/* Slides Container */}
      <div
        className="flex transition-transform duration-500 ease-out"
        style={{ transform: `translateX(-${currentSlide * 100}%)` }}
      >
        {HERO_SLIDES.map((slide, idx) => (
          <div key={slide.id} className="w-full shrink-0 relative min-h-[250px] sm:min-h-[260px] flex flex-col justify-end p-4 text-white">
            {/* Background Photographic Image */}
            <img
              src={slide.imageUrl}
              alt={slide.title}
              referrerPolicy="no-referrer"
              className="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000"
            />

            {/* Dark Dramatic Backdrop Gradient for maximum legibility */}
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-900/40" />

            {/* Content Box */}
            <div className="relative z-10 space-y-2.5">
              {/* Badges */}
              <div className="flex items-center justify-between">
                <span className="text-[10px] font-bold text-amber-300 bg-amber-400/20 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-amber-400/30 flex items-center gap-1 shadow-xs">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                  {slide.badge}
                </span>

                <span className="text-[10px] bg-red-600/80 backdrop-blur-md text-white font-extrabold px-2 py-0.5 rounded-full border border-red-500/40 shadow-xs">
                  {slide.statBadge}
                </span>
              </div>

              {/* Headings */}
              <div>
                <h2 className="text-lg font-black text-white leading-tight drop-shadow-sm">
                  {slide.title}{' '}
                  <span className="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-rose-300 to-amber-300">
                    {slide.titleHighlight}
                  </span>
                </h2>
                <p className="text-xs text-slate-200 mt-1 leading-relaxed font-normal line-clamp-2 drop-shadow-xs">
                  {slide.description}
                </p>
              </div>

              {/* Action Buttons */}
              <div className="pt-1 flex items-center space-x-2">
                <button
                  onClick={() => handleCtaAction(slide.primaryAction)}
                  className="flex-1 bg-red-600 hover:bg-red-700 active:scale-95 text-white text-xs font-bold py-2.5 px-3 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
                >
                  <span>{slide.primaryCta}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <button
                  onClick={() => handleCtaAction(slide.secondaryAction)}
                  className="bg-slate-900/90 hover:bg-slate-800 active:scale-95 text-slate-200 text-xs font-semibold py-2.5 px-3 rounded-xl transition flex items-center space-x-1 border border-slate-700/80 backdrop-blur-sm cursor-pointer"
                >
                  <span>{slide.secondaryCta}</span>
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Manual Arrow Controls (Visible on hover or tap) */}
      <button
        onClick={handlePrev}
        className="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-900/60 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-900/90 transition border border-white/10 opacity-70 hover:opacity-100 z-20 cursor-pointer"
        aria-label="Previous slide"
      >
        <ChevronLeft className="w-4 h-4" />
      </button>

      <button
        onClick={handleNext}
        className="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-900/60 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-900/90 transition border border-white/10 opacity-70 hover:opacity-100 z-20 cursor-pointer"
        aria-label="Next slide"
      >
        <ChevronRight className="w-4 h-4" />
      </button>

      {/* Pagination Indicators (Dots) */}
      <div className="absolute bottom-2 right-4 z-20 flex items-center space-x-1.5 pointer-events-auto">
        {HERO_SLIDES.map((_, idx) => (
          <button
            key={idx}
            onClick={() => setCurrentSlide(idx)}
            className={`transition-all duration-300 rounded-full h-1.5 cursor-pointer ${
              currentSlide === idx
                ? 'w-5 bg-red-500 shadow-sm'
                : 'w-1.5 bg-white/40 hover:bg-white/70'
            }`}
            aria-label={`Go to slide ${idx + 1}`}
          />
        ))}
      </div>
    </div>
  );
};
