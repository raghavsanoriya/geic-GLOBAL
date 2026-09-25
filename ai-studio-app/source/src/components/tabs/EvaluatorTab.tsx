import React, { useState } from 'react';
import { 
  TrendingUp, 
  CheckCircle2, 
  ShieldCheck, 
  Sparkles, 
  Award, 
  BookOpen, 
  FileText, 
  ArrowRight,
  RefreshCw,
  Info,
  Calendar,
  Layers
} from 'lucide-react';
import { STUDY_DESTINATIONS } from '../../data/liveCatalog';
import { ProfileEvaluationResult } from '../../types';
import { VisaProcessRoadmap } from '../evaluator/VisaProcessRoadmap';

interface EvaluatorTabProps {
  onOpenBooking: (country?: string) => void;
}

export const EvaluatorTab: React.FC<EvaluatorTabProps> = ({ onOpenBooking }) => {
  const [fullName, setFullName] = useState('');
  const [scorePercentage, setScorePercentage] = useState(76);
  const [intendedLevel, setIntendedLevel] = useState<'Masters' | 'Bachelors' | 'Diploma' | 'Doctorate'>('Masters');
  const [targetCountries, setTargetCountries] = useState<string[]>(['Australia', 'United Kingdom']);
  const [intendedCourse, setIntendedCourse] = useState('Data Science / Computer Science');
  const [englishTest, setEnglishTest] = useState<'IELTS' | 'PTE' | 'TOEFL' | 'Duolingo' | 'Not Taken Yet'>('IELTS');
  const [testScore, setTestScore] = useState('6.5');
  const [workExperienceYears, setWorkExperienceYears] = useState(1);

  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<ProfileEvaluationResult | null>(null);
  const [evaluationError, setEvaluationError] = useState('');

  // Document checklist state
  const [checklist, setChecklist] = useState<Record<string, boolean>>({
    passport: false,
    transcripts: false,
    sop: false,
    lor: false,
    bankStatement: false,
    englishExam: false,
  });

  const toggleChecklist = (key: string) => {
    setChecklist((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const handleCountryToggle = (country: string) => {
    setTargetCountries((prev) => {
      if (prev.includes(country)) {
        return prev.length > 1 ? prev.filter((c) => c !== country) : prev;
      } else {
        return [...prev, country];
      }
    });
  };

  const handleEvaluate = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    setLoading(true);
    setEvaluationError('');

    try {
      const res = await fetch('/api/mobile/profile-evaluations', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          fullName,
          academicPercentage: scorePercentage,
          studyLevel: { Bachelors: 'Undergraduate', Masters: 'Postgraduate', Diploma: 'Diploma or pathway', Doctorate: 'Research' }[intendedLevel],
          originalStudyLevel: intendedLevel,
          preferredDestinations: targetCountries.map(name => STUDY_DESTINATIONS.find(item => item.name === name)?.id || name),
          intendedCourse,
          englishTest: englishTest === 'Not Taken Yet' ? 'Planning to take a test' : englishTest,
          englishScore: testScore,
          workExperienceYears,
        }),
      });

      if (!res.ok) throw new Error('Evaluation failed');
      const data = await res.json();
      setResult({
        overallScore: data.readinessScore, visaSuccessProbability: null, summary: data.summary,
        ambitiousMatches: ['Discuss ambitious university choices with a counsellor.'],
        targetMatches: data.matches.map((match: { name: string; fit: string }) => `${match.name} — ${match.fit}`),
        safeMatches: ['Admission and visa outcomes require official assessment.'],
        scholarshipEligibility: 'Confirm current awards with the institution.',
        keyStrengths: data.strengths, recommendedActionItems: data.actionItems,
      });
    } catch (err) {
      setResult(null);
      setEvaluationError(err instanceof Error ? err.message : 'Evaluation unavailable. Please try again.');
      return;
    } finally {
      setLoading(false);
    }
  };

  const completedDocsCount = Object.values(checklist).filter(Boolean).length;

  return (
    <div className="pb-24 text-slate-800 space-y-4">
      {/* Top Header */}
      <div className="pt-2 px-4 space-y-2">
        <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider">
          Instant Admissibility & Visa Tool
        </span>
        <h2 className="text-xl font-black text-slate-900 leading-tight">
          Profile Evaluator & Visa Meter
        </h2>
        <p className="text-xs text-slate-500">
          Review your application readiness and explore study options with GEIC guidance.
        </p>
      </div>

      {/* Evaluation Input Form */}
      <div className="px-4">
        <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3.5">
          <div className="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 className="font-extrabold text-xs text-slate-900 flex items-center gap-1.5">
              <Sparkles className="w-3.5 h-3.5 text-red-600" />
              <span>Academic & Visa Parameters</span>
            </h3>
            <span className="text-[10px] text-slate-400">1-Minute Check</span>
          </div>

          <form onSubmit={handleEvaluate} className="space-y-3 text-xs">
            {/* Academic Score Slider */}
            <div>
              <div className="flex justify-between items-center mb-1">
                <label className="text-[11px] font-bold text-slate-700">
                  Academic Score / GPA / Percentage
                </label>
                <span className="text-xs font-black text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">
                  {scorePercentage}%
                </span>
              </div>
              <input
                type="range"
                min="50"
                max="98"
                value={scorePercentage}
                onChange={(e) => setScorePercentage(Number(e.target.value))}
                className="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-red-600"
              />
              <div className="flex justify-between text-[9px] text-slate-400 mt-0.5 font-medium">
                <span>50% (Passing)</span>
                <span>75% (First Class)</span>
                <span>95%+ (Distinction)</span>
              </div>
            </div>

            {/* Target Degree Level */}
            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1">
                Intended Study Level
              </label>
              <div className="grid grid-cols-3 gap-1.5">
                {(['Bachelors', 'Masters', 'Diploma'] as const).map((level) => (
                  <button
                    key={level}
                    type="button"
                    onClick={() => setIntendedLevel(level)}
                    className={`py-1.5 px-2 rounded-xl text-[11px] font-semibold transition border cursor-pointer ${
                      intendedLevel === level
                        ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                        : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                    }`}
                  >
                    {level}
                  </button>
                ))}
              </div>
            </div>

            {/* Target Countries Multi-Select */}
            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1">
                Target Countries
              </label>
              <div className="flex flex-wrap gap-1.5">
                {STUDY_DESTINATIONS.map(destination => destination.name).map(
                  (c) => {
                    const isSelected = targetCountries.includes(c);
                    return (
                      <button
                        key={c}
                        type="button"
                        onClick={() => handleCountryToggle(c)}
                        className={`text-[11px] font-semibold px-2.5 py-1 rounded-xl transition border cursor-pointer ${
                          isSelected
                            ? 'bg-red-600 text-white border-red-600 shadow-xs'
                            : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                        }`}
                      >
                        {c}
                      </button>
                    );
                  }
                )}
              </div>
            </div>

            {/* Target Field */}
            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1">
                Field of Study
              </label>
              <input
                type="text"
                value={intendedCourse}
                onChange={(e) => setIntendedCourse(e.target.value)}
                placeholder="e.g. Computer Science, AI, MBA, Mechanical"
                className="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
              />
            </div>

            {/* English Test */}
            <div className="grid grid-cols-2 gap-2">
              <div>
                <label className="block text-[11px] font-bold text-slate-700 mb-1">
                  Language Exam
                </label>
                <select
                  value={englishTest}
                  onChange={(e) => setEnglishTest(e.target.value as any)}
                  className="w-full p-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium"
                >
                  <option value="IELTS">IELTS</option>
                  <option value="PTE">PTE Academic</option>
                  <option value="TOEFL">TOEFL iBT</option>
                  <option value="Duolingo">Duolingo</option>
                  <option value="Not Taken Yet">Not Taken Yet</option>
                </select>
              </div>

              <div>
                <label className="block text-[11px] font-bold text-slate-700 mb-1">
                  Band / Score
                </label>
                <input
                  type="text"
                  value={testScore}
                  onChange={(e) => setTestScore(e.target.value)}
                  placeholder="e.g. 6.5 or 65"
                  className="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium"
                />
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer mt-1"
            >
              {loading ? (
                <>
                  <RefreshCw className="w-4 h-4 animate-spin" />
                  <span>Checking application readiness...</span>
                </>
              ) : (
                <>
                  <Sparkles className="w-4 h-4" />
                  <span>Check application readiness</span>
                </>
              )}
            </button>
          </form>
          {evaluationError && <p role="alert" className="text-xs text-red-600 mt-2">{evaluationError}</p>}
        </div>
      </div>

      {/* Evaluation Results Card */}
      {result && (
        <div className="px-4 space-y-3 animate-in fade-in duration-300">
          <div className="bg-slate-900 text-white rounded-3xl p-4 shadow-xl space-y-3.5 border border-slate-800">
            <div className="flex items-center justify-between">
              <span className="text-[10px] font-bold text-amber-400 bg-amber-500/20 px-2 py-0.5 rounded-full border border-amber-500/30">
                Evaluation Report
              </span>
              <span className="text-xs text-slate-400">{fullName}</span>
            </div>

            {/* Score Gauges */}
            <div className="grid grid-cols-2 gap-2.5">
              <div className="bg-slate-800/80 p-3 rounded-2xl border border-slate-700">
                <span className="text-[10px] text-slate-400 font-semibold block">Application readiness</span>
                <div className="flex items-baseline space-x-1 mt-1">
                  <span className="text-2xl font-black text-amber-400">{result.overallScore}</span>
                  <span className="text-xs text-slate-400">/ 100</span>
                </div>
                <div className="w-full bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                  <div
                    className="bg-amber-400 h-full rounded-full transition-all duration-500"
                    style={{ width: `${result.overallScore}%` }}
                  />
                </div>
              </div>

              <div className="bg-slate-800/80 p-3 rounded-2xl border border-slate-700">
                <span className="text-[10px] text-slate-400 font-semibold block">Visa Assessment</span>
                <div className="flex items-baseline space-x-1 mt-1">
                  <span className="text-sm font-black text-emerald-400">{result.visaSuccessProbability == null ? 'Counsellor review' : `${result.visaSuccessProbability}%`}</span>
                </div>
                <div className="w-full bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                  <div
                    className="bg-emerald-400 h-full rounded-full transition-all duration-500"
                    style={{ width: `${result.visaSuccessProbability ?? 0}%` }}
                  />
                </div>
              </div>
            </div>

            <p className="text-xs text-slate-300 leading-relaxed font-normal bg-slate-800/40 p-2.5 rounded-xl border border-slate-800">
              {result.summary}
            </p>

            {/* University Match Tiers */}
            <div className="space-y-2 pt-1">
              <h4 className="font-extrabold text-xs text-white flex items-center gap-1.5">
                <Layers className="w-3.5 h-3.5 text-red-500" />
                <span>Study planning guidance</span>
              </h4>

              {/* Ambitious */}
              <div className="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700 space-y-1">
                <div className="flex items-center space-x-1.5 text-amber-400 font-bold text-[11px]">
                  <span>🌟 Ambitious (High Tier / Group of Eight)</span>
                </div>
                <div className="text-[11px] text-slate-300 space-y-0.5 pl-2">
                  {result.ambitiousMatches.map((m, i) => (
                    <div key={i} className="flex items-center gap-1.5">
                      <span className="text-amber-400">•</span>
                      <span>{m}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Target */}
              <div className="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700 space-y-1">
                <div className="flex items-center space-x-1.5 text-emerald-400 font-bold text-[11px]">
                  <span>🎯 Destinations to explore</span>
                </div>
                <div className="text-[11px] text-slate-300 space-y-0.5 pl-2">
                  {result.targetMatches.map((m, i) => (
                    <div key={i} className="flex items-center gap-1.5">
                      <span className="text-emerald-400">•</span>
                      <span>{m}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Safe */}
              <div className="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700 space-y-1">
                <div className="flex items-center space-x-1.5 text-blue-400 font-bold text-[11px]">
                  <span>🛡️ Safe / Fast-Track Options</span>
                </div>
                <div className="text-[11px] text-slate-300 space-y-0.5 pl-2">
                  {result.safeMatches.map((m, i) => (
                    <div key={i} className="flex items-center gap-1.5">
                      <span className="text-blue-400">•</span>
                      <span>{m}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Scholarship Note */}
            <div className="p-2.5 rounded-xl bg-red-950/60 border border-red-800/60 flex items-center space-x-2 text-[11px]">
              <Award className="w-4 h-4 text-amber-400 shrink-0" />
              <span className="text-slate-200">
                <strong>Scholarship Status:</strong> {result.scholarshipEligibility}
              </span>
            </div>

            {/* Action CTA */}
            <button
              onClick={() => onOpenBooking(targetCountries[0])}
              className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
            >
              <span>Discuss Report with Indore Specialist</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      )}

      {/* Interactive Step-by-Step Visa Process Roadmap */}
      <div className="px-4">
        <VisaProcessRoadmap
          initialCountry={targetCountries[0] || 'Australia'}
          onOpenBooking={onOpenBooking}
        />
      </div>

      {/* Document Readiness Vault */}
      <div className="px-4">
        <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3">
          <div className="flex items-center justify-between">
            <div>
              <h3 className="font-extrabold text-xs text-slate-900">
                Application Document Vault
              </h3>
              <p className="text-[11px] text-slate-500">Track your readiness for fast submission</p>
            </div>
            <span className="text-[11px] font-black text-red-600 bg-red-50 px-2 py-0.5 rounded-lg border border-red-100">
              {completedDocsCount}/6 Ready
            </span>
          </div>

          <div className="space-y-2 text-xs">
            {[
              { key: 'passport', label: 'Valid Passport (min 18 months validity)', note: 'Required for all offer letters' },
              { key: 'transcripts', label: '10th, 12th & Degree Marksheets', note: 'Certified institutional copies' },
              { key: 'sop', label: 'Statement of Purpose (SOP)', note: 'Trans Globe Indore helps review & edit' },
              { key: 'lor', label: '2 Letters of Recommendation (LOR)', note: 'From college professors or managers' },
              { key: 'englishExam', label: 'IELTS / PTE / TOEFL Scorecard', note: 'Required for unconditional offer' },
              { key: 'bankStatement', label: 'Proof of Funds / Bank Statement', note: 'Required for student visa filing' },
            ].map((doc) => {
              const isChecked = checklist[doc.key];

              return (
                <div
                  key={doc.key}
                  onClick={() => toggleChecklist(doc.key)}
                  className={`p-2.5 rounded-2xl border transition cursor-pointer flex items-start justify-between ${
                    isChecked
                      ? 'border-emerald-200 bg-emerald-50/50'
                      : 'border-slate-200 bg-white hover:bg-slate-50'
                  }`}
                >
                  <div className="space-y-0.5">
                    <span className={`font-bold text-xs block ${isChecked ? 'text-emerald-900' : 'text-slate-800'}`}>
                      {doc.label}
                    </span>
                    <span className="text-[10px] text-slate-500 block">{doc.note}</span>
                  </div>

                  <div
                    className={`w-5 h-5 rounded-md border flex items-center justify-center shrink-0 mt-0.5 ${
                      isChecked
                        ? 'bg-emerald-600 border-emerald-600 text-white'
                        : 'border-slate-300 bg-white'
                    }`}
                  >
                    {isChecked && <CheckCircle2 className="w-3.5 h-3.5" />}
                  </div>
                </div>
              );
            })}
          </div>

          <p className="text-[10px] text-slate-500 text-center">
            💡 Missing documents? Trans Globe Indore provides templates and SOP drafting guidance.
          </p>
        </div>
      </div>
    </div>
  );
};
