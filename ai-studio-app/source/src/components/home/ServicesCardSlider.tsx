import React from 'react';
import { Briefcase, ArrowRight, CheckCircle2 } from 'lucide-react';
import { GEIC_SERVICES } from '../../data/liveCatalog';
import { GeicService } from '../../types';
import { BookmarkButton } from '../common/BookmarkButton';

interface ServicesCardSliderProps {
  onNavigateService?: (serviceId: string) => void;
  onNavigateServicesList?: () => void;
  onOpenBooking: (serviceTitle?: string) => void;
}

export const ServicesCardSlider: React.FC<ServicesCardSliderProps> = ({
  onNavigateService,
  onNavigateServicesList,
  onOpenBooking,
}) => {
  return (
    <div className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
      {/* App Section Header with Nav Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0 shadow-xs">
            <Briefcase className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                Our {GEIC_SERVICES.length} Global Services
              </h3>
              <span className="text-[10px] font-bold bg-red-50 text-red-600 px-2 py-0.5 rounded-full border border-red-200">
                Step-by-Step
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              Swipe through our end-to-end guidance from counselling to PR
            </p>
          </div>
        </div>

      </div>

      {/* Horizontal Slider Track */}
      <div
        tabIndex={0}
        aria-label="Swipe to browse services"
        className="flex overflow-x-auto gap-3.5 pb-2 pt-1 scrollbar-none snap-x snap-mandatory scroll-smooth -mx-1 px-1"
      >
        {GEIC_SERVICES.map((srv: GeicService) => (
          <div
            key={srv.id}
            onClick={() => {
              if (onNavigateService) {
                onNavigateService(srv.id);
              } else {
                onOpenBooking(srv.title);
              }
            }}
            className="min-w-[230px] max-w-[230px] bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-red-300 transition-all duration-200 cursor-pointer shrink-0 snap-start flex flex-col justify-between group select-none"
          >
            {/* Banner Image with Step Overlay */}
            <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
              <img
                src={srv.bannerImage}
                alt={srv.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
              
              <div className="absolute top-2.5 left-2.5 bg-red-600 text-white font-black text-[9px] px-2 py-0.5 rounded-md shadow-xs flex items-center gap-1">
                <span>STEP</span>
                <span>{srv.stepNumber}</span>
              </div>

              <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
                <span className="bg-slate-900/80 backdrop-blur-xs text-slate-200 font-bold text-[9px] px-1.5 py-0.5 rounded border border-white/20">
                  {srv.category}
                </span>
                <BookmarkButton
                  type="service"
                  id={srv.id}
                  title={srv.title}
                  size="xs"
                  variant="glass"
                />
              </div>

              <div className="absolute bottom-2 left-2.5 right-2.5 text-white">
                <h4 className="font-extrabold text-xs leading-snug line-clamp-1 group-hover:text-amber-300 transition-colors">
                  {srv.title}
                </h4>
              </div>
            </div>

            {/* Service Details & Benefits */}
            <div className="p-3 space-y-2 text-xs flex-1 flex flex-col justify-between">
              <div>
                <p className="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                  {srv.shortDesc}
                </p>

                {/* Key Benefit Highlights */}
                {srv.keyBenefits && srv.keyBenefits.length > 0 && (
                  <div className="mt-2 pt-2 border-t border-slate-200/70 space-y-1">
                    <div className="flex items-center space-x-1.5 text-[10px] text-slate-700 font-medium">
                      <CheckCircle2 className="w-3 h-3 text-red-500 shrink-0" />
                      <span className="truncate">{srv.keyBenefits[0]}</span>
                    </div>
                  </div>
                )}
              </div>

              {/* Action Button */}
              <div className="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] font-bold text-red-600 group-hover:text-red-700">
                <span>View Full Page</span>
                <span className="w-5 h-5 rounded-full bg-red-50 group-hover:bg-red-600 group-hover:text-white flex items-center justify-center transition-colors">
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
          {GEIC_SERVICES.length} Dedicated Service Pages Available
        </span>
        <button
          onClick={() => {
            if (onNavigateServicesList) {
              onNavigateServicesList();
            } else if (onNavigateService) {
              onNavigateService('counselling');
            }
          }}
          className="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer hover:underline"
        >
          <span>Explore All {GEIC_SERVICES.length} Services</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </button>
      </div>
    </div>
  );
};
