import React from 'react';
import { Calendar, MapPin, Sparkles, ArrowRight, Ticket } from 'lucide-react';
import { UPCOMING_EVENTS_LIST } from '../../data/liveCatalog';
import { UpcomingEventInfo } from '../../types';
import { BookmarkButton } from '../common/BookmarkButton';

interface EventCardSliderProps {
  onNavigateEvent?: (eventId: string) => void;
  onNavigateEventsList?: () => void;
  onOpenExpo: () => void;
  onOpenBooking: () => void;
}

export const EventCardSlider: React.FC<EventCardSliderProps> = ({
  onNavigateEvent,
  onNavigateEventsList,
  onOpenExpo,
  onOpenBooking,
}) => {
  return (
    <div className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
      {/* App Section Header with Nav Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-xs">
            <Calendar className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                Events & Workshops
              </h3>
              <span className="text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full border border-amber-200">
                GEIC Events
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              Direct face-to-face sessions with international delegates in Indore
            </p>
          </div>
        </div>

      </div>

      {/* Horizontal Slider Track */}
      <div
        tabIndex={0}
        aria-label="Swipe to browse events"
        className="flex overflow-x-auto gap-3.5 pb-2 pt-1 scrollbar-none snap-x snap-mandatory scroll-smooth -mx-1 px-1"
      >
        {UPCOMING_EVENTS_LIST.map((ev: UpcomingEventInfo) => (
          <div
            key={ev.id}
            onClick={() => {
              if (onNavigateEvent) {
                onNavigateEvent(ev.id);
              } else if (ev.id === 'expo-2026') {
                onOpenExpo();
              } else {
                onOpenBooking();
              }
            }}
            className="min-w-[240px] max-w-[240px] bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-amber-300 transition-all duration-200 cursor-pointer shrink-0 snap-start flex flex-col justify-between group select-none"
          >
            {/* Event Banner */}
            <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
              <img
                src={ev.bannerUrl}
                alt={ev.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
              
              <div className="absolute top-2.5 left-2.5 bg-red-600 text-white font-black text-[9px] px-2 py-0.5 rounded-md shadow-xs flex items-center gap-1">
                <span>{ev.badge}</span>
              </div>

              <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                <div className="bg-emerald-950/90 text-emerald-300 border border-emerald-500/40 font-black text-[8px] px-1.5 py-0.5 rounded shadow-xs flex items-center gap-1">
                  <Ticket className="w-2.5 h-2.5" />
                  <span>EVENT DETAILS</span>
                </div>
                <BookmarkButton
                  type="event"
                  id={ev.id}
                  title={ev.title}
                  size="xs"
                  variant="glass"
                />
              </div>

              <div className="absolute bottom-2 left-2.5 right-2.5 text-white">
                <h4 className="font-extrabold text-xs leading-snug line-clamp-1 group-hover:text-amber-300 transition-colors">
                  {ev.title}
                </h4>
              </div>
            </div>

            {/* Event Meta Details */}
            <div className="p-3 space-y-2 text-xs flex-1 flex flex-col justify-between">
              <div className="space-y-1.5">
                <div className="flex items-center space-x-1.5 text-[11px] text-slate-800 font-bold">
                  <Calendar className="w-3.5 h-3.5 text-red-600 shrink-0" />
                  <span className="truncate">{ev.date}</span>
                </div>

                <div className="flex items-center space-x-1.5 text-[10px] text-slate-500">
                  <MapPin className="w-3 h-3 text-slate-400 shrink-0" />
                  <span className="truncate">{ev.city} • {ev.venue.split(',')[0]}</span>
                </div>

                {ev.participatingUniversitiesCount && (
                  <div className="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md font-semibold inline-block border border-amber-200">
                    ★ {ev.participatingUniversitiesCount}+ Universities Represented
                  </div>
                )}
              </div>

              {/* Action Button */}
              <div className="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] font-bold text-amber-700 group-hover:text-amber-800">
                <span>Event Details</span>
                <span className="w-5 h-5 rounded-full bg-amber-100 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <ArrowRight className="w-3 h-3" />
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Section Footer / View All Link */}
      <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
        <span className="text-[11px] text-slate-500 font-medium">
          {UPCOMING_EVENTS_LIST.length} Summits & Admission Days
        </span>
        <button
          onClick={() => {
            if (onNavigateEventsList) {
              onNavigateEventsList();
            } else if (onNavigateEvent) {
              onNavigateEvent('expo-2026');
            }
          }}
          className="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer hover:underline"
        >
          <span>View All {UPCOMING_EVENTS_LIST.length} Events</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </button>
      </div>
    </div>
  );
};
