import React, { useState } from 'react';
import { 
  Compass, 
  Clock, 
  CheckCircle2, 
  AlertTriangle, 
  FileText, 
  Lightbulb, 
  ArrowRight, 
  ArrowLeft, 
  ShieldCheck, 
  Briefcase, 
  Coins, 
  Sparkles,
  ChevronRight,
  Plane,
  Building,
  Check
} from 'lucide-react';
import { VISA_ROADMAP_DATA, CountryVisaRoadmap, VisaRoadmapStep } from '../../data/visaRoadmapData';

interface VisaProcessRoadmapProps {
  initialCountry?: string;
  onOpenBooking: (country?: string) => void;
}

export const VisaProcessRoadmap: React.FC<VisaProcessRoadmapProps> = ({
  initialCountry = 'Australia',
  onOpenBooking,
}) => {
  const availableCountries = Object.keys(VISA_ROADMAP_DATA);
  const [selectedCountryKey, setSelectedCountryKey] = useState<string>(
    VISA_ROADMAP_DATA[initialCountry] ? initialCountry : 'Australia'
  );
  const [activeStepIndex, setActiveStepIndex] = useState<number>(0);
  const [currentStageSimulated, setCurrentStageSimulated] = useState<number>(1);

  const currentCountryData: CountryVisaRoadmap = VISA_ROADMAP_DATA[selectedCountryKey] || VISA_ROADMAP_DATA['Australia'];
  const activeStep: VisaRoadmapStep = currentCountryData.steps[activeStepIndex] || currentCountryData.steps[0];

  const handleCountryChange = (country: string) => {
    setSelectedCountryKey(country);
    setActiveStepIndex(0);
    setCurrentStageSimulated(1);
  };

  // Remaining steps calculation
  const totalSteps = currentCountryData.steps.length;
  const completedCount = Math.max(0, currentStageSimulated - 1);
  const remainingCount = totalSteps - completedCount;

  return (
    <div className="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div className="space-y-0.5">
          <div className="flex items-center space-x-2">
            <div className="w-7 h-7 rounded-xl bg-red-50 text-red-600 flex items-center justify-center border border-red-200 shadow-2xs">
              <Compass className="w-4 h-4" />
            </div>
            <h3 className="font-black text-sm text-slate-900 tracking-tight">
              Interactive Visa Process Roadmap
            </h3>
          </div>
          <p className="text-[11px] text-slate-500">
            Step-by-step consular milestones, document matrix & insider tips from Indore visa desk
          </p>
        </div>

        <div className="flex items-center space-x-1.5 self-start sm:self-auto">
          <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
            <ShieldCheck className="w-3 h-3 text-emerald-600" />
            {currentCountryData.transGlobeSuccessRate} Visa Success
          </span>
        </div>
      </div>

      {/* Country Switcher Tabs */}
      <div className="flex items-center space-x-1.5 overflow-x-auto pb-1 no-scrollbar">
        {availableCountries.map((cKey) => {
          const item = VISA_ROADMAP_DATA[cKey];
          const isSelected = selectedCountryKey === cKey;
          return (
            <button
              key={cKey}
              onClick={() => handleCountryChange(cKey)}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 cursor-pointer ${
                isSelected
                  ? 'bg-slate-900 text-white shadow-xs'
                  : 'bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100'
              }`}
            >
              <span>{item.flag}</span>
              <span>{item.country}</span>
            </button>
          );
        })}
      </div>

      {/* Country Visa Overview Banner */}
      <div className="p-3.5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white space-y-2.5 shadow-md">
        <div className="flex items-start justify-between">
          <div>
            <div className="flex items-center space-x-2">
              <span className="text-xl">{currentCountryData.flag}</span>
              <h4 className="font-extrabold text-sm text-white">
                {currentCountryData.visaName}
              </h4>
            </div>
            <p className="text-[11px] text-slate-300 mt-1 leading-relaxed">
              {currentCountryData.overview}
            </p>
          </div>
        </div>

        {/* Highlight Grid */}
        <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 border-t border-slate-700/60 text-[11px]">
          <div className="bg-slate-800/80 p-2 rounded-xl border border-slate-700">
            <span className="text-[10px] text-slate-400 font-semibold block flex items-center gap-1">
              <Clock className="w-3 h-3 text-amber-400" />
              Turnaround
            </span>
            <span className="font-bold text-slate-200 mt-0.5 block">
              {currentCountryData.processingTime}
            </span>
          </div>

          <div className="bg-slate-800/80 p-2 rounded-xl border border-slate-700">
            <span className="text-[10px] text-slate-400 font-semibold block flex items-center gap-1">
              <Briefcase className="w-3 h-3 text-emerald-400" />
              Work Rights
            </span>
            <span className="font-bold text-slate-200 mt-0.5 block truncate" title={currentCountryData.workRights}>
              {currentCountryData.workRights}
            </span>
          </div>

          <div className="col-span-2 sm:col-span-1 bg-slate-800/80 p-2 rounded-xl border border-slate-700">
            <span className="text-[10px] text-slate-400 font-semibold block flex items-center gap-1">
              <Coins className="w-3 h-3 text-rose-400" />
              Funds Key
            </span>
            <span className="font-bold text-slate-200 mt-0.5 block truncate" title={currentCountryData.fundsRequirement}>
              {currentCountryData.fundsRequirement}
            </span>
          </div>
        </div>
      </div>

      {/* Visual Step Stepper / Milestone Rail */}
      <div className="space-y-2">
        <div className="flex items-center justify-between text-xs">
          <span className="font-bold text-slate-800 flex items-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-red-600" />
            Milestones ({activeStepIndex + 1} of {totalSteps})
          </span>
          <span className="text-[11px] text-slate-500">
            Tap any milestone to view checklist
          </span>
        </div>

        {/* Milestone Rail */}
        <div className="relative flex items-center justify-between px-2 pt-2 pb-1">
          {/* Background Rail Line */}
          <div className="absolute left-6 right-6 top-5 h-1 bg-slate-200 rounded-full z-0" />
          {/* Active progress rail */}
          <div 
            className="absolute left-6 top-5 h-1 bg-gradient-to-r from-red-600 to-amber-500 rounded-full z-0 transition-all duration-300"
            style={{ width: `${(activeStepIndex / (totalSteps - 1)) * 88}%` }}
          />

          {currentCountryData.steps.map((s, index) => {
            const isCompleted = index < activeStepIndex;
            const isCurrent = index === activeStepIndex;

            return (
              <button
                key={s.stepNumber}
                onClick={() => setActiveStepIndex(index)}
                className="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none"
                title={s.title}
              >
                <div
                  className={`w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all ${
                    isCurrent
                      ? 'bg-red-600 text-white ring-4 ring-red-100 scale-110 shadow-md'
                      : isCompleted
                      ? 'bg-slate-900 text-amber-400 shadow-xs'
                      : 'bg-white text-slate-500 border-2 border-slate-300 hover:border-slate-400'
                  }`}
                >
                  {isCompleted ? (
                    <Check className="w-3.5 h-3.5" />
                  ) : (
                    <span>{s.stepNumber}</span>
                  )}
                </div>
                <span 
                  className={`text-[9px] font-bold mt-1 text-center max-w-[50px] truncate ${
                    isCurrent ? 'text-red-600 font-black' : 'text-slate-500'
                  }`}
                >
                  {s.title.split(' ')[0]}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* Active Step Spotlight Card */}
      <div className="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative overflow-hidden transition-all duration-200">
        {activeStep.isCrucial && (
          <div className="absolute top-0 right-0 bg-red-600 text-white text-[9px] font-black uppercase px-2.5 py-0.5 rounded-bl-xl tracking-wider flex items-center gap-1 shadow-xs">
            <AlertTriangle className="w-2.5 h-2.5 text-amber-300" />
            Crucial Decision Stage
          </div>
        )}

        {/* Step Header */}
        <div className="flex items-start justify-between pr-14">
          <div>
            <span className="text-[10px] font-extrabold text-red-600 uppercase tracking-wider block">
              Step 0{activeStep.stepNumber} Milestone
            </span>
            <h4 className="font-extrabold text-sm text-slate-900 mt-0.5 leading-snug">
              {activeStep.title}
            </h4>
            <p className="text-[11px] font-medium text-slate-500 mt-0.5">
              {activeStep.shortDesc}
            </p>
          </div>
        </div>

        {/* Duration badge */}
        <div className="inline-flex items-center space-x-1.5 bg-white border border-slate-200 px-2.5 py-1 rounded-lg text-[11px] text-slate-700 font-semibold shadow-2xs">
          <Clock className="w-3.5 h-3.5 text-amber-500" />
          <span>Typical Duration: <strong>{activeStep.duration}</strong></span>
        </div>

        {/* Comprehensive Details */}
        <p className="text-xs text-slate-700 leading-relaxed bg-white p-3 rounded-xl border border-slate-200/80">
          {activeStep.details}
        </p>

        {/* Required Documents Matrix */}
        <div className="space-y-1.5 pt-1">
          <span className="text-[11px] font-bold text-slate-800 flex items-center gap-1">
            <FileText className="w-3.5 h-3.5 text-slate-600" />
            Essential Documents for this step:
          </span>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
            {activeStep.requiredDocuments.map((doc, i) => (
              <div
                key={i}
                className="text-[11px] font-medium bg-white text-slate-800 px-2.5 py-1.5 rounded-xl border border-slate-200 flex items-center space-x-1.5 shadow-2xs"
              >
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                <span className="truncate">{doc}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Trans Globe Indore Pro Tip Callout */}
        <div className="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 space-y-1">
          <div className="flex items-center space-x-1.5 text-xs font-extrabold text-amber-900">
            <Lightbulb className="w-3.5 h-3.5 text-amber-600 shrink-0" />
            <span>Trans Globe Indore Insider Advice</span>
          </div>
          <p className="text-[11px] text-amber-800 leading-relaxed pl-5">
            {activeStep.indoreCounsellorTip}
          </p>
        </div>

        {/* Stepper Navigation Buttons */}
        <div className="flex items-center justify-between pt-2 border-t border-slate-200">
          <button
            onClick={() => setActiveStepIndex((prev) => Math.max(0, prev - 1))}
            disabled={activeStepIndex === 0}
            className="px-3 py-1.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 disabled:opacity-40 text-xs font-bold transition flex items-center space-x-1 cursor-pointer"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Previous</span>
          </button>

          <button
            onClick={() => setActiveStepIndex((prev) => Math.min(totalSteps - 1, prev + 1))}
            disabled={activeStepIndex === totalSteps - 1}
            className="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white disabled:opacity-40 text-xs font-bold transition flex items-center space-x-1 shadow-xs cursor-pointer"
          >
            <span>Next Step</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      {/* Interactive Stage Tracker / Readiness Simulator */}
      <div className="p-3.5 rounded-2xl bg-red-50/60 border border-red-200/80 space-y-3">
        <div className="flex items-center justify-between">
          <div>
            <h4 className="font-extrabold text-xs text-slate-900 flex items-center gap-1.5">
              <Plane className="w-3.5 h-3.5 text-red-600" />
              Where are you right now in this roadmap?
            </h4>
            <p className="text-[10px] text-slate-500">
              Select your current progress to view remaining turnaround
            </p>
          </div>
          <span className="text-[11px] font-black text-red-700 bg-red-100 px-2 py-0.5 rounded-md">
            Step {currentStageSimulated} of {totalSteps}
          </span>
        </div>

        {/* Quick Stage Selector Chips */}
        <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar pb-1">
          {currentCountryData.steps.map((st) => (
            <button
              key={st.stepNumber}
              onClick={() => {
                setCurrentStageSimulated(st.stepNumber);
                setActiveStepIndex(st.stepNumber - 1);
              }}
              className={`px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer ${
                currentStageSimulated === st.stepNumber
                  ? 'bg-red-600 text-white shadow-xs'
                  : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'
              }`}
            >
              Step {st.stepNumber}: {st.title.split(' ')[0]}
            </button>
          ))}
        </div>

        {/* Readiness Outcome Box */}
        <div className="p-3 rounded-xl bg-white border border-red-100 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
          <div className="space-y-0.5">
            <span className="text-[11px] font-bold text-slate-900 block">
              {remainingCount === 0
                ? '🎉 Ready for Departure & Welcome Briefing!'
                : `${remainingCount} Milestone${remainingCount > 1 ? 's' : ''} Remaining to Visa Grant`}
            </span>
            <span className="text-[10px] text-slate-500 block">
              Estimated completion: <strong>{currentCountryData.processingTime}</strong> with zero-error Trans Globe filing.
            </span>
          </div>

          <button
            onClick={() => onOpenBooking(currentCountryData.country)}
            className="bg-red-600 hover:bg-red-700 text-white font-black text-xs px-3.5 py-2 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-xs shrink-0 cursor-pointer active:scale-95"
          >
            <span>Get {currentCountryData.country} Visa Support</span>
            <ChevronRight className="w-3.5 h-3.5 text-amber-300" />
          </button>
        </div>
      </div>
    </div>
  );
};
