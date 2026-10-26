import React, { useState, useEffect, useRef } from 'react';
import { Sparkles, Calendar, Award, ArrowRight } from 'lucide-react';
import { HERO_SLIDES } from '../../data/liveCatalog';
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
      className="relative rounded-3xl overflow-hidden shadow-lg border border-stone-850 bg-stone-950 select-none group"
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
          <div key={slide.id} className="w-full shrink-0 relative min-h-[250px] sm:min-h-[260px] flex flex-col justify-end p-5 text-white">
            {/* Background Photographic Image */}
            <img
              src={slide.imageUrl}
              alt={slide.title}
              referrerPolicy="no-referrer"
              className="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 opacity-90"
            />

            {/* Calming ambient backdrop gradient */}
            <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/70 to-stone-950/25" />

            {/* Content Box */}
            <div className="relative z-10 space-y-2.5">
              {/* Badges */}
              <div className="flex items-center justify-between text-xs">
                <span className="text-[10px] font-semibold text-amber-200 bg-amber-400/15 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-amber-400/25 flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
                  {slide.badge}
                </span>

                <span className="text-[10px] bg-rose-950/80 backdrop-blur-md text-rose-200 font-bold px-2.5 py-0.5 rounded-full border border-rose-800/40">
                  {slide.statBadge}
                </span>
              </div>

              {/* Headings */}
              <div>
                <h2 className="text-xl font-black text-white leading-snug tracking-tight">
                  {slide.title}{' '}
                  <span className="text-amber-200">
                    {slide.titleHighlight}
                  </span>
                </h2>
                <p className="text-xs text-stone-300 mt-1 leading-relaxed font-normal line-clamp-2">
                  {slide.description}
                </p>
              </div>

              {/* Action Buttons */}
              <div className="pt-1 flex items-center space-x-2">
                <button
                  onClick={() => handleCtaAction(slide.primaryAction)}
                  className="flex-1 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold py-2.5 px-3.5 rounded-2xl transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
                >
                  <span>{slide.primaryCta}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <button
                  onClick={() => handleCtaAction(slide.secondaryAction)}
                  className="bg-stone-900/80 hover:bg-stone-800 active:bg-stone-700 text-stone-200 text-xs font-medium py-2.5 px-3.5 rounded-2xl transition flex items-center space-x-1 border border-stone-750 backdrop-blur-sm cursor-pointer"
                >
                  <span>{slide.secondaryCta}</span>
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Pagination Indicators (Dots) */}
      <div className="absolute bottom-2.5 right-4 z-20 flex items-center space-x-1.5 pointer-events-auto">
        {HERO_SLIDES.map((_, idx) => (
          <button
            key={idx}
            onClick={() => setCurrentSlide(idx)}
            className={`transition-all duration-300 rounded-full h-1.5 cursor-pointer ${
              currentSlide === idx
                ? 'w-5 bg-rose-500 shadow-xs'
                : 'w-1.5 bg-white/40 hover:bg-white/70'
            }`}
            aria-label={`Go to slide ${idx + 1}`}
          />
        ))}
      </div>
    </div>
  );
};
