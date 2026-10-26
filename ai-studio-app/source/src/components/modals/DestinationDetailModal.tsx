import React, { useState } from 'react';
import { 
  X, 
  MapPin, 
  DollarSign, 
  Clock, 
  Award, 
  Building2, 
  CheckCircle2, 
  Briefcase, 
  Languages, 
  Calendar,
  Share2,
  Sparkles,
  ShieldCheck,
  Check,
  Globe,
  ArrowLeftRight
} from 'lucide-react';
import { StudyDestination } from '../../types';

interface DestinationDetailModalProps {
  destination: StudyDestination | null;
  onClose: () => void;
  onBookConsultation: (country: string) => void;
}

export const DestinationDetailModal: React.FC<DestinationDetailModalProps> = ({
  destination,
  onClose,
  onBookConsultation,
}) => {
  const [activeSubTab, setActiveSubTab] = useState<'overview' | 'costs' | 'universities' | 'visa'>('overview');

  if (!destination) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-stone-950/60 backdrop-blur-xs p-0 sm:p-4">
      <div 
        className="bg-white w-full max-w-[460px] max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-200 border border-stone-200/80"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Modal Header with Hero Image */}
        <div className="relative h-48 shrink-0 overflow-hidden bg-stone-900">
          <img
            src={destination.imageUrl}
            alt={destination.name}
            className="w-full h-full object-cover opacity-85"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/50 to-transparent" />

          {/* Close & Flag Header */}
          <div className="absolute top-3.5 left-4 right-4 flex items-center justify-between z-10">
            <div className="flex items-center space-x-2 bg-stone-900/80 backdrop-blur-md px-3 py-1 rounded-full border border-stone-700/60 shadow-xs">
              <span className="text-base">{destination.flagEmoji}</span>
              <span className="text-xs font-bold text-white uppercase tracking-wider">{destination.code}</span>
            </div>

            <button
              onClick={onClose}
              className="w-8 h-8 rounded-full bg-stone-900/80 backdrop-blur-md text-white hover:bg-stone-800 transition flex items-center justify-center border border-stone-700/60 cursor-pointer"
              aria-label="Close"
            >
              <X className="w-4 h-4" />
            </button>
          </div>

          {/* Destination Title & Tagline */}
          <div className="absolute bottom-4 left-4 right-4 z-10 text-white space-y-1">
            <h2 className="text-2xl font-black tracking-tight text-white flex items-center gap-2">
              <span>{destination.name}</span>
            </h2>
            <p className="text-xs text-stone-300 line-clamp-1 font-normal leading-relaxed">
              {destination.tagline}
            </p>
          </div>
        </div>

        {/* Sub-tabs with Soothing Design */}
        <div className="flex border-b border-stone-200/80 bg-stone-50/70 px-3 pt-2">
          {[
            { id: 'overview', label: 'Overview' },
            { id: 'costs', label: 'Costs & Work' },
            { id: 'universities', label: 'Top Unis' },
            { id: 'visa', label: 'PR & Visa' },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveSubTab(tab.id as any)}
              className={`flex-1 py-2.5 text-xs font-semibold transition-all border-b-2 cursor-pointer ${
                activeSubTab === tab.id
                  ? 'border-rose-600 text-rose-700'
                  : 'border-transparent text-stone-500 hover:text-stone-800'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Modal Scrollable Body */}
        <div className="flex-1 overflow-y-auto p-5 space-y-4 text-stone-800 text-xs">
          {activeSubTab === 'overview' && (
            <div className="space-y-4">
              <p className="text-xs text-stone-600 leading-relaxed font-normal">
                {destination.description}
              </p>

              {/* Key Highlights */}
              <div className="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 space-y-2.5">
                <div className="flex items-center space-x-1.5 text-rose-800 font-bold text-xs">
                  <Sparkles className="w-3.5 h-3.5 text-rose-600" />
                  <span>Why Indian Students Choose {destination.name}</span>
                </div>
                <div className="space-y-2">
                  {destination.highlights.map((h, i) => (
                    <div key={i} className="flex items-start space-x-2.5 text-xs text-stone-700">
                      <div className="w-4 h-4 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                        <Check className="w-3 h-3 stroke-[2.5]" />
                      </div>
                      <span className="leading-snug">{h}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Popular Degrees */}
              <div>
                <h4 className="font-bold text-stone-900 mb-2 flex items-center gap-1.5 text-xs">
                  <Briefcase className="w-3.5 h-3.5 text-stone-500" />
                  <span>High-Employability Courses</span>
                </h4>
                <div className="flex flex-wrap gap-1.5">
                  {destination.popularCourses.map((c, i) => (
                    <span key={i} className="bg-stone-100 text-stone-700 px-2.5 py-1 rounded-xl text-[11px] font-medium border border-stone-200/70">
                      {c}
                    </span>
                  ))}
                </div>
              </div>

              {/* Language & Intakes */}
              <div className="grid grid-cols-2 gap-2.5 pt-1">
                <div className="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                  <span className="text-[10px] text-stone-500 uppercase font-semibold flex items-center gap-1">
                    <Calendar className="w-3 h-3 text-rose-500" /> Major Intakes
                  </span>
                  <p className="font-bold text-stone-800 text-xs mt-1">
                    {destination.intakes.join(', ')}
                  </p>
                </div>

                <div className="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                  <span className="text-[10px] text-stone-500 uppercase font-semibold flex items-center gap-1">
                    <Languages className="w-3 h-3 text-rose-500" /> Test Score Req.
                  </span>
                  <p className="font-bold text-stone-800 text-xs mt-1">
                    {destination.languageRequirements}
                  </p>
                </div>
              </div>
            </div>
          )}

          {activeSubTab === 'costs' && (
            <div className="space-y-3">
              <div className="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/60 space-y-1">
                <div className="flex items-center space-x-1.5 text-amber-900 font-bold text-xs">
                  <DollarSign className="w-4 h-4 text-amber-600" />
                  <span>Annual Estimated Tuition</span>
                </div>
                <p className="text-base font-black text-stone-900">{destination.avgTuitionYear}</p>
                <p className="text-[11px] text-stone-600">Varies based on bachelor, master, STEM or business field.</p>
              </div>

              <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200/70 space-y-1">
                <div className="flex items-center space-x-1.5 text-stone-700 font-bold text-xs">
                  <Building2 className="w-4 h-4 text-stone-500" />
                  <span>Estimated Living Expenses</span>
                </div>
                <p className="text-base font-black text-stone-900">{destination.livingExpensesYear}</p>
                <p className="text-[11px] text-stone-600">Includes accommodation, groceries, student transport, and utilities.</p>
              </div>

              <div className="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/60 space-y-1">
                <div className="flex items-center space-x-1.5 text-emerald-800 font-bold text-xs">
                  <Clock className="w-4 h-4 text-emerald-600" />
                  <span>Post-Study Work Permit (PSW)</span>
                </div>
                <p className="text-sm font-black text-stone-900">{destination.postStudyWorkVisa}</p>
                <p className="text-[11px] text-stone-600">Allows full-time work with local employers post graduation.</p>
              </div>
            </div>
          )}

          {activeSubTab === 'universities' && (
            <div className="space-y-3">
              <p className="text-xs text-stone-600 font-normal">
                Trans Globe Indore maintains direct fast-track application pathways with partner institutions across {destination.name}:
              </p>
              <div className="space-y-2">
                {destination.topUniversities.map((uni, i) => (
                  <div key={i} className="p-3.5 rounded-2xl border border-stone-200/80 bg-stone-50/60 hover:bg-stone-50 transition flex items-center justify-between">
                    <div className="flex items-center space-x-2.5">
                      <div className="w-6 h-6 rounded-full bg-stone-900 text-white font-bold flex items-center justify-center text-xs">
                        {i + 1}
                      </div>
                      <span className="font-bold text-stone-900 text-xs">{uni}</span>
                    </div>
                    <span className="text-[10px] bg-white text-stone-600 font-medium px-2 py-0.5 rounded-full border border-stone-200 shadow-2xs">
                      Official Partner
                    </span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeSubTab === 'visa' && (
            <div className="space-y-3">
              <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200/80 space-y-1.5">
                <div className="flex items-center space-x-1.5 text-stone-900 font-bold text-xs">
                  <Award className="w-4 h-4 text-rose-600" />
                  <span>Permanent Residency & Settlement</span>
                </div>
                <p className="text-xs text-stone-600 leading-relaxed font-normal">
                  {destination.prPathway}
                </p>
              </div>

              <div className="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/60 space-y-1.5">
                <div className="flex items-center space-x-1.5 text-emerald-900 font-bold text-xs">
                  <ShieldCheck className="w-4 h-4 text-emerald-600" />
                  <span>Trans Globe Indore Visa Guarantee</span>
                </div>
                <p className="text-[11px] text-stone-600 leading-relaxed font-normal">
                  Our specialists can help review documents and financial evidence. Confirm the current visa requirements and your individual eligibility with a counsellor.
                </p>
              </div>
            </div>
          )}
        </div>

        {/* Modal Sticky Bottom CTA */}
        <div className="p-4 bg-stone-50/80 border-t border-stone-200/80 flex items-center space-x-2">
          <button
            onClick={() => {
              onClose();
              onBookConsultation(destination.name);
            }}
            className="flex-1 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-3 px-4 rounded-2xl text-xs transition flex items-center justify-center space-x-2 shadow-md cursor-pointer"
          >
            <span>Plan {destination.name} Application</span>
          </button>
        </div>
      </div>
    </div>
  );
};
