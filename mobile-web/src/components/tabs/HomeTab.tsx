import React, { useState } from 'react';
import { 
  Sparkles, 
  Search, 
  MapPin, 
  Calendar, 
  ChevronRight, 
  Award, 
  CheckCircle2, 
  Star, 
  Phone, 
  MessageCircle, 
  Compass, 
  GraduationCap, 
  BookOpen, 
  ShieldCheck, 
  PlaneTakeoff, 
  ArrowRight,
  TrendingUp,
  Clock,
  ExternalLink,
  Menu,
  Bell
} from 'lucide-react';
import { 
  GEIC_BRAND, 
  GEIC_STATS, 
  STUDY_DESTINATIONS, 
  GEIC_SERVICES, 
  FOUR_STEPS, 
  STUDENT_REVIEWS, 
  UPCOMING_EXPO 
} from '../../data/geicData';
import { AppTab, MoreSection, StudyDestination } from '../../types';
import { HeroSliderBanner } from '../home/HeroSliderBanner';
import { NotificationsModal } from '../modals/NotificationsModal';

interface HomeTabProps {
  onNavigateTab: (tab: AppTab) => void;
  onNavigateMoreSection?: (section: MoreSection) => void;
  onSelectDestination: (destination: StudyDestination) => void;
  onOpenBooking: (country?: string) => void;
  onOpenExpo: () => void;
  onOpenDrawer?: () => void;
}

export const HomeTab: React.FC<HomeTabProps> = ({
  onNavigateTab,
  onNavigateMoreSection,
  onSelectDestination,
  onOpenBooking,
  onOpenExpo,
  onOpenDrawer,
}) => {
  const [activeStep, setActiveStep] = useState(0);
  const [searchQuery, setSearchQuery] = useState('');
  const [notificationsOpen, setNotificationsOpen] = useState(false);
  const [hasUnreadAlerts, setHasUnreadAlerts] = useState(true);

  const filteredDestinations = STUDY_DESTINATIONS.filter(
    (d) =>
      d.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      d.popularCourses.some((c) => c.toLowerCase().includes(searchQuery.toLowerCase()))
  );

  return (
    <div className="pb-24 text-slate-800">
      {/* Sticky Top Mobile App Header */}
      <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-md px-4 py-2.5 border-b border-slate-200/80 shadow-xs">
        {/* Brand Bar with Hamburger Menu */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            {/* Hamburger Menu Trigger */}
            <button
              onClick={onOpenDrawer}
              className="p-2 -ml-1.5 text-slate-700 hover:text-slate-900 rounded-2xl hover:bg-slate-100 active:bg-slate-200 transition cursor-pointer"
              aria-label="Open navigation menu"
            >
              <Menu className="w-5 h-5 text-slate-800" />
            </button>

            <div className="w-9 h-9 rounded-2xl bg-gradient-to-tr from-red-600 via-rose-600 to-amber-500 p-0.5 shadow-md flex items-center justify-center shrink-0">
              <div className="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
                <span className="font-black text-white text-sm tracking-tighter">TG</span>
              </div>
            </div>
            <div>
              <div className="flex items-center space-x-1.5">
                <h1 className="font-black text-sm text-slate-900 tracking-tight">Trans Globe</h1>
                <span className="text-[9px] bg-red-100 text-red-700 font-extrabold px-1.5 py-0.2 rounded-md">
                  INDORE
                </span>
              </div>
              <p className="text-[11px] text-slate-500 flex items-center gap-1 font-medium">
                <MapPin className="w-3 h-3 text-red-500" />
                <span>Indore, Madhya Pradesh</span>
              </p>
            </div>
          </div>

          <div className="flex items-center space-x-1.5">
            {/* Mock Notification Bell Icon */}
            <button
              onClick={() => setNotificationsOpen(true)}
              className="relative p-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 hover:text-slate-900 transition border border-slate-200 shadow-xs cursor-pointer focus:outline-none"
              title="Application Deadlines & Expo Alerts"
              aria-label="View notifications"
            >
              <Bell className="w-4 h-4 text-slate-700" />
              {hasUnreadAlerts && (
                <span className="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center">
                  <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span className="relative inline-flex rounded-full h-3 w-3 bg-red-600 text-[8px] font-black text-white items-center justify-center leading-none">
                    4
                  </span>
                </span>
              )}
            </button>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I want to enquire about studying abroad for 2026 intake.')}`}
              target="_blank"
              rel="noreferrer"
              className="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition border border-emerald-200 shadow-xs"
              title="WhatsApp Indore Desk"
            >
              <MessageCircle className="w-4 h-4" />
            </a>

            <a
              href="tel:+919826666886"
              className="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition border border-red-200 shadow-xs"
              title="Call Trans Globe Indore"
            >
              <Phone className="w-4 h-4" />
            </a>
          </div>
        </div>
      </header>

      {/* Main Home Tab Scrollable Content Body */}
      <div className="pt-3 space-y-5">
        <div className="px-4 space-y-3">
          {/* 3 Slider Banner in Hero Section */}
          <HeroSliderBanner
            onOpenBooking={onOpenBooking}
            onNavigateTab={onNavigateTab}
            onNavigateMoreSection={onNavigateMoreSection}
          />

          {/* Search / Filter Input */}
          <div className="relative">
            <Search className="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search country, course (e.g. Data Science, UK, Go8)..."
              className="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium bg-white shadow-xs focus:outline-none focus:ring-2 focus:ring-red-500 transition"
            />
          </div>
        </div>

      {/* Global Uni Expo 2026 Hero Card */}
      <div className="px-4">
        <div 
          onClick={onOpenExpo}
          className="bg-gradient-to-br from-red-900 via-red-800 to-slate-950 text-white rounded-3xl p-4 shadow-xl border border-red-700/50 relative overflow-hidden cursor-pointer group"
        >
          <div className="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-amber-500/20 via-transparent to-transparent pointer-events-none" />
          
          <div className="flex items-start justify-between">
            <div className="space-y-1">
              <span className="text-[10px] font-black uppercase tracking-widest text-amber-300 bg-amber-400/20 px-2 py-0.5 rounded-full border border-amber-400/30">
                Major Indore Event
              </span>
              <h3 className="text-base font-black text-white mt-1 group-hover:text-amber-200 transition">
                {UPCOMING_EXPO.title}
              </h3>
              <p className="text-[11px] text-slate-200 font-medium">
                {UPCOMING_EXPO.subtitle}
              </p>
            </div>

            <div className="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex flex-col items-center justify-center border border-white/20 text-center shrink-0">
              <span className="text-[10px] uppercase font-bold text-amber-300">OCT</span>
              <span className="text-base font-black text-white leading-none">18</span>
            </div>
          </div>

          <div className="mt-3 pt-2.5 border-t border-red-700/60 flex items-center justify-between text-xs">
            <div className="flex items-center space-x-1.5 text-[11px] text-slate-300">
              <MapPin className="w-3.5 h-3.5 text-amber-400" />
              <span className="truncate max-w-[180px]">{UPCOMING_EXPO.location}</span>
            </div>

            <span className="text-amber-300 font-bold flex items-center gap-1 group-hover:translate-x-1 transition text-xs">
              <span>Claim VIP Pass</span>
              <ChevronRight className="w-3.5 h-3.5" />
            </span>
          </div>
        </div>
      </div>

      {/* Bento Quick Actions Grid */}
      <div className="px-4 space-y-2.5">
        <div className="flex items-center justify-between">
          <h3 className="font-extrabold text-xs text-slate-900 uppercase tracking-wider">
            Quick Services & Tools
          </h3>
          <span className="text-[11px] text-red-600 font-bold">100% Free For Students</span>
        </div>

        <div className="grid grid-cols-3 gap-2">
          <button
            onClick={() => onNavigateTab('explore')}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-110 transition">
              <GraduationCap className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">800+ Unis</span>
              <span className="text-[10px] text-slate-500">Directory</span>
            </div>
          </button>

          <button
            onClick={() => onNavigateTab('evaluator')}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition">
              <TrendingUp className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">Visa Score</span>
              <span className="text-[10px] text-slate-500">Evaluator</span>
            </div>
          </button>

          <button
            onClick={() => onNavigateTab('counsellor')}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition">
              <Sparkles className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">AI Advisor</span>
              <span className="text-[10px] text-slate-500">Trans Globe</span>
            </div>
          </button>

          <button
            onClick={() => onOpenBooking()}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
              <ShieldCheck className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">Visa Filing</span>
              <span className="text-[10px] text-slate-500">98.7% Rate</span>
            </div>
          </button>

          <button
            onClick={() => onNavigateTab('explore')}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
              <Award className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">Scholarships</span>
              <span className="text-[10px] text-slate-500">₹25 Cr+ Won</span>
            </div>
          </button>

          <button
            onClick={() => {
              if (onNavigateMoreSection) {
                onNavigateMoreSection('tests');
              } else {
                onNavigateTab('more');
              }
            }}
            className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
          >
            <div className="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition">
              <BookOpen className="w-4 h-4" />
            </div>
            <div className="mt-2.5">
              <span className="block font-bold text-xs text-slate-900 leading-tight">IELTS / PTE</span>
              <span className="text-[10px] text-slate-500">Test Prep</span>
            </div>
          </button>
        </div>
      </div>

      {/* Key Numbers Ticker */}
      <div className="px-4">
        <div className="bg-slate-50 border border-slate-200/80 rounded-2xl p-3 grid grid-cols-4 divide-x divide-slate-200 text-center">
          {GEIC_STATS.map((s, i) => (
            <div key={i} className="px-1 space-y-0.5">
              <span className="block font-black text-sm text-slate-900">{s.value}</span>
              <span className="block text-[9px] text-slate-500 font-medium leading-tight">{s.label}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Trending Study Destinations (Horizontal Carousel) */}
      <div className="space-y-2.5">
        <div className="flex items-center justify-between px-4">
          <div>
            <h3 className="font-extrabold text-sm text-slate-900">
              Trending Study Destinations
            </h3>
            <p className="text-[11px] text-slate-500">Explore top countries for Indian students</p>
          </div>
          <button
            onClick={() => onNavigateTab('explore')}
            className="text-xs font-bold text-red-600 flex items-center gap-0.5 hover:underline"
          >
            <span>View All (11)</span>
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="flex overflow-x-auto gap-3 px-4 pb-2 scrollbar-none snap-x snap-mandatory">
          {filteredDestinations.map((dest) => (
            <div
              key={dest.id}
              onClick={() => onSelectDestination(dest)}
              className="min-w-[210px] max-w-[210px] bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition cursor-pointer shrink-0 snap-start flex flex-col justify-between"
            >
              <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
                <img
                  src={dest.imageUrl}
                  alt={dest.name}
                  className="w-full h-full object-cover group-hover:scale-105 transition"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent" />
                <div className="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-xs px-2 py-0.5 rounded-full text-xs font-bold text-white flex items-center gap-1">
                  <span>{dest.flagEmoji}</span>
                  <span className="text-[10px]">{dest.code}</span>
                </div>
                <div className="absolute bottom-2 left-2 right-2">
                  <h4 className="text-white font-black text-sm">{dest.name}</h4>
                  <p className="text-[10px] text-slate-200 line-clamp-1">{dest.tagline}</p>
                </div>
              </div>

              <div className="p-2.5 space-y-2 text-xs">
                <div className="space-y-1">
                  <div className="flex items-center justify-between text-[10px]">
                    <span className="text-slate-500">Post-Study Work</span>
                    <span className="font-bold text-slate-800 text-right line-clamp-1">{dest.postStudyWorkVisa.split('(')[0]}</span>
                  </div>
                  <div className="flex items-center justify-between text-[10px]">
                    <span className="text-slate-500">Tuition Range</span>
                    <span className="font-bold text-red-600 text-right line-clamp-1">{dest.avgTuitionYear.split('–')[0]}</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] font-semibold text-slate-600">
                    {dest.topUniversities.length} Key Partners
                  </span>
                  <span className="text-xs font-bold text-red-600 flex items-center">
                    <span>Explore</span>
                    <ChevronRight className="w-3 h-3" />
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Interactive 4-Step Journey */}
      <div className="px-4">
        <div className="bg-slate-50 border border-slate-200 rounded-3xl p-4 space-y-3">
          <div className="flex items-center justify-between">
            <div>
              <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider">
                Roadmap to Success
              </span>
              <h3 className="font-extrabold text-sm text-slate-900">
                Your 4-Step Journey Abroad
              </h3>
            </div>
            <span className="text-[10px] bg-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded-full">
              Step {activeStep + 1} of 4
            </span>
          </div>

          <div className="flex items-center justify-between relative px-2 pt-1">
            <div className="absolute top-5 left-6 right-6 h-0.5 bg-slate-200 -z-0" />
            {FOUR_STEPS.map((step, idx) => (
              <button
                key={idx}
                onClick={() => setActiveStep(idx)}
                className={`relative z-10 w-9 h-9 rounded-xl font-bold text-xs flex items-center justify-center transition cursor-pointer ${
                  activeStep === idx
                    ? 'bg-red-600 text-white shadow-md scale-110'
                    : activeStep > idx
                    ? 'bg-slate-900 text-white'
                    : 'bg-white text-slate-600 border border-slate-300'
                }`}
              >
                {step.step}
              </button>
            ))}
          </div>

          <div className="bg-white border border-slate-200 rounded-2xl p-3.5 space-y-2">
            <div className="flex items-center space-x-2 text-slate-900 font-bold text-xs">
              <CheckCircle2 className="w-4 h-4 text-red-600" />
              <span>{FOUR_STEPS[activeStep].title}</span>
            </div>
            <p className="text-[11px] text-slate-600 leading-relaxed">
              {FOUR_STEPS[activeStep].desc}
            </p>
            <div className="pt-1 flex items-center justify-between">
              <button
                onClick={() => onOpenBooking()}
                className="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1"
              >
                <span>Get Step {activeStep + 1} Support</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
              {activeStep < 3 && (
                <button
                  onClick={() => setActiveStep((prev) => prev + 1)}
                  className="text-[11px] font-semibold text-slate-500 hover:text-slate-800"
                >
                  Next Step →
                </button>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Verified Student Reviews (4.8 Stars / 495 Google Reviews) */}
      <div className="px-4 space-y-2.5">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center space-x-1.5">
              <h3 className="font-extrabold text-sm text-slate-900">
                Verified Student Reviews
              </h3>
              <span className="flex items-center text-[10px] font-bold text-amber-500 bg-amber-50 px-1.5 py-0.2 rounded-md border border-amber-200">
                <Star className="w-2.5 h-2.5 fill-amber-400 text-amber-400 mr-0.5" /> 4.8
              </span>
            </div>
            <p className="text-[11px] text-slate-500">495+ Google Reviews for Trans Globe Indore</p>
          </div>
          <span className="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-full">
            Google Verified
          </span>
        </div>

        <div className="space-y-2.5">
          {STUDENT_REVIEWS.slice(0, 3).map((review) => (
            <div
              key={review.id}
              className="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-2 text-xs"
            >
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                  <div className="w-7 h-7 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-[11px]">
                    {review.avatarLetter}
                  </div>
                  <div>
                    <h5 className="font-bold text-slate-900 leading-tight">{review.studentName}</h5>
                    <p className="text-[10px] text-slate-500">{review.university} • {review.destination}</p>
                  </div>
                </div>

                <div className="flex items-center text-amber-400">
                  {[...Array(review.rating)].map((_, i) => (
                    <Star key={i} className="w-3 h-3 fill-amber-400" />
                  ))}
                </div>
              </div>

              <p className="text-[11px] text-slate-600 italic leading-relaxed line-clamp-3">
                "{review.comment}"
              </p>

              <div className="flex items-center justify-between pt-1 text-[10px] text-slate-400">
                <span className="text-emerald-600 font-semibold flex items-center gap-1">
                  <CheckCircle2 className="w-3 h-3" /> Enrolled via Trans Globe
                </span>
                <span>{review.reviewDate}</span>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Indore Office Contact Card */}
      <div className="px-4">
        <div className="p-4 rounded-3xl bg-slate-900 text-white space-y-3 shadow-lg">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 rounded-xl bg-red-600 flex items-center justify-center text-white font-bold">
                <MapPin className="w-4 h-4" />
              </div>
              <div>
                <h4 className="font-bold text-xs">Trans Globe Indore Center</h4>
                <p className="text-[10px] text-slate-400">Visit us for free consultation</p>
              </div>
            </div>
            <span className="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-bold">
              Open 10 AM - 7 PM
            </span>
          </div>

          <div className="text-xs text-slate-300 space-y-1">
            <p className="font-medium text-[11px]">{GEIC_BRAND.address}</p>
            <p className="text-[11px] text-slate-400">Helpline: <strong className="text-white">+91 98266 66886</strong></p>
          </div>

          <div className="grid grid-cols-2 gap-2 pt-1">
            <a
              href="tel:+919826666886"
              className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-xl text-[11px] flex items-center justify-center space-x-1.5 transition"
            >
              <Phone className="w-3.5 h-3.5" />
              <span>Call Indore Desk</span>
            </a>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I want to visit the Indore office for study abroad counselling.')}`}
              target="_blank"
              rel="noreferrer"
              className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-xl text-[11px] flex items-center justify-center space-x-1.5 transition"
            >
              <MessageCircle className="w-3.5 h-3.5" />
              <span>WhatsApp Chat</span>
            </a>
          </div>
        </div>
      </div>
      </div>

      {/* Interactive Mock Notifications Modal */}
      <NotificationsModal
        isOpen={notificationsOpen}
        onClose={() => {
          setNotificationsOpen(false);
          setHasUnreadAlerts(false);
        }}
        onNavigateTab={onNavigateTab}
        onOpenBooking={onOpenBooking}
      />
    </div>
  );
};
