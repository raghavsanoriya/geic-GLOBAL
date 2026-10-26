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
  Sparkles
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
    <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/70 backdrop-blur-xs p-0 sm:p-4">
      <div 
        className="bg-white w-full max-w-[440px] max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-200"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Modal Header with Hero Image */}
        <div className="relative h-44 shrink-0 overflow-hidden bg-slate-900">
          <img
            src={destination.imageUrl}
            alt={destination.name}
            className="w-full h-full object-cover opacity-80"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

          {/* Close & Action Buttons */}
          <div className="absolute top-3 left-3 right-3 flex items-center justify-between z-10">
            <div className="flex items-center space-x-2 bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-full border border-slate-700/50">
              <span className="text-base">{destination.flagEmoji}</span>
              <span className="text-xs font-bold text-white uppercase tracking-wider">{destination.code}</span>
            </div>

            <button
              onClick={onClose}
              className="p-1.5 rounded-full bg-slate-900/80 text-white hover:bg-slate-800 transition"
              aria-label="Close"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Destination Title & Tagline */}
          <div className="absolute bottom-3 left-4 right-4 z-10">
            <h2 className="text-2xl font-black text-white flex items-center gap-2">
              <span>{destination.name}</span>
            </h2>
            <p className="text-xs text-slate-200 line-clamp-1 font-medium mt-0.5">
              {destination.tagline}
            </p>
          </div>
        </div>

        {/* Sub-tabs */}
        <div className="flex border-b border-slate-100 bg-slate-50 px-3 pt-2">
          {[
            { id: 'overview', label: 'Overview' },
            { id: 'costs', label: 'Costs & Work' },
            { id: 'universities', label: 'Top Unis' },
            { id: 'visa', label: 'PR & Visa' },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveSubTab(tab.id as any)}
              className={`flex-1 py-2 text-xs font-bold transition-all border-b-2 cursor-pointer ${
                activeSubTab === tab.id
                  ? 'border-red-600 text-red-600'
                  : 'border-transparent text-slate-500 hover:text-slate-900'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Modal Scrollable Body */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4 text-slate-800 text-xs">
          {activeSubTab === 'overview' && (
            <div className="space-y-3.5">
              <p className="text-xs text-slate-600 leading-relaxed font-normal">
                {destination.description}
              </p>

              {/* Key Highlights */}
              <div className="bg-red-50/70 border border-red-100 rounded-2xl p-3.5 space-y-2">
                <div className="flex items-center space-x-1.5 text-red-700 font-bold">
                  <Sparkles className="w-3.5 h-3.5 text-red-600" />
                  <span>Why Indian Students Choose {destination.name}</span>
                </div>
                <div className="space-y-1.5">
                  {destination.highlights.map((h, i) => (
                    <div key={i} className="flex items-start space-x-2 text-[11px] text-slate-700">
                      <CheckCircle2 className="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5" />
                      <span>{h}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Popular Degrees */}
              <div>
                <h4 className="font-bold text-slate-900 mb-2 flex items-center gap-1.5">
                  <Briefcase className="w-3.5 h-3.5 text-slate-700" />
                  <span>High-Employability Courses</span>
                </h4>
                <div className="flex flex-wrap gap-1.5">
                  {destination.popularCourses.map((c, i) => (
                    <span key={i} className="bg-slate-100 text-slate-800 px-2.5 py-1 rounded-lg text-[11px] font-medium border border-slate-200">
                      {c}
                    </span>
                  ))}
                </div>
              </div>

              {/* Language & Intakes */}
              <div className="grid grid-cols-2 gap-2 pt-1">
                <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                  <span className="text-[10px] text-slate-500 uppercase font-semibold flex items-center gap-1">
                    <Calendar className="w-3 h-3 text-red-500" /> Major Intakes
                  </span>
                  <p className="font-bold text-slate-800 text-[11px] mt-1">
                    {destination.intakes.join(', ')}
                  </p>
                </div>

                <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                  <span className="text-[10px] text-slate-500 uppercase font-semibold flex items-center gap-1">
                    <Languages className="w-3 h-3 text-red-500" /> Test Score Req.
                  </span>
                  <p className="font-bold text-slate-800 text-[11px] mt-1">
                    {destination.languageRequirements}
                  </p>
                </div>
              </div>
            </div>
          )}

          {activeSubTab === 'costs' && (
            <div className="space-y-3">
              <div className="p-3 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-1.5">
                <div className="flex items-center space-x-1.5 text-amber-900 font-bold">
                  <DollarSign className="w-4 h-4 text-amber-600" />
                  <span>Annual Estimated Tuition</span>
                </div>
                <p className="text-base font-black text-slate-900">{destination.avgTuitionYear}</p>
                <p className="text-[11px] text-slate-600">Varies based on bachelor, master, STEM or business field.</p>
              </div>

              <div className="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div className="flex items-center space-x-1.5 text-slate-700 font-bold">
                  <Building2 className="w-4 h-4 text-slate-600" />
                  <span>Estimated Living Expenses</span>
                </div>
                <p className="text-base font-black text-slate-900">{destination.livingExpensesYear}</p>
                <p className="text-[11px] text-slate-600">Includes accommodation, groceries, student transport, and utilities.</p>
              </div>

              <div className="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-1.5">
                <div className="flex items-center space-x-1.5 text-emerald-800 font-bold">
                  <Clock className="w-4 h-4 text-emerald-600" />
                  <span>Post-Study Work Permit (PSW)</span>
                </div>
                <p className="text-sm font-black text-slate-900">{destination.postStudyWorkVisa}</p>
                <p className="text-[11px] text-slate-600">Allows full-time work with local employers post graduation.</p>
              </div>
            </div>
          )}

          {activeSubTab === 'universities' && (
            <div className="space-y-2.5">
              <p className="text-[11px] text-slate-600 font-medium">
                Trans Globe Indore has official affiliations and direct fast-track application pathways with institutions across {destination.name}:
              </p>
              <div className="space-y-2">
                {destination.topUniversities.map((uni, i) => (
                  <div key={i} className="p-3 rounded-xl border border-slate-200 bg-white hover:border-red-300 transition flex items-center justify-between">
                    <div className="flex items-center space-x-2.5">
                      <div className="w-7 h-7 rounded-lg bg-red-50 text-red-600 font-bold flex items-center justify-center text-xs">
                        {i + 1}
                      </div>
                      <span className="font-bold text-slate-900 text-xs">{uni}</span>
                    </div>
                    <span className="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-full">
                      Official Partner
                    </span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeSubTab === 'visa' && (
            <div className="space-y-3">
              <div className="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-200 space-y-2">
                <div className="flex items-center space-x-2 text-blue-900 font-bold">
                  <Award className="w-4 h-4 text-blue-600" />
                  <span>Permanent Residency & Settlement</span>
                </div>
                <p className="text-xs text-slate-700 leading-relaxed font-normal">
                  {destination.prPathway}
                </p>
              </div>

              <div className="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <h5 className="font-bold text-slate-900">Trans Globe Indore Visa Guarantee</h5>
                <p className="text-[11px] text-slate-600 leading-relaxed">
                  Our network maintains a <strong>98.7% visa success rate</strong>. Our specialists conduct multi-level document verification, funds assessment, GTE / genuine student justification, and mock embassy interviews.
                </p>
              </div>
            </div>
          )}
        </div>

        {/* Modal Sticky Bottom CTA */}
        <div className="p-3.5 bg-slate-50 border-t border-slate-200 flex items-center space-x-2">
          <button
            onClick={() => {
              onClose();
              onBookConsultation(destination.name);
            }}
            className="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md"
          >
            <span>Plan {destination.name} Application</span>
          </button>
        </div>
      </div>
    </div>
  );
};
