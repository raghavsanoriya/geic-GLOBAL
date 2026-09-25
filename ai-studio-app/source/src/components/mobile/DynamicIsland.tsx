import React, { useState } from 'react';
import { Calendar, Sparkles, ChevronRight, X, PhoneCall } from 'lucide-react';
import { GEIC_BRAND, UPCOMING_EXPO } from '../../data/liveCatalog';

interface DynamicIslandProps {
  onOpenExpo: () => void;
  onOpenBooking: () => void;
}

export const DynamicIsland: React.FC<DynamicIslandProps> = ({ onOpenExpo, onOpenBooking }) => {
  const [isExpanded, setIsExpanded] = useState(false);

  return (
    <div className="flex justify-center w-full min-w-0 min-h-10 px-2 select-none relative z-50">
      <div
        onClick={() => setIsExpanded(!isExpanded)}
        className={`bg-slate-950 text-white transition-colors duration-200 cursor-pointer shadow-lg border border-slate-800/80 ${
          isExpanded
            ? 'absolute top-full left-2 right-2 mx-auto max-w-[340px] px-4 py-3 rounded-xl shadow-2xl'
            : 'w-full max-w-[280px] min-w-0 px-3 py-1.5 flex items-center h-9 rounded-full'
        }`}
      >
        {!isExpanded ? (
          <div className="flex min-w-0 items-center justify-between w-full gap-2 text-[11px] font-medium tracking-tight">
            <div className="flex items-center space-x-1.5">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
              </span>
              <span className="font-semibold text-white truncate min-w-0">GEIC Events</span>
            </div>
            <span className="shrink-0 whitespace-nowrap text-[10px] bg-red-600/90 text-white px-1.5 py-0.5 rounded-full font-bold">
              {UPCOMING_EXPO.registrationOpen ? UPCOMING_EXPO.date : 'Updates'}
            </span>
          </div>
        ) : (
          <div className="space-y-2.5 text-left" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-1.5">
                <span className="p-1 rounded-md bg-red-600/20 text-red-400">
                  <Calendar className="w-3.5 h-3.5" />
                </span>
                <span className="text-xs font-bold text-white tracking-wide">
                  {UPCOMING_EXPO.title}
                </span>
              </div>
              <button
                onClick={() => setIsExpanded(false)}
                className="text-slate-400 hover:text-white p-0.5 rounded-full transition"
                aria-label="Close island"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            </div>

            <p className="text-[11px] text-slate-300 leading-snug">
              {UPCOMING_EXPO.subtitle}
            </p>

            <div className="flex items-center space-x-2 pt-1">
              <button
                onClick={() => {
                  setIsExpanded(false);
                  onOpenExpo();
                }}
                className="flex-1 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold py-1.5 px-2.5 rounded-xl transition flex items-center justify-center space-x-1 shadow-sm"
              >
                <Sparkles className="w-3 h-3" />
                <span>View events</span>
                <ChevronRight className="w-3 h-3" />
              </button>

              <button
                onClick={() => {
                  setIsExpanded(false);
                  onOpenBooking();
                }}
                className="bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold py-1.5 px-2.5 rounded-xl transition flex items-center space-x-1"
              >
                <PhoneCall className="w-3 h-3" />
                <span>Book 1-on-1</span>
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
