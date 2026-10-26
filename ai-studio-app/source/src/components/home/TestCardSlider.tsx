import React from 'react';
import { BookOpen, Award, Clock, ArrowRight, CheckCircle2 } from 'lucide-react';
import { ENGLISH_TESTS } from '../../data/liveCatalog';
import { EnglishTestInfo } from '../../types';
import { BookmarkButton } from '../common/BookmarkButton';

interface TestCardSliderProps {
  onNavigateTest?: (testId: string) => void;
  onNavigateTestsList?: () => void;
  onOpenBooking: () => void;
}

export const TestCardSlider: React.FC<TestCardSliderProps> = ({
  onNavigateTest,
  onNavigateTestsList,
  onOpenBooking,
}) => {
  return (
    <div className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
      {/* App Section Header with Nav Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 shadow-xs">
            <BookOpen className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                English & Entrance Test Prep
              </h3>
              <span className="text-[10px] font-bold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full border border-blue-200">
                Test preparation
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              Classroom & online coaching in Indore with mock test simulators
            </p>
          </div>
        </div>

      </div>

      {/* Horizontal Slider Track */}
      <div
        tabIndex={0}
        aria-label="Swipe to browse test programs"
        className="flex overflow-x-auto gap-3.5 pb-2 pt-1 scrollbar-none snap-x snap-mandatory scroll-smooth -mx-1 px-1"
      >
        {ENGLISH_TESTS.map((test: EnglishTestInfo) => (
          <div
            key={test.id}
            onClick={() => {
              if (onNavigateTest) {
                onNavigateTest(test.id);
              } else {
                onOpenBooking();
              }
            }}
            className="min-w-[230px] max-w-[230px] bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-blue-300 transition-all duration-200 cursor-pointer shrink-0 snap-start flex flex-col justify-between group select-none"
          >
            {/* Test Banner */}
            <div className="relative h-24 w-full bg-slate-900 overflow-hidden">
              <img
                src={test.bannerImage}
                alt={test.name}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
              
              <div className="absolute top-2.5 left-2.5 bg-blue-600 text-white font-black text-[9px] px-2 py-0.5 rounded-md shadow-xs">
                {test.code}
              </div>

              <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                <span className="bg-slate-900/80 backdrop-blur-xs text-blue-200 font-bold text-[8px] px-1.5 py-0.5 rounded border border-white/20">
                  {test.category}
                </span>
                <BookmarkButton
                  type="test"
                  id={test.id}
                  title={test.name}
                  size="xs"
                  variant="glass"
                />
              </div>

              <div className="absolute bottom-2 left-2.5 right-2.5 text-white">
                <h4 className="font-extrabold text-xs leading-snug line-clamp-1 group-hover:text-blue-300 transition-colors">
                  {test.name}
                </h4>
              </div>
            </div>

            {/* Test Details */}
            <div className="p-3 space-y-2 text-xs flex-1 flex flex-col justify-between">
              <div className="space-y-1.5">
                <div className="flex items-center justify-between text-[10px]">
                  <span className="text-slate-500 font-medium">Target Score:</span>
                  <span className="font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 line-clamp-1">
                    {test.minTargetBand}
                  </span>
                </div>

                <div className="flex items-center justify-between text-[10px]">
                  <span className="text-slate-500 font-medium">Duration:</span>
                  <span className="font-semibold text-slate-800 line-clamp-1">
                    {test.duration}
                  </span>
                </div>

                <p className="text-[10px] text-slate-600 line-clamp-2 leading-relaxed">
                  {test.tagline}
                </p>
              </div>

              {/* Action Button */}
              <div className="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] font-bold text-blue-700 group-hover:text-blue-800">
                <span>Modules & Batch Timings</span>
                <span className="w-5 h-5 rounded-full bg-blue-100 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <ArrowRight className="w-3 h-3" />
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Section Footer */}
      <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
        <span className="text-[11px] text-slate-500 font-medium">
          IELTS, PTE, TOEFL, Duolingo, GRE, GMAT, SAT & German
        </span>
        <button
          onClick={() => {
            if (onNavigateTestsList) {
              onNavigateTestsList();
            } else if (onNavigateTest) {
              onNavigateTest('ielts');
            }
          }}
          className="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 cursor-pointer hover:underline"
        >
          <span>All Test Programs (8)</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </button>
      </div>
    </div>
  );
};
