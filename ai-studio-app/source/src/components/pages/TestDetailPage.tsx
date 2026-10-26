import React, { useState } from 'react';
import { 
  ArrowLeft, 
  CheckCircle2, 
  Sparkles, 
  Phone, 
  MessageCircle, 
  MapPin, 
  ChevronRight, 
  ChevronDown, 
  Share2, 
  BookOpen, 
  Clock, 
  Calendar, 
  Award, 
  Target, 
  Check, 
  CheckCheck,
  GraduationCap,
  Users
} from 'lucide-react';
import { EnglishTestInfo } from '../../types';
import { ENGLISH_TESTS, GEIC_BRAND } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';

interface TestDetailPageProps {
  test: EnglishTestInfo;
  onBack: () => void;
  onSelectTest: (testId: string) => void;
  onBookConsultation: (testName: string) => void;
  onViewAllTests: () => void;
}

export const TestDetailPage: React.FC<TestDetailPageProps> = ({
  test,
  onBack,
  onSelectTest,
  onBookConsultation,
  onViewAllTests,
}) => {
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);
  const [demoRegistered, setDemoRegistered] = useState(false);
  const [studentName, setStudentName] = useState('');
  const [studentPhone, setStudentPhone] = useState('');
  const [preferredBatch, setPreferredBatch] = useState('Morning Batch');
  const [copiedLink, setCopiedLink] = useState(false);

  const handleShare = () => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(window.location.href);
      setCopiedLink(true);
      setTimeout(() => setCopiedLink(false), 2500);
    }
  };

  const handleDemoSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!studentName.trim() || !studentPhone.trim()) return;
    setDemoRegistered(true);
  };

  const otherTests = ENGLISH_TESTS.filter((t) => t.id !== test.id);

  return (
    <div className="pb-32 text-stone-800 space-y-4 bg-stone-50/40 min-h-screen animate-in fade-in duration-200">
      {/* Sticky Top Navigation Bar with Soothing Frosted Glass */}
      <header className="sticky top-0 z-30 bg-white/90 backdrop-blur-md px-4 py-3 border-b border-stone-200/70 shadow-2xs flex items-center justify-between">
        <button
          onClick={onBack}
          className="flex items-center space-x-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition-colors cursor-pointer group"
        >
          <div className="w-7 h-7 rounded-full bg-stone-100 group-hover:bg-stone-200/80 flex items-center justify-center transition">
            <ArrowLeft className="w-3.5 h-3.5 text-stone-700" />
          </div>
          <span>All Tests</span>
        </button>

        <div className="flex items-center space-x-2">
          <BookmarkButton
            type="test"
            id={test.id}
            title={test.name}
            size="sm"
            variant="pill"
          />

          <button
            onClick={handleShare}
            className="p-2 rounded-full text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition cursor-pointer"
            title="Share this test page"
            aria-label="Share this test page"
          >
            {copiedLink ? (
              <Check className="w-4 h-4 text-emerald-600" />
            ) : (
              <Share2 className="w-4 h-4" />
            )}
          </button>

          <a
            href="tel:+919826666886"
            className="p-2 rounded-full bg-stone-100 text-stone-700 hover:bg-rose-50 hover:text-rose-700 transition"
            title="Call Coaching Desk"
            aria-label="Call Coaching Desk"
          >
            <Phone className="w-4 h-4" />
          </a>
        </div>
      </header>

      {/* Hero Banner Card with Soothing Aesthetic */}
      <section className="px-4">
        <div className="relative rounded-3xl overflow-hidden bg-stone-900 border border-stone-200/80 shadow-md">
          <div className="h-52 w-full relative">
            <img
              src={test.bannerImage}
              alt={test.name}
              referrerPolicy="no-referrer"
              className="w-full h-full object-cover opacity-90"
            />
            {/* Calming ambient gradient */}
            <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/65 to-stone-950/20" />

            <div className="absolute top-3.5 left-4 flex items-center space-x-2 text-[11px] font-medium text-stone-300">
              <span className="bg-white/20 backdrop-blur-md px-2.5 py-0.5 rounded-full text-white font-semibold">
                {test.code}
              </span>
              <span>·</span>
              <span className="text-stone-200">{test.category}</span>
            </div>

            <div className="absolute top-3.5 right-4">
              <div className="bg-stone-900/90 backdrop-blur-md px-3 py-1 rounded-xl border border-stone-700/80 text-right">
                <span className="text-[9px] font-medium text-stone-400 block uppercase">TARGET</span>
                <span className="text-xs font-black text-amber-300">{test.minTargetBand}</span>
              </div>
            </div>

            <div className="absolute bottom-4 left-4 right-4 text-white space-y-1.5">
              <h1 className="text-2xl font-black tracking-tight text-white leading-snug">
                {test.name}
              </h1>
              <p className="text-xs text-stone-300 font-normal leading-relaxed line-clamp-2">
                {test.tagline}
              </p>
            </div>
          </div>

          {/* Quick Metrics Ticker */}
          <div className="bg-stone-950/95 p-3.5 border-t border-stone-800/80 grid grid-cols-3 divide-x divide-stone-800 text-center text-stone-300">
            <div className="px-1">
              <span className="text-[10px] text-stone-500 font-medium block uppercase tracking-wider">Duration</span>
              <span className="text-xs font-bold text-white">{test.duration.split('(')[0]}</span>
            </div>
            <div className="px-1">
              <span className="text-[10px] text-stone-500 font-medium block uppercase tracking-wider">Scale</span>
              <span className="text-xs font-bold text-white">{test.scoringScale.split('(')[0]}</span>
            </div>
            <div className="px-1">
              <span className="text-[10px] text-stone-500 font-medium block uppercase tracking-wider">Mode</span>
              <span className="text-xs font-bold text-emerald-400">Class & Live</span>
            </div>
          </div>
        </div>
      </section>

      {/* Acceptance & Overview */}
      <section className="px-4 space-y-3">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div>
            <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block mb-1">
              Test Architecture & Purpose
            </span>
            <p className="text-xs text-stone-700 leading-relaxed font-normal whitespace-pre-line">
              {test.overview}
            </p>
          </div>

          {/* Institutional Acceptance */}
          <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200/80 space-y-1">
            <span className="text-xs font-bold text-stone-900 block flex items-center gap-1.5">
              <Target className="w-3.5 h-3.5 text-rose-600" />
              <span>Global University & Visa Acceptance:</span>
            </span>
            <p className="text-xs text-stone-600 leading-relaxed">
              {test.acceptedBy}
            </p>
          </div>

          {/* Test Format Summary */}
          <div className="space-y-1.5 pt-1 border-t border-stone-100">
            <span className="text-[11px] font-medium text-stone-500 block">
              Official Examination Format:
            </span>
            <p className="text-xs text-stone-700 bg-stone-50/80 p-3 rounded-2xl border border-stone-200/70 leading-relaxed">
              {test.testFormat}
            </p>
          </div>
        </div>
      </section>

      {/* Module-by-Module Breakdown */}
      <section className="px-4">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div className="flex items-center justify-between">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
                Syllabus & Structure
              </span>
              <h3 className="text-sm font-black text-stone-900">
                Section Breakdown & Scoring Tactics
              </h3>
            </div>
            <span className="text-[10px] font-bold bg-stone-100 text-stone-700 px-2.5 py-0.5 rounded-full">
              {test.modules.length} Modules
            </span>
          </div>

          <div className="space-y-3 pt-1">
            {test.modules.map((mod, i) => (
              <div
                key={i}
                className="bg-stone-50/80 border border-stone-200/80 rounded-2xl p-4 space-y-2 text-xs"
              >
                <div className="flex items-start justify-between">
                  <div className="flex items-center space-x-2.5">
                    <span className="w-6 h-6 rounded-full bg-stone-900 text-white font-bold text-xs flex items-center justify-center">
                      {i + 1}
                    </span>
                    <h4 className="font-bold text-xs text-stone-900">{mod.title}</h4>
                  </div>
                  <span className="text-[10px] font-medium text-stone-600 bg-white border border-stone-200 px-2 py-0.5 rounded-lg shadow-2xs">
                    {mod.duration}
                  </span>
                </div>

                <p className="text-xs text-stone-600 leading-relaxed font-normal">
                  {mod.description}
                </p>

                <div className="bg-amber-50/70 border border-amber-200/60 p-2.5 rounded-xl text-xs text-amber-900 space-y-0.5">
                  <strong className="block font-bold text-[11px] text-amber-800">Master Trainer Strategy:</strong>
                  <p className="text-[11px] text-amber-900/90 leading-relaxed">{mod.scoringTips}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Why Prep With Trans Globe Indore */}
      <section className="px-4">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div className="flex items-center justify-between">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
                Classroom & Lab Facilities
              </span>
              <h3 className="text-sm font-black text-stone-900">
                Why Prepare With Trans Globe
              </h3>
            </div>
            <span className="text-[10px] font-bold text-rose-800 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200/60">
              IDP & British Council
            </span>
          </div>

          <div className="space-y-2.5 pt-1">
            {test.transGlobeHighlights.map((hl, i) => (
              <div key={i} className="flex items-start space-x-3 text-xs text-stone-700">
                <div className="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                  <Check className="w-3 h-3 stroke-[2.5]" />
                </div>
                <span className="leading-relaxed font-normal">{hl}</span>
              </div>
            ))}
          </div>

          {/* Batch Timings Card */}
          <div className="mt-3 bg-stone-900 text-white rounded-2xl p-4 space-y-1.5 text-xs">
            <div className="flex items-center space-x-1.5 text-amber-300 font-bold text-xs">
              <Clock className="w-3.5 h-3.5" />
              <span>Available Batch Schedules in Indore:</span>
            </div>
            <p className="text-stone-300 text-xs leading-relaxed font-normal">
              {test.batchTimings}
            </p>
            {test.courseFeeINR && (
              <p className="text-[11px] text-stone-400 pt-1 border-t border-stone-800">
                Course Fee: <strong className="text-emerald-400">{test.courseFeeINR}</strong> (includes test portal access & mocks)
              </p>
            )}
          </div>
        </div>
      </section>

      {/* Free Demo & Mock Test Form */}
      <section className="px-4">
        <div className="bg-gradient-to-br from-stone-900 via-stone-850 to-stone-900 text-white rounded-3xl p-5 shadow-lg border border-stone-800 space-y-4 relative overflow-hidden">
          <div className="absolute -top-12 -right-12 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none" />

          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-amber-300">
              Zero Obligation
            </span>
            <h3 className="text-lg font-black text-white leading-tight">
              Reserve Free {test.code} Demo Class & Diagnostic Mock
            </h3>
            <p className="text-xs text-stone-300 leading-relaxed font-normal">
              Experience our faculty’s teaching methodology in Indore and receive your personalized diagnostic band score within 24 hours.
            </p>
          </div>

          {demoRegistered ? (
            <div className="bg-emerald-950/70 border border-emerald-700/60 rounded-2xl p-4 text-center space-y-1.5 animate-in zoom-in-95 duration-200">
              <div className="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center">
                <CheckCheck className="w-5 h-5 text-emerald-400" />
              </div>
              <h4 className="font-extrabold text-sm text-white">
                Demo Class Slot Reserved!
              </h4>
              <p className="text-xs text-emerald-200 leading-relaxed">
                Thank you, <strong>{studentName}</strong>. Our test coordinator will reach out at <strong>{studentPhone}</strong> to schedule your slot.
              </p>
              <button
                onClick={() => setDemoRegistered(false)}
                className="mt-2 text-xs text-emerald-300 underline font-semibold cursor-pointer"
              >
                Register another student
              </button>
            </div>
          ) : (
            <form onSubmit={handleDemoSubmit} className="space-y-3 pt-1 text-xs">
              <div>
                <label className="text-[10px] font-bold text-stone-300 block mb-1">
                  Full Name *
                </label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Raghav Sanoriya"
                  value={studentName}
                  onChange={(e) => setStudentName(e.target.value)}
                  className="w-full bg-stone-950/80 border border-stone-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-rose-500 transition"
                />
              </div>

              <div>
                <label className="text-[10px] font-bold text-stone-300 block mb-1">
                  WhatsApp / Phone Number *
                </label>
                <input
                  type="tel"
                  required
                  placeholder="+91 98266 66886"
                  value={studentPhone}
                  onChange={(e) => setStudentPhone(e.target.value)}
                  className="w-full bg-stone-950/80 border border-stone-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-rose-500 transition"
                />
              </div>

              <div>
                <label className="text-[10px] font-bold text-stone-300 block mb-1">
                  Preferred Batch Timing
                </label>
                <select
                  value={preferredBatch}
                  onChange={(e) => setPreferredBatch(e.target.value)}
                  className="w-full bg-stone-950/80 border border-stone-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-rose-500 transition cursor-pointer"
                >
                  <option value="Morning Batch">Morning Batch (8:00 AM – 10:00 AM)</option>
                  <option value="Mid-day Lab Batch">Mid-day Computer Lab (11:00 AM – 1:00 PM)</option>
                  <option value="Evening Batch">Evening Batch (5:30 PM – 7:30 PM)</option>
                  <option value="Weekend Intensive">Weekend Intensive (Saturday & Sunday)</option>
                </select>
              </div>

              <button
                type="submit"
                className="w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-3 rounded-2xl text-xs flex items-center justify-center space-x-1.5 transition shadow-md cursor-pointer mt-1"
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>Confirm Free Demo & Mock Test</span>
              </button>
            </form>
          )}

          <div className="grid grid-cols-2 gap-2 pt-1 text-xs">
            <a
              href="tel:+919826666886"
              className="bg-stone-800/90 hover:bg-stone-700 text-stone-200 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-stone-700 transition"
            >
              <Phone className="w-3.5 h-3.5 text-stone-400" />
              <span>Call Coaching Desk</span>
            </a>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent(`Hi Trans Globe! I would like to register for a free demo class for ${test.name}.`)}`}
              target="_blank"
              rel="noreferrer"
              className="bg-emerald-950/70 hover:bg-emerald-900 text-emerald-300 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-emerald-800/50 transition"
            >
              <MessageCircle className="w-3.5 h-3.5 text-emerald-400" />
              <span>WhatsApp Slot</span>
            </a>
          </div>
        </div>
      </section>

      {/* Frequently Asked Questions */}
      {test.faqs && test.faqs.length > 0 && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
                Testing FAQs
              </span>
              <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                {test.code} Questions Answered
              </h3>
            </div>

            <div className="space-y-2 pt-1">
              {test.faqs.map((faq, idx) => {
                const isOpen = openFaqIndex === idx;

                return (
                  <div
                    key={idx}
                    className="border border-stone-200/80 rounded-2xl overflow-hidden transition"
                  >
                    <button
                      onClick={() => setOpenFaqIndex(isOpen ? null : idx)}
                      className="w-full p-3.5 text-left flex items-center justify-between gap-2 hover:bg-stone-50/80 transition cursor-pointer"
                    >
                      <span className="font-bold text-xs text-stone-800">
                        {faq.question}
                      </span>
                      <ChevronDown
                        className={`w-4 h-4 text-stone-400 shrink-0 transition-transform ${
                          isOpen ? 'rotate-180 text-stone-700' : ''
                        }`}
                      />
                    </button>
                    {isOpen && (
                      <div className="px-3.5 pb-3.5 text-xs text-stone-600 leading-relaxed font-normal bg-stone-50/50 border-t border-stone-100 pt-2.5">
                        {faq.answer}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        </section>
      )}

      {/* Explore Other Tests Carousel */}
      <section className="space-y-3 pt-2">
        <div className="flex items-center justify-between px-4">
          <div className="space-y-0.5">
            <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
              Curriculum
            </span>
            <h3 className="font-extrabold text-xs text-stone-900">
              Other Test Coaching Programs
            </h3>
          </div>
          <button
            onClick={onViewAllTests}
            className="text-xs font-semibold text-rose-600 flex items-center gap-0.5 hover:underline cursor-pointer"
          >
            <span>View All (8)</span>
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="flex overflow-x-auto gap-3 px-4 pb-2 scrollbar-none snap-x snap-mandatory">
          {otherTests.map((other) => (
            <div
              key={other.id}
              onClick={() => onSelectTest(other.id)}
              className="min-w-[200px] max-w-[200px] bg-white rounded-3xl border border-stone-200/80 p-4 shadow-xs hover:border-rose-300 transition cursor-pointer shrink-0 snap-start flex flex-col justify-between group"
            >
              <div className="space-y-2">
                <span className="text-[10px] font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-full inline-block">
                  {other.code}
                </span>
                <h4 className="font-bold text-xs text-stone-900 group-hover:text-rose-600 transition-colors line-clamp-1">
                  {other.name}
                </h4>
                <p className="text-[11px] text-stone-500 line-clamp-2 leading-relaxed">
                  {other.tagline}
                </p>
              </div>

              <div className="pt-3 mt-3 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold text-rose-600">
                <span>View Syllabus</span>
                <ChevronRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
};
