import React, { useState } from 'react';
import { 
  X, 
  ArrowLeftRight, 
  Sparkles, 
  DollarSign, 
  Award, 
  GraduationCap, 
  CheckCircle2, 
  ShieldCheck, 
  Clock, 
  BookOpen, 
  FileText, 
  Languages, 
  Calendar,
  Building2,
  ChevronDown,
  Check,
  Phone,
  MessageCircle,
  TrendingUp,
  MapPin
} from 'lucide-react';
import { UniversityComparisonData, GLOBAL_UNIVERSITIES } from '../../data/liveCatalog';

interface CompareUniversitiesModalProps {
  isOpen: boolean;
  onClose: () => void;
  university1: UniversityComparisonData | null;
  university2: UniversityComparisonData | null;
  onSelectUniversity1: (uni: UniversityComparisonData) => void;
  onSelectUniversity2: (uni: UniversityComparisonData) => void;
  onBookConsultation: (universityName: string) => void;
}

export const CompareUniversitiesModal: React.FC<CompareUniversitiesModalProps> = ({
  isOpen,
  onClose,
  university1,
  university2,
  onSelectUniversity1,
  onSelectUniversity2,
  onBookConsultation,
}) => {
  const [highlightDifferences, setHighlightDifferences] = useState(false);

  if (!isOpen) return null;

  // Fallback defaults if one or both are null
  const uni1 = university1 || GLOBAL_UNIVERSITIES[0];
  const uni2 = university2 || GLOBAL_UNIVERSITIES[1] || GLOBAL_UNIVERSITIES[0];

  if (!uni1 || !uni2) return <div role="dialog" className="fixed inset-0 z-50 bg-white p-6"><p>No universities are available for comparison.</p><button onClick={onClose}>Close</button></div>;

  const handleSwap = () => {
    onSelectUniversity1(uni2);
    onSelectUniversity2(uni1);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-stone-950/70 backdrop-blur-xs p-0 sm:p-4">
      <div 
        className="bg-white w-full max-w-4xl max-h-[92vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-200 border border-stone-200/80"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Sticky Modal Top Header */}
        <header className="sticky top-0 z-20 bg-white/95 backdrop-blur-md px-5 py-3.5 border-b border-stone-200/80 flex items-center justify-between shadow-2xs">
          <div className="flex items-center space-x-2.5">
            <div className="w-8 h-8 rounded-full bg-rose-50 text-rose-700 flex items-center justify-center font-bold">
              <ArrowLeftRight className="w-4 h-4 text-rose-600" />
            </div>
            <div>
              <div className="flex items-center space-x-2">
                <h3 className="font-extrabold text-sm text-stone-900 tracking-tight">
                  Side-by-Side University Comparison
                </h3>
                <span className="text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70 px-2 py-0.5 rounded-full">
                  2026 Admissions
                </span>
              </div>
              <p className="text-[11px] text-stone-500 font-normal">
                Compare fees, acceptance rate, and testing requirements for Indore students
              </p>
            </div>
          </div>

          <div className="flex items-center space-x-2">
            <button
              onClick={handleSwap}
              className="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
              title="Swap columns"
            >
              <ArrowLeftRight className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">Swap</span>
            </button>

            <button
              onClick={onClose}
              className="p-1.5 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 hover:text-stone-900 transition cursor-pointer"
              aria-label="Close Comparison"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </header>

        {/* Scrollable Comparison Content */}
        <div className="flex-1 overflow-y-auto">
          {/* Universities Selector Header (Sticky on Scroll) */}
          <div className="sticky top-0 z-10 bg-stone-50/95 backdrop-blur-md border-b border-stone-200/80 px-4 py-3 shadow-2xs">
            <div className="grid grid-cols-2 gap-3 sm:gap-6">
              {/* University 1 Column Picker */}
              <div className="bg-white rounded-2xl p-3 border border-stone-200/80 shadow-2xs space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700">
                    Option A
                  </span>
                  <span className="text-sm">{uni1.flagEmoji}</span>
                </div>

                <div className="relative">
                  <select
                    value={uni1.id}
                    onChange={(e) => {
                      const found = GLOBAL_UNIVERSITIES.find((u) => u.id === e.target.value);
                      if (found) onSelectUniversity1(found);
                    }}
                    className="w-full text-xs font-bold text-stone-900 bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-2 pr-7 appearance-none focus:outline-none focus:border-rose-500 cursor-pointer"
                  >
                    {GLOBAL_UNIVERSITIES.map((u) => (
                      <option key={u.id} value={u.id}>
                        {u.name} ({u.countryCode})
                      </option>
                    ))}
                  </select>
                  <ChevronDown className="w-3.5 h-3.5 text-stone-400 absolute right-2.5 top-3 pointer-events-none" />
                </div>

                <div className="text-[11px] text-stone-500 flex items-center gap-1 font-medium truncate">
                  <MapPin className="w-3 h-3 text-stone-400 shrink-0" />
                  <span className="truncate">{uni1.city}, {uni1.country}</span>
                </div>

                <button
                  onClick={() => onBookConsultation(uni1.name)}
                  className="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-1.5 rounded-xl text-[11px] transition shadow-xs cursor-pointer flex items-center justify-center gap-1"
                >
                  <Sparkles className="w-3 h-3" />
                  <span>Apply with Trans Globe</span>
                </button>
              </div>

              {/* University 2 Column Picker */}
              <div className="bg-white rounded-2xl p-3 border border-stone-200/80 shadow-2xs space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                    Option B
                  </span>
                  <span className="text-sm">{uni2.flagEmoji}</span>
                </div>

                <div className="relative">
                  <select
                    value={uni2.id}
                    onChange={(e) => {
                      const found = GLOBAL_UNIVERSITIES.find((u) => u.id === e.target.value);
                      if (found) onSelectUniversity2(found);
                    }}
                    className="w-full text-xs font-bold text-stone-900 bg-stone-50 border border-stone-200 rounded-xl px-2.5 py-2 pr-7 appearance-none focus:outline-none focus:border-emerald-500 cursor-pointer"
                  >
                    {GLOBAL_UNIVERSITIES.map((u) => (
                      <option key={u.id} value={u.id}>
                        {u.name} ({u.countryCode})
                      </option>
                    ))}
                  </select>
                  <ChevronDown className="w-3.5 h-3.5 text-stone-400 absolute right-2.5 top-3 pointer-events-none" />
                </div>

                <div className="text-[11px] text-stone-500 flex items-center gap-1 font-medium truncate">
                  <MapPin className="w-3 h-3 text-stone-400 shrink-0" />
                  <span className="truncate">{uni2.city}, {uni2.country}</span>
                </div>

                <button
                  onClick={() => onBookConsultation(uni2.name)}
                  className="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-1.5 rounded-xl text-[11px] transition shadow-xs cursor-pointer flex items-center justify-center gap-1"
                >
                  <Sparkles className="w-3 h-3" />
                  <span>Apply with Trans Globe</span>
                </button>
              </div>
            </div>
          </div>

          {/* Side-by-Side Comparison Tables */}
          <div className="p-4 space-y-6">
            {/* 1. KEY DECISION METRICS (Acceptance Rate, Fees & Requirements) */}
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-stone-900 font-extrabold text-xs">
                <GraduationCap className="w-4 h-4 text-rose-600" />
                <span>Primary Decision Metrics</span>
              </div>

              <div className="bg-white rounded-3xl border border-stone-200/80 overflow-hidden shadow-xs divide-y divide-stone-100">
                {/* Metric 1: Global QS Rank */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    World Ranking (QS 2026)
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="font-extrabold text-stone-900 leading-snug">
                      <span className="inline-block px-2 py-0.5 bg-rose-50 text-rose-700 rounded-lg border border-rose-200/60 font-mono text-[11px] mr-1">
                        {uni1.globalRankNumber == null ? 'Not provided' : `#${uni1.globalRankNumber}`}
                      </span>
                      <span>{uni1.globalRank}</span>
                    </div>
                    <div className="font-extrabold text-stone-900 leading-snug">
                      <span className="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200/60 font-mono text-[11px] mr-1">
                        {uni2.globalRankNumber == null ? 'Not provided' : `#${uni2.globalRankNumber}`}
                      </span>
                      <span>{uni2.globalRank}</span>
                    </div>
                  </div>
                </div>

                {/* Metric 2: Acceptance Rate */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Acceptance Rate & Competitiveness
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="space-y-1">
                      <span className="text-sm font-black text-rose-700 block">
                        {uni1.acceptanceRatePercentage == null ? 'Not provided' : `${uni1.acceptanceRatePercentage}%`}
                      </span>
                      <p className="text-[11px] text-stone-600 leading-relaxed">
                        {uni1.acceptanceRate}
                      </p>
                    </div>
                    <div className="space-y-1">
                      <span className="text-sm font-black text-emerald-700 block">
                        {uni2.acceptanceRatePercentage == null ? 'Not provided' : `${uni2.acceptanceRatePercentage}%`}
                      </span>
                      <p className="text-[11px] text-stone-600 leading-relaxed">
                        {uni2.acceptanceRate}
                      </p>
                    </div>
                  </div>
                </div>

                {/* Metric 3: Annual Tuition Fees */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Annual Tuition Fees (Indian Student Approx.)
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="space-y-0.5">
                      <span className="font-extrabold text-stone-900 text-xs block">
                        {uni1.annualTuition}
                      </span>
                      <span className="text-[11px] font-semibold text-rose-700 block">
                        Approx. {uni1.annualTuitionINR}
                      </span>
                    </div>
                    <div className="space-y-0.5">
                      <span className="font-extrabold text-stone-900 text-xs block">
                        {uni2.annualTuition}
                      </span>
                      <span className="text-[11px] font-semibold text-emerald-700 block">
                        Approx. {uni2.annualTuitionINR}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Metric 4: Living Expenses */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Estimated Annual Living Expenses
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div>
                      <span className="font-bold text-stone-800 block">{uni1.annualLivingCost}</span>
                      <span className="text-[11px] text-stone-500 font-medium block">{uni1.annualLivingCostINR}</span>
                    </div>
                    <div>
                      <span className="font-bold text-stone-800 block">{uni2.annualLivingCost}</span>
                      <span className="text-[11px] text-stone-500 font-medium block">{uni2.annualLivingCostINR}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* 2. ADMISSION & TESTING REQUIREMENTS */}
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-stone-900 font-extrabold text-xs">
                <Languages className="w-4 h-4 text-rose-600" />
                <span>Eligibility & Testing Requirements</span>
              </div>

              <div className="bg-white rounded-3xl border border-stone-200/80 overflow-hidden shadow-xs divide-y divide-stone-100">
                {/* IELTS Requirement */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">
                    IELTS Academic Cut-off
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <span className="font-bold text-stone-800">{uni1.ieltsRequirement}</span>
                    <span className="font-bold text-stone-800">{uni2.ieltsRequirement}</span>
                  </div>
                </div>

                {/* PTE Requirement */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">
                    PTE Academic Cut-off
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <span className="font-medium text-stone-700">{uni1.pteRequirement}</span>
                    <span className="font-medium text-stone-700">{uni2.pteRequirement}</span>
                  </div>
                </div>

                {/* TOEFL Requirement */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">
                    TOEFL iBT Cut-off
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <span className="font-medium text-stone-700">{uni1.toeflRequirement}</span>
                    <span className="font-medium text-stone-700">{uni2.toeflRequirement}</span>
                  </div>
                </div>

                {/* GRE / GMAT Requirement */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">
                    GRE / GMAT Policy
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <span className="text-stone-700 leading-relaxed font-normal">{uni1.greGmatRequirement}</span>
                    <span className="text-stone-700 leading-relaxed font-normal">{uni2.greGmatRequirement}</span>
                  </div>
                </div>

                {/* Minimum Academic GPA */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">
                    Bachelor’s Marks / GPA Cut-off
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <span className="font-bold text-stone-900">{uni1.minGpaPercentage}</span>
                    <span className="font-bold text-stone-900">{uni2.minGpaPercentage}</span>
                  </div>
                </div>
              </div>
            </div>

            {/* 3. SCHOLARSHIPS & FINANCIAL AID */}
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-stone-900 font-extrabold text-xs">
                <Award className="w-4 h-4 text-amber-600" />
                <span>Scholarships & Financial Support</span>
              </div>

              <div className="bg-white rounded-3xl border border-stone-200/80 overflow-hidden shadow-xs divide-y divide-stone-100">
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Available Merit Scholarships
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="space-y-1">
                      <span className="font-extrabold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200 inline-block text-[11px]">
                        {uni1.maxScholarshipAmount}
                      </span>
                      <p className="text-[11px] text-stone-600 leading-relaxed">
                        {uni1.scholarships}
                      </p>
                    </div>
                    <div className="space-y-1">
                      <span className="font-extrabold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200 inline-block text-[11px]">
                        {uni2.maxScholarshipAmount}
                      </span>
                      <p className="text-[11px] text-stone-600 leading-relaxed">
                        {uni2.scholarships}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* 4. POST-STUDY WORK VISA & CAREER OUTCOMES */}
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-stone-900 font-extrabold text-xs">
                <Clock className="w-4 h-4 text-emerald-600" />
                <span>Post-Study Work Rights & Stay-Back Visa</span>
              </div>

              <div className="bg-white rounded-3xl border border-stone-200/80 overflow-hidden shadow-xs divide-y divide-stone-100">
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Graduate Work Visa (PSW)
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="font-bold text-stone-900 leading-relaxed">
                      <span className="block text-emerald-700 font-extrabold mb-0.5">
                        {uni1.postStudyWorkVisa.split('(')[0]}
                      </span>
                      <span className="text-[11px] text-stone-500 font-normal">
                        {uni1.postStudyWorkVisa}
                      </span>
                    </div>
                    <div className="font-bold text-stone-900 leading-relaxed">
                      <span className="block text-emerald-700 font-extrabold mb-0.5">
                        {uni2.postStudyWorkVisa.split('(')[0]}
                      </span>
                      <span className="text-[11px] text-stone-500 font-normal">
                        {uni2.postStudyWorkVisa}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Popular Programs */}
                <div className="p-3.5 hover:bg-stone-50/50 transition">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Popular High-Employability Programs
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <div className="flex flex-wrap gap-1">
                      {uni1.popularPrograms.map((p, i) => (
                        <span key={i} className="text-[10px] bg-stone-100 text-stone-700 px-2 py-0.5 rounded-md font-medium">
                          {p}
                        </span>
                      ))}
                    </div>
                    <div className="flex flex-wrap gap-1">
                      {uni2.popularPrograms.map((p, i) => (
                        <span key={i} className="text-[10px] bg-stone-100 text-stone-700 px-2 py-0.5 rounded-md font-medium">
                          {p}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Trans Globe Indore Advantage */}
                <div className="p-3.5 bg-stone-50/80">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                    Trans Globe Indore Advantage
                  </span>
                  <div className="grid grid-cols-2 gap-4 text-xs">
                    <p className="text-[11px] text-stone-600 leading-relaxed font-normal">
                      {uni1.transGlobeHighlights}
                    </p>
                    <p className="text-[11px] text-stone-600 leading-relaxed font-normal">
                      {uni2.transGlobeHighlights}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Modal Sticky Footer CTAs */}
        <footer className="sticky bottom-0 z-20 bg-white/95 backdrop-blur-md px-4 py-3 border-t border-stone-200/80 flex items-center justify-between gap-3 text-xs">
          <div className="text-[11px] text-stone-500 hidden sm:block">
            📍 Trans Globe Indore provides 100% free application processing & visa filing.
          </div>

          <div className="flex items-center space-x-2 w-full sm:w-auto">
            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent(`Hi Trans Globe Indore! I am comparing ${uni1.name} and ${uni2.name}. Can you help evaluate my profile?`)}`}
              target="_blank"
              rel="noreferrer"
              className="flex-1 sm:flex-initial bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 font-bold px-3.5 py-2.5 rounded-2xl flex items-center justify-center gap-1.5 border border-emerald-800/50 transition cursor-pointer"
            >
              <MessageCircle className="w-3.5 h-3.5 text-emerald-400" />
              <span>WhatsApp Profile Review</span>
            </a>

            <button
              onClick={() => {
                onClose();
                onBookConsultation(`${uni1.name} vs ${uni2.name}`);
              }}
              className="flex-1 sm:flex-initial bg-rose-600 hover:bg-rose-700 text-white font-bold px-4 py-2.5 rounded-2xl flex items-center justify-center gap-1.5 shadow-md transition cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Book Counselling</span>
            </button>
          </div>
        </footer>
      </div>
    </div>
  );
};
