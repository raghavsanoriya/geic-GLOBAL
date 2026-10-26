import React, { useState, useEffect, useRef } from 'react';
import { 
  Star, 
  CheckCircle2, 
  Pause, 
  Play, 
  Quote, 
  MessageSquareHeart 
} from 'lucide-react';
import { STUDENT_REVIEWS } from '../../data/liveCatalog';
import { StudentReview } from '../../types';

export const ReviewsAutoSlide: React.FC = () => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isAutoPlay, setIsAutoPlay] = useState(true);
  const [isHovered, setIsHovered] = useState(false);
  const touchStartX = useRef<number | null>(null);
  const touchEndX = useRef<number | null>(null);

  const total = STUDENT_REVIEWS.length;

  // Auto slide every 4.5 seconds
  useEffect(() => {
    if (!isAutoPlay || isHovered || total < 2) return;

    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % total);
    }, 4500);

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

  const activeReview = STUDENT_REVIEWS[currentIndex];
  if (!activeReview) return <section className="rounded-3xl border border-slate-200 bg-white p-5 space-y-2">
    <h3 className="font-bold text-sm">Student reviews</h3>
    <p className="text-xs text-slate-500">No student reviews have been published in the app yet.</p>
  </section>;

  return (
    <div 
      className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4 select-none"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
    >
      {/* App Section Header with Nav Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-xs">
            <MessageSquareHeart className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                Verified Student Reviews
              </h3>
              <span className="flex items-center text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                <Star className="w-3 h-3 fill-amber-400 text-amber-400 mr-1" />
                4.8 / 5.0
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              495+ authentic Google reviews for Trans Globe Indore
            </p>
          </div>
        </div>

        {/* Play/Pause & Navigation Arrows */}
        <div className="flex items-center space-x-1.5 shrink-0">
          <button
            onClick={() => setIsAutoPlay(!isAutoPlay)}
            className="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition cursor-pointer border border-slate-200 shadow-xs"
            title={isAutoPlay ? 'Pause Auto-Slide' : 'Resume Auto-Slide'}
            aria-label={isAutoPlay ? 'Pause Auto-Slide' : 'Resume Auto-Slide'}
          >
            {isAutoPlay ? <Pause className="w-3.5 h-3.5" /> : <Play className="w-3.5 h-3.5 text-amber-600" />}
          </button>
        </div>
      </div>

      {/* Main Active Auto-Sliding Review Card */}
      <div className="relative p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-50 via-amber-50/20 to-white border border-slate-200 shadow-xs space-y-3 transition-all duration-300">
        <div className="absolute top-3 right-3 text-amber-200/50 pointer-events-none">
          <Quote className="w-10 h-10 -rotate-12" />
        </div>

        {/* Reviewer Header */}
        <div className="flex items-center justify-between relative z-10">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-900 to-slate-700 text-white font-black flex items-center justify-center text-sm shadow-md border-2 border-white">
              {activeReview.avatarLetter}
            </div>
            <div>
              <div className="flex items-center space-x-1.5">
                <h5 className="font-extrabold text-xs text-slate-900 leading-tight">
                  {activeReview.studentName}
                </h5>
                <span className="text-[9px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.2 rounded-full flex items-center gap-0.5">
                  <CheckCircle2 className="w-2.5 h-2.5" />
                  Verified
                </span>
              </div>
              <p className="text-[10px] text-slate-500 mt-0.5">
                {activeReview.university} • <strong className="text-slate-700">{activeReview.destination}</strong>
              </p>
            </div>
          </div>

          {/* 5-Star Rating & Date */}
          <div className="text-right">
            <div className="flex items-center text-amber-400 justify-end">
              {[...Array(activeReview.rating)].map((_, i) => (
                <Star key={i} className="w-3.5 h-3.5 fill-amber-400" />
              ))}
            </div>
            <span className="text-[10px] text-slate-400 font-medium block mt-0.5">
              {activeReview.reviewDate}
            </span>
          </div>
        </div>

        {/* Course Studied Badge */}
        <div className="inline-block bg-slate-100 text-slate-700 text-[10px] font-semibold px-2.5 py-0.5 rounded-lg border border-slate-200">
          Enrolled: {activeReview.course}
        </div>

        {/* Review Quote Text */}
        <p className="text-xs text-slate-700 italic leading-relaxed pt-1 relative z-10">
          "{activeReview.comment}"
        </p>

        {/* Review Footer Verification Badge */}
        <div className="flex items-center justify-between pt-2 border-t border-slate-200/80 text-[10px]">
          <span className="text-emerald-700 font-semibold flex items-center gap-1">
            <CheckCircle2 className="w-3 h-3 text-emerald-600" />
            Admitted & Visa Processed via Trans Globe Indore
          </span>
          <span className="text-slate-400 font-medium">
            Review {currentIndex + 1} of {total}
          </span>
        </div>
      </div>

      {/* Progress Dots Indicator */}
      <div className="flex items-center justify-center gap-1.5 pt-1">
        {STUDENT_REVIEWS.map((_, i) => (
          <button
            key={i}
            onClick={() => {
              setCurrentIndex(i);
              setIsAutoPlay(false);
            }}
            className={`h-1.5 rounded-full transition-all duration-300 cursor-pointer ${
              currentIndex === i
                ? 'w-6 bg-amber-500'
                : 'w-1.5 bg-slate-300 hover:bg-slate-400'
            }`}
            aria-label={`Jump to review ${i + 1}`}
          />
        ))}
      </div>

      {/* Section Footer */}
      <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
        <span className="text-[11px] text-slate-500 font-medium">
          Ranked #1 Overseas Consultancy in Indore by Google Reviews
        </span>
        <a
          href="https://maps.google.com/?q=Trans+Globe+Indore"
          target="_blank"
          rel="noreferrer"
          className="text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1 cursor-pointer hover:underline"
        >
          <span>View on Google Maps</span>
          <Star className="w-3 h-3 fill-amber-500 text-amber-500" />
        </a>
      </div>
    </div>
  );
};
