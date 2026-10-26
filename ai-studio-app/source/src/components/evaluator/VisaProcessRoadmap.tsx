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
  Check,
  ClipboardList,
  CheckSquare,
  Square,
  Filter,
  Copy,
  RotateCcw,
  ChevronDown,
  ChevronUp,
  Share2,
  FileCheck2
} from 'lucide-react';
import { 
  VISA_ROADMAP_DATA, 
  CountryVisaRoadmap, 
  VisaRoadmapStep,
  DestinationRequiredDocument,
  DocumentCategory
} from '../../data/liveCatalog';

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

  // Toggle for 'Required Documents' Checklist
  const [showChecklist, setShowChecklist] = useState<boolean>(false);
  const [checklistFilterCategory, setChecklistFilterCategory] = useState<string>('All');
  const [checklistStatusFilter, setChecklistStatusFilter] = useState<'all' | 'pending' | 'completed'>('all');
  const [copiedNotification, setCopiedNotification] = useState<boolean>(false);

  // Document checked state keyed by [countryKey][docId]
  const [checkedDocs, setCheckedDocs] = useState<Record<string, Record<string, boolean>>>({
    Australia: {
      aus_doc_passport: true,
      aus_doc_academic: true,
      aus_doc_english: true,
    },
    'United Kingdom': {
      uk_doc_passport: true,
      uk_doc_academic: true,
    },
    Germany: {
      de_doc_passport: true,
      de_doc_academics: true,
    },
    'United States': {
      us_doc_passport: true,
      us_doc_transcripts: true,
      us_doc_standardized: true,
    },
    Canada: {
      ca_doc_passport: true,
      ca_doc_transcripts: true,
    },
  });

  const currentCountryData: CountryVisaRoadmap = 
    VISA_ROADMAP_DATA[selectedCountryKey] || VISA_ROADMAP_DATA['Australia'];
  const activeStep: VisaRoadmapStep = 
    currentCountryData.steps[activeStepIndex] || currentCountryData.steps[0];

  const handleCountryChange = (country: string) => {
    setSelectedCountryKey(country);
    setActiveStepIndex(0);
    setCurrentStageSimulated(1);
    setChecklistFilterCategory('All');
    setChecklistStatusFilter('all');
  };

  // Toggle individual document in the checklist
  const handleToggleDocument = (docId: string) => {
    setCheckedDocs((prev) => {
      const countryDocs = prev[selectedCountryKey] || {};
      return {
        ...prev,
        [selectedCountryKey]: {
          ...countryDocs,
          [docId]: !countryDocs[docId],
        },
      };
    });
  };

  // Check all documents for current destination
  const handleSelectAllDocuments = () => {
    const allChecked: Record<string, boolean> = {};
    currentCountryData.requiredDocumentsChecklist.forEach((doc) => {
      allChecked[doc.id] = true;
    });
    setCheckedDocs((prev) => ({
      ...prev,
      [selectedCountryKey]: allChecked,
    }));
  };

  // Reset all documents for current destination
  const handleResetDocuments = () => {
    setCheckedDocs((prev) => ({
      ...prev,
      [selectedCountryKey]: {},
    }));
  };

  // Copy checklist summary to clipboard
  const handleCopyChecklist = () => {
    const countryDocs = currentCountryData.requiredDocumentsChecklist;
    const currentChecked = checkedDocs[selectedCountryKey] || {};
    
    const lines = [
      `📑 ${currentCountryData.country} (${currentCountryData.visaName}) - Required Documents Checklist`,
      `Verified via Trans Globe Indore`,
      `----------------------------------------`,
    ];

    countryDocs.forEach((doc, idx) => {
      const isDone = !!currentChecked[doc.id];
      lines.push(`${isDone ? '[X]' : '[ ]'} ${idx + 1}. ${doc.name} (${doc.isMandatory ? 'Mandatory' : 'Supporting'})`);
      lines.push(`     Category: ${doc.category}`);
      lines.push(`     Notes: ${doc.notes}`);
    });

    lines.push(`----------------------------------------`);
    lines.push(`Need document verification? Visit Trans Globe Indore: geic-study.com`);

    navigator.clipboard?.writeText(lines.join('\n'));
    setCopiedNotification(true);
    setTimeout(() => setCopiedNotification(false), 2500);
  };

  // Remaining steps calculation
  const totalSteps = currentCountryData.steps.length;
  const completedCount = Math.max(0, currentStageSimulated - 1);
  const remainingCount = totalSteps - completedCount;

  // Checklist counts & stats
  const allDestinationDocs = currentCountryData.requiredDocumentsChecklist || [];
  const currentCheckedMap = checkedDocs[selectedCountryKey] || {};
  const completedDocsCount = allDestinationDocs.filter((d) => currentCheckedMap[d.id]).length;
  const totalDocsCount = allDestinationDocs.length;
  const docsProgressPercentage = totalDocsCount > 0 ? Math.round((completedDocsCount / totalDocsCount) * 100) : 0;
  const mandatoryDocs = allDestinationDocs.filter((d) => d.isMandatory);
  const mandatoryCompletedCount = mandatoryDocs.filter((d) => currentCheckedMap[d.id]).length;

  // Filtered documents
  const filteredDocuments = allDestinationDocs.filter((doc) => {
    const matchesCategory = checklistFilterCategory === 'All' || doc.category === checklistFilterCategory;
    const isDone = !!currentCheckedMap[doc.id];
    const matchesStatus = 
      checklistStatusFilter === 'all' || 
      (checklistStatusFilter === 'completed' && isDone) || 
      (checklistStatusFilter === 'pending' && !isDone);
    return matchesCategory && matchesStatus;
  });

  const categoriesList: DocumentCategory[] = [
    'Academic & Test Scores',
    'Financial & Sponsorship',
    'Identity & Civil',
    'Health & Insurance',
    'Consular & Legal'
  ];

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
          const countryDocs = item.requiredDocumentsChecklist || [];
          const countryChecked = checkedDocs[cKey] || {};
          const countryDoneCount = countryDocs.filter(d => countryChecked[d.id]).length;

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
              {countryDocs.length > 0 && (
                <span 
                  className={`text-[9px] px-1.5 py-0.2 rounded-full font-semibold ${
                    isSelected ? 'bg-slate-700 text-slate-200' : 'bg-slate-200 text-slate-600'
                  }`}
                >
                  {countryDoneCount}/{countryDocs.length}
                </span>
              )}
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

      {/* Primary Feature: Required Documents Checklist Toggle Section */}
      <div className="rounded-2xl border border-red-200/90 bg-gradient-to-r from-red-50/90 via-amber-50/40 to-red-50/80 p-3 sm:p-3.5 shadow-2xs space-y-2.5 transition-all">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
          <div className="flex items-center space-x-2.5">
            <div className="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center shadow-xs shrink-0">
              <ClipboardList className="w-5 h-5" />
            </div>
            <div>
              <div className="flex items-center space-x-2 flex-wrap">
                <span className="font-black text-xs sm:text-sm text-slate-900">
                  {currentCountryData.country} Required Documents Checklist
                </span>
                <span className="text-[10px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200/80">
                  {completedDocsCount}/{totalDocsCount} Ready ({docsProgressPercentage}%)
                </span>
              </div>
              <p className="text-[11px] text-slate-600">
                Interactive verification checklist for {currentCountryData.visaName}
              </p>
            </div>
          </div>

          {/* Prominent Toggle Button */}
          <button
            onClick={() => setShowChecklist((prev) => !prev)}
            className={`px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer active:scale-95 shrink-0 ${
              showChecklist
                ? 'bg-slate-900 hover:bg-slate-800 text-white'
                : 'bg-red-600 hover:bg-red-700 text-white'
            }`}
          >
            <FileCheck2 className="w-3.5 h-3.5 text-amber-300" />
            <span>{showChecklist ? 'Hide Documents Checklist' : 'Toggle Required Documents'}</span>
            {showChecklist ? (
              <ChevronUp className="w-4 h-4 ml-0.5" />
            ) : (
              <ChevronDown className="w-4 h-4 ml-0.5" />
            )}
          </button>
        </div>

        {/* Small progress meter always visible on toggle banner */}
        <div className="space-y-1">
          <div className="flex items-center justify-between text-[10px] font-bold text-slate-600">
            <span>
              {completedDocsCount === totalDocsCount
                ? '🎉 All required documents ready!'
                : `${mandatoryCompletedCount} of ${mandatoryDocs.length} Mandatory Documents Ready`}
            </span>
            <span>{docsProgressPercentage}% Complete</span>
          </div>
          <div className="w-full bg-slate-200/90 h-1.5 rounded-full overflow-hidden">
            <div
              className={`h-full transition-all duration-500 rounded-full ${
                docsProgressPercentage === 100
                  ? 'bg-emerald-500'
                  : docsProgressPercentage >= 50
                  ? 'bg-amber-500'
                  : 'bg-red-600'
              }`}
              style={{ width: `${docsProgressPercentage}%` }}
            />
          </div>
        </div>
      </div>

      {/* Expanded 'Required Documents' Checklist View */}
      {showChecklist && (
        <div className="rounded-3xl border-2 border-red-500/20 bg-slate-50/70 p-3.5 sm:p-4.5 space-y-3.5 transition-all duration-300">
          {/* Checklist Top Controls */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2 border-b border-slate-200">
            <div>
              <h4 className="font-black text-xs text-slate-900 flex items-center gap-1.5">
                <span>{currentCountryData.flag}</span>
                <span>{currentCountryData.country} Visa Document Checklist</span>
              </h4>
              <p className="text-[10px] text-slate-500">
                Click any item below to check off documents as you prepare them.
              </p>
            </div>

            {/* Quick Actions */}
            <div className="flex items-center space-x-1.5 self-start sm:self-auto flex-wrap">
              <button
                onClick={handleSelectAllDocuments}
                className="text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-700 px-2 py-1 rounded-lg border border-slate-200 transition cursor-pointer"
              >
                Check All
              </button>
              <button
                onClick={handleResetDocuments}
                className="text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-600 px-2 py-1 rounded-lg border border-slate-200 transition flex items-center gap-1 cursor-pointer"
              >
                <RotateCcw className="w-2.5 h-2.5" />
                <span>Reset</span>
              </button>
              <button
                onClick={handleCopyChecklist}
                className="text-[10px] font-bold bg-slate-900 hover:bg-slate-800 text-white px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer shadow-2xs"
              >
                <Copy className="w-2.5 h-2.5 text-amber-300" />
                <span>{copiedNotification ? 'Copied!' : 'Copy List'}</span>
              </button>
            </div>
          </div>

          {/* Category Filter Chips */}
          <div className="space-y-1.5">
            <div className="flex items-center justify-between text-[10px] text-slate-500 font-bold">
              <span>Filter by Category:</span>
              <div className="flex items-center space-x-1">
                {(['all', 'pending', 'completed'] as const).map((st) => (
                  <button
                    key={st}
                    onClick={() => setChecklistStatusFilter(st)}
                    className={`px-2 py-0.5 rounded-md text-[9px] uppercase tracking-wider font-extrabold transition cursor-pointer ${
                      checklistStatusFilter === st
                        ? 'bg-slate-900 text-white'
                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'
                    }`}
                  >
                    {st}
                  </button>
                ))}
              </div>
            </div>

            <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar pb-0.5">
              <button
                onClick={() => setChecklistFilterCategory('All')}
                className={`px-2.5 py-1 rounded-xl text-[10px] font-bold whitespace-nowrap transition cursor-pointer shrink-0 ${
                  checklistFilterCategory === 'All'
                    ? 'bg-red-600 text-white shadow-2xs'
                    : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'
                }`}
              >
                All Categories ({allDestinationDocs.length})
              </button>
              {categoriesList.map((cat) => {
                const countInCat = allDestinationDocs.filter((d) => d.category === cat).length;
                if (countInCat === 0) return null;
                const isCatSelected = checklistFilterCategory === cat;

                return (
                  <button
                    key={cat}
                    onClick={() => setChecklistFilterCategory(cat)}
                    className={`px-2.5 py-1 rounded-xl text-[10px] font-bold whitespace-nowrap transition cursor-pointer shrink-0 ${
                      isCatSelected
                        ? 'bg-red-600 text-white shadow-2xs'
                        : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'
                    }`}
                  >
                    {cat} ({countInCat})
                  </button>
                );
              })}
            </div>
          </div>

          {/* Document Items List */}
          <div className="space-y-2 max-h-[460px] overflow-y-auto pr-1">
            {filteredDocuments.length === 0 ? (
              <div className="text-center py-6 bg-white rounded-2xl border border-dashed border-slate-300 text-slate-500 text-xs">
                No documents match the selected filter criteria.
              </div>
            ) : (
              filteredDocuments.map((doc) => {
                const isChecked = !!currentCheckedMap[doc.id];

                return (
                  <div
                    key={doc.id}
                    onClick={() => handleToggleDocument(doc.id)}
                    className={`p-3 rounded-2xl border transition-all cursor-pointer select-none space-y-1.5 ${
                      isChecked
                        ? 'bg-emerald-50/40 border-emerald-300 shadow-2xs'
                        : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-2xs'
                    }`}
                  >
                    <div className="flex items-start justify-between gap-2.5">
                      <div className="flex items-start space-x-2.5">
                        <div
                          className={`w-5 h-5 rounded-md flex items-center justify-center shrink-0 mt-0.5 transition-colors ${
                            isChecked
                              ? 'bg-emerald-600 text-white'
                              : 'border-2 border-slate-300 bg-white text-transparent group-hover:border-slate-400'
                          }`}
                        >
                          <Check className="w-3.5 h-3.5 stroke-[3]" />
                        </div>
                        <div className="space-y-0.5">
                          <div className="flex items-center space-x-1.5 flex-wrap">
                            <span
                              className={`text-xs font-bold leading-snug ${
                                isChecked
                                  ? 'text-slate-700 line-through decoration-emerald-600/70'
                                  : 'text-slate-900'
                              }`}
                            >
                              {doc.name}
                            </span>
                            {doc.isMandatory ? (
                              <span className="text-[9px] font-black uppercase px-1.5 py-0.2 rounded-md bg-red-100 text-red-700 tracking-wide">
                                Mandatory
                              </span>
                            ) : (
                              <span className="text-[9px] font-bold px-1.5 py-0.2 rounded-md bg-slate-100 text-slate-600">
                                Supporting
                              </span>
                            )}
                          </div>
                          <span className="text-[10px] text-slate-400 font-semibold block">
                            Category: {doc.category}
                          </span>
                        </div>
                      </div>

                      <span
                        className={`text-[9px] font-bold px-2 py-0.5 rounded-full shrink-0 ${
                          isChecked
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'bg-slate-100 text-slate-600'
                        }`}
                      >
                        {isChecked ? 'Ready' : 'Pending'}
                      </span>
                    </div>

                    {/* Notes */}
                    <p className="text-[11px] text-slate-600 leading-relaxed pl-7">
                      {doc.notes}
                    </p>

                    {/* Indore Specialist Tip */}
                    {doc.indoreTip && (
                      <div className="ml-7 p-2 rounded-xl bg-amber-50/70 border border-amber-200/70 text-[10px] text-amber-900 flex items-start space-x-1.5">
                        <Lightbulb className="w-3 h-3 text-amber-600 shrink-0 mt-0.5" />
                        <span>
                          <strong>Trans Globe Indore Tip:</strong> {doc.indoreTip}
                        </span>
                      </div>
                    )}
                  </div>
                );
              })
            )}
          </div>

          {/* Checklist Footer Actions */}
          <div className="pt-2 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div className="text-[11px] text-slate-600">
              Need help obtaining or authenticating any documents?
            </div>
            <div className="flex items-center space-x-2">
              <button
                onClick={() => onOpenBooking(currentCountryData.country)}
                className="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl transition flex items-center space-x-1 shadow-xs cursor-pointer"
              >
                <span>Audit My {currentCountryData.country} Documents</span>
                <ChevronRight className="w-3.5 h-3.5 text-amber-300" />
              </button>
              <button
                onClick={() => setShowChecklist(false)}
                className="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold px-3 py-1.5 rounded-xl transition cursor-pointer"
              >
                Close Checklist
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Visual Step Stepper / Milestone Rail */}
      <div className="space-y-2 pt-1">
        <div className="flex items-center justify-between text-xs">
          <span className="font-bold text-slate-800 flex items-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-red-600" />
            Milestones ({activeStepIndex + 1} of {totalSteps})
          </span>
          <span className="text-[11px] text-slate-500">
            Tap any milestone to view details
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

        {/* Required Documents for this step with quick toggle link */}
        <div className="space-y-1.5 pt-1">
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-800 flex items-center gap-1">
              <FileText className="w-3.5 h-3.5 text-slate-600" />
              Essential Documents for this step:
            </span>
            <button
              onClick={() => setShowChecklist(true)}
              className="text-[10px] font-bold text-red-600 hover:text-red-700 flex items-center gap-0.5 cursor-pointer underline underline-offset-2"
            >
              <span>View Full {currentCountryData.country} Checklist</span>
              <ChevronRight className="w-3 h-3" />
            </button>
          </div>
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
