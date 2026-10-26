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
  Bell,
  Layers,
  ArrowLeftRight
} from 'lucide-react';
import { 
  NOTIFICATIONS,
  GEIC_BRAND, 
  GEIC_STATS, 
  STUDY_DESTINATIONS, 
  FOUR_STEPS, 
  UPCOMING_EXPO 
} from '../../data/liveCatalog';
import { AppTab, MoreSection, StudyDestination, BlogPost } from '../../types';
import { GlobalSearchBar } from '../common/GlobalSearchBar';
import { UniversityComparisonData, GLOBAL_UNIVERSITIES } from '../../data/liveCatalog';
import { HeroSliderBanner } from '../home/HeroSliderBanner';
import { ServicesCardSlider } from '../home/ServicesCardSlider';
import { EventCardSlider } from '../home/EventCardSlider';
import { TestCardSlider } from '../home/TestCardSlider';
import { BlogCardSlider } from '../home/BlogCardSlider';
import { UniversitiesAutoRotate } from '../home/UniversitiesAutoRotate';
import { ReviewsAutoSlide } from '../home/ReviewsAutoSlide';
import { NotificationsModal } from '../modals/NotificationsModal';
import { BlogDetailModal } from '../modals/BlogDetailModal';

interface HomeTabProps {
  onNavigateTab: (tab: AppTab) => void;
  onNavigateMoreSection?: (section: MoreSection) => void;
  onSelectDestination: (destination: StudyDestination) => void;
  onOpenBooking: (country?: string) => void;
  onOpenExpo: () => void;
  onOpenDrawer?: () => void;
  onNavigateService?: (serviceId: string) => void;
  onNavigateTest?: (testId: string) => void;
  onNavigateEvent?: (eventId: string) => void;
  onNavigateServicesList?: () => void;
  onNavigateTestsList?: () => void;
  onNavigateEventsList?: () => void;
  onCompareUniversities?: (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => void;
}

export const HomeTab: React.FC<HomeTabProps> = ({
  onNavigateTab,
  onNavigateMoreSection,
  onSelectDestination,
  onOpenBooking,
  onOpenExpo,
  onOpenDrawer,
  onNavigateService,
  onNavigateTest,
  onNavigateEvent,
  onNavigateServicesList,
  onNavigateTestsList,
  onNavigateEventsList,
  onCompareUniversities,
}) => {
  const [activeStep, setActiveStep] = useState(0);
  const [searchQuery, setSearchQuery] = useState('');
  const [notificationsOpen, setNotificationsOpen] = useState(false);
  const [hasUnreadAlerts, setHasUnreadAlerts] = useState(NOTIFICATIONS.some(item => !item.isRead));
  const [selectedBlogPost, setSelectedBlogPost] = useState<BlogPost | null>(null);

  const filteredDestinations = STUDY_DESTINATIONS.filter(
    (d) =>
      d.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      d.popularCourses.some((c) => c.toLowerCase().includes(searchQuery.toLowerCase()))
  );

  return (
    <div className="pb-28 text-stone-800 bg-stone-50/50 min-h-screen">
      {/* Sticky Top Mobile App Header */}
      <header className="sticky top-0 z-30 bg-white/90 backdrop-blur-md px-4 py-2.5 border-b border-stone-200/70 shadow-2xs">
        {/* Brand Bar with Hamburger Menu */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            {/* Hamburger Menu Trigger */}
            <button
              onClick={onOpenDrawer}
              className="flex h-11 w-11 items-center justify-center rounded-xl border border-stone-200 bg-stone-100 text-stone-800 transition hover:bg-stone-200 active:bg-stone-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600"
              aria-label="Open navigation menu"
            >
              <Menu className="w-5 h-5 text-stone-800" />
            </button>

          </div>

          <div className="flex items-center space-x-1.5">
            {/* Notification Bell Icon */}
            <button
              onClick={() => setNotificationsOpen(true)}
              className="relative p-2 rounded-2xl bg-stone-100/80 hover:bg-stone-200/80 text-stone-700 hover:text-stone-900 transition border border-stone-200/70 shadow-2xs cursor-pointer focus:outline-none"
              title="Application Deadlines & Expo Alerts"
              aria-label="View notifications"
            >
              <Bell className="w-4 h-4 text-stone-700" />
              {hasUnreadAlerts && (
                <span className="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center">
                  <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                  <span className="relative inline-flex rounded-full h-3 w-3 bg-rose-600 text-[8px] font-black text-white items-center justify-center leading-none">
                    {NOTIFICATIONS.filter(item => !item.isRead).length}
                  </span>
                </span>
              )}
            </button>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I want to enquire about studying abroad for 2026 intake.')}`}
              target="_blank"
              rel="noreferrer"
              className="p-2 rounded-2xl bg-emerald-50/80 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/60 shadow-2xs"
              title="WhatsApp Indore Desk"
            >
              <MessageCircle className="w-4 h-4" />
            </a>

            <a
              href="tel:+919826666886"
              className="p-2 rounded-2xl bg-rose-50/80 text-rose-700 hover:bg-rose-100 transition border border-rose-200/60 shadow-2xs"
              title="Call Trans Globe Indore"
            >
              <Phone className="w-4 h-4" />
            </a>
          </div>
        </div>
      </header>

      {/* Main Home Tab Scrollable Content Body with App-Divided Sections */}
      <main className="pt-3 space-y-4 px-3 sm:px-4">
        {/* SECTION 1: HERO SLIDER & GLOBAL FUZZY SEARCH */}
        <section aria-label="Hero Spotlight and Global Search" className="space-y-3">
          <HeroSliderBanner
            onOpenBooking={onOpenBooking}
            onNavigateTab={onNavigateTab}
            onNavigateMoreSection={onNavigateMoreSection}
          />

          {/* Global Fuzzy Search Bar across Services, Tests, Events, Destinations */}
          <GlobalSearchBar
            placeholder="Search services, tests, events, universities, destinations (e.g. ASU, Visa, IELTS, Germany)..."
            onSelectService={onNavigateService}
            onSelectTest={onNavigateTest}
            onSelectEvent={onNavigateEvent}
            onSelectDestination={onSelectDestination}
            onCompareUniversities={onCompareUniversities}
            onOpenExpo={onOpenExpo}
            allowFilterSwitching={true}
            showSuggestions={true}
          />
        </section>

        {/* SIDE-BY-SIDE COMPARE UNIVERSITIES PROMINENT WIDGET */}
        <section aria-label="Compare Universities Feature" className="bg-stone-900 text-white rounded-3xl p-4 sm:p-5 border border-stone-800 shadow-sm relative overflow-hidden">
          <div className="flex items-start sm:items-center justify-between gap-3 flex-col sm:flex-row">
            <div className="space-y-1">
              <div className="flex items-center space-x-2 text-[11px] text-stone-400">
                <span className="font-bold text-rose-400 bg-rose-950/80 border border-rose-800/60 px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider">
                  Decision Tool
                </span>
                <span>·</span>
                <span>Side-by-Side Table</span>
              </div>
              <h3 className="font-extrabold text-sm sm:text-base text-white">
                Compare Universities for 2026 Intakes
              </h3>
              <p className="text-xs text-stone-300 font-normal">
                Analyze tuition fees, acceptance rates, and IELTS/GRE requirements side-by-side.
              </p>
            </div>

            <button
              type="button"
              onClick={() => onCompareUniversities?.(GLOBAL_UNIVERSITIES[0], GLOBAL_UNIVERSITIES[1])}
              className="px-4 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 shrink-0 cursor-pointer w-full sm:w-auto"
            >
              <ArrowLeftRight className="w-3.5 h-3.5" />
              <span>Launch University Comparison</span>
            </button>
          </div>
        </section>

        {/* SECTION 2: GLOBAL UNI EXPO 2026 SPOTLIGHT CARD */}
        <section aria-label="Global Uni Expo 2026">
          <div 
            onClick={onOpenExpo}
            className="bg-gradient-to-br from-red-950 via-red-900 to-slate-950 text-white rounded-3xl p-4 sm:p-5 shadow-lg border border-red-700/50 relative overflow-hidden cursor-pointer group"
          >
            <div className="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-amber-500/20 via-transparent to-transparent pointer-events-none" />
            
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <span className="text-[10px] font-black uppercase tracking-widest text-amber-300 bg-amber-400/20 px-2 py-0.5 rounded-full border border-amber-400/30 inline-block">
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
                <span className="text-[10px] uppercase font-bold text-amber-300">Event</span>
                <span className="text-base font-black text-white leading-none">{UPCOMING_EXPO.registrationOpen ? UPCOMING_EXPO.date : 'TBA'}</span>
              </div>
            </div>

            <div className="mt-3 pt-2.5 border-t border-red-700/60 flex items-center justify-between text-xs">
              <div className="flex items-center space-x-1.5 text-[11px] text-slate-300">
                <MapPin className="w-3.5 h-3.5 text-amber-400" />
                <span className="truncate max-w-[180px]">{UPCOMING_EXPO.location}</span>
              </div>

              <span className="text-amber-300 font-bold flex items-center gap-1 group-hover:translate-x-1 transition text-xs">
                <span>View event details</span>
                <ChevronRight className="w-3.5 h-3.5" />
              </span>
            </div>
          </div>
        </section>

        {/* SECTION 3: BENTO QUICK ACTIONS APPLICATION WIDGET */}
        <section aria-label="Quick Services and Tools" className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <div className="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                <Layers className="w-4 h-4" />
              </div>
              <div>
                <h3 className="font-black text-xs text-slate-900 uppercase tracking-wider">
                  Quick Services & Tools
                </h3>
                <span className="text-[10px] text-slate-500">Instant application utilities</span>
              </div>
            </div>
            <span className="text-[10px] bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded-full border border-red-200">
              100% Free For Students
            </span>
          </div>

          <div className="grid grid-cols-3 gap-2">
            <button
              onClick={() => onNavigateTab('explore')}
              className="p-3 rounded-2xl bg-slate-50 hover:bg-red-50/40 border border-slate-200 shadow-2xs hover:border-red-400 transition text-left flex flex-col justify-between group cursor-pointer"
            >
              <div className="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-110 transition">
                <GraduationCap className="w-4 h-4" />
              </div>
              <div className="mt-2.5">
                <span className="block font-bold text-xs text-slate-900 leading-tight">{GLOBAL_UNIVERSITIES.length} Universities</span>
                <span className="text-[10px] text-slate-500">Directory</span>
              </div>
            </button>

            <button
              onClick={() => onNavigateTab('evaluator')}
              className="p-3 rounded-2xl bg-slate-50 hover:bg-amber-50/40 border border-slate-200 shadow-2xs hover:border-amber-400 transition text-left flex flex-col justify-between group cursor-pointer"
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
              className="p-3 rounded-2xl bg-slate-50 hover:bg-blue-50/40 border border-slate-200 shadow-2xs hover:border-blue-400 transition text-left flex flex-col justify-between group cursor-pointer"
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
              onClick={() => {
                if (onNavigateService) {
                  onNavigateService('visa');
                } else {
                  onOpenBooking();
                }
              }}
              className="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/40 border border-slate-200 shadow-2xs hover:border-emerald-400 transition text-left flex flex-col justify-between group cursor-pointer"
            >
              <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                <ShieldCheck className="w-4 h-4" />
              </div>
              <div className="mt-2.5">
                <span className="block font-bold text-xs text-slate-900 leading-tight">Visa Filing</span>
                <span className="text-[10px] text-slate-500">Readiness guidance</span>
              </div>
            </button>

            <button
              onClick={() => {
                if (onNavigateService) {
                  onNavigateService('scholarships');
                } else {
                  onNavigateTab('explore');
                }
              }}
              className="p-3 rounded-2xl bg-slate-50 hover:bg-purple-50/40 border border-slate-200 shadow-2xs hover:border-purple-400 transition text-left flex flex-col justify-between group cursor-pointer"
            >
              <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
                <Award className="w-4 h-4" />
              </div>
              <div className="mt-2.5">
                <span className="block font-bold text-xs text-slate-900 leading-tight">Scholarships</span>
                <span className="text-[10px] text-slate-500">Scholarship guidance</span>
              </div>
            </button>

            <button
              onClick={() => {
                if (onNavigateTestsList) {
                  onNavigateTestsList();
                } else if (onNavigateMoreSection) {
                  onNavigateMoreSection('tests');
                } else {
                  onNavigateTab('more');
                }
              }}
              className="p-3 rounded-2xl bg-slate-50 hover:bg-rose-50/40 border border-slate-200 shadow-2xs hover:border-rose-400 transition text-left flex flex-col justify-between group cursor-pointer"
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
        </section>

        {/* SECTION 4: KEY STATS TICKER APPLICATION CARD */}
        <section aria-label="Key Stats Overview" className="bg-white rounded-3xl p-3.5 sm:p-4 border border-slate-200/90 shadow-xs">
          <div className="grid grid-cols-4 divide-x divide-slate-200 text-center">
            {GEIC_STATS.map((s, i) => (
              <div key={i} className="px-1 space-y-0.5">
                <span className="block font-black text-sm text-slate-900">{s.value}</span>
                <span className="block text-[9px] text-slate-500 font-semibold leading-tight">{s.label}</span>
              </div>
            ))}
          </div>
        </section>

        {/* SECTION 5: TRENDING STUDY DESTINATIONS CAROUSEL CARD */}
        <section aria-label="Study Destinations" className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2.5">
              <div className="w-8 h-8 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0 shadow-xs">
                <Compass className="w-4 h-4" />
              </div>
              <div>
                <div className="flex items-center space-x-2">
                  <h3 className="font-black text-sm text-slate-900 tracking-tight">
                    Trending Study Destinations
                  </h3>
                  <span className="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">
                    {STUDY_DESTINATIONS.length} Countries
                  </span>
                </div>
                <p className="text-[11px] text-slate-500 font-medium">
                  Compare post-study work rights, PR pathways and costs
                </p>
              </div>
            </div>

          </div>

          <div
            tabIndex={0}
            aria-label="Swipe to browse study destinations"
            className="flex overflow-x-auto gap-3.5 pb-2 pt-1 scrollbar-none snap-x snap-mandatory scroll-smooth -mx-1 px-1"
          >
            {filteredDestinations.map((dest) => (
              <div
                key={dest.id}
                onClick={() => onSelectDestination(dest)}
                className="min-w-[215px] max-w-[215px] bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition cursor-pointer shrink-0 snap-start flex flex-col justify-between group select-none"
              >
                <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
                  <img
                    src={dest.imageUrl}
                    alt={dest.name}
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
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

                  <div className="pt-1.5 border-t border-slate-200 flex items-center justify-between">
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

          <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
            <span className="text-[11px] text-slate-500 font-medium">
              Popular: Australia, UK, Germany, Canada, USA, Ireland
            </span>
            <button
              onClick={() => onNavigateTab('explore')}
              className="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer hover:underline"
            >
              <span>View All {STUDY_DESTINATIONS.length} Countries</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </section>

        {/* SECTION 6: SERVICES CARD SLIDER */}
        <section aria-label="Services Card Slider">
          <ServicesCardSlider
            onNavigateService={onNavigateService}
            onNavigateServicesList={onNavigateServicesList}
            onOpenBooking={onOpenBooking}
          />
        </section>

        {/* SECTION 7: UNIVERSITIES AUTO ROTATE SECTION */}
        <section aria-label="Universities Auto Rotate">
          <UniversitiesAutoRotate
            onNavigateTab={onNavigateTab}
            onOpenBooking={onOpenBooking}
          />
        </section>

        {/* SECTION 8: TEST CARD SLIDER */}
        <section aria-label="Test Prep Card Slider">
          <TestCardSlider
            onNavigateTest={onNavigateTest}
            onNavigateTestsList={onNavigateTestsList}
            onOpenBooking={onOpenBooking}
          />
        </section>

        {/* SECTION 9: EVENT CARD SLIDER */}
        <section aria-label="Summits and Events Card Slider">
          <EventCardSlider
            onNavigateEvent={onNavigateEvent}
            onNavigateEventsList={onNavigateEventsList}
            onOpenExpo={onOpenExpo}
            onOpenBooking={onOpenBooking}
          />
        </section>

        {/* SECTION 10: INTERACTIVE 4-STEP JOURNEY ABROAD */}
        <section aria-label="4-Step Journey Roadmap" className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2.5">
              <div className="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 shadow-xs">
                <PlaneTakeoff className="w-4 h-4" />
              </div>
              <div>
                <h3 className="font-black text-sm text-slate-900 tracking-tight">
                  Your 4-Step Journey Abroad
                </h3>
                <p className="text-[11px] text-slate-500 font-medium">
                  Proven step-by-step roadmap to your dream university
                </p>
              </div>
            </div>
            <span className="text-[10px] bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded-full border border-red-200">
              Step {activeStep + 1} of 4
            </span>
          </div>

          <div className="flex items-center justify-between relative px-3 pt-2">
            <div className="absolute top-6 left-8 right-8 h-0.5 bg-slate-200 -z-0" />
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

          <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
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
                className="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer"
              >
                <span>Get Step {activeStep + 1} Support</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
              {activeStep < 3 && (
                <button
                  onClick={() => setActiveStep((prev) => prev + 1)}
                  className="text-[11px] font-semibold text-slate-500 hover:text-slate-800 cursor-pointer"
                >
                  Next Step →
                </button>
              )}
            </div>
          </div>
        </section>

        {/* SECTION 11: BLOG CARD SLIDER */}
        <section aria-label="Study Abroad Guides and News Blog Slider">
          <BlogCardSlider
            onSelectBlog={(post) => setSelectedBlogPost(post)}
          />
        </section>

        {/* SECTION 12: REVIEW AUTO SLIDE SECTION */}
        <section aria-label="Student Reviews Auto Slide">
          <ReviewsAutoSlide />
        </section>

        {/* SECTION 13: INDORE OFFICE CONTACT HUB */}
        <section aria-label="Trans Globe Indore Office Hub">
          <div className="p-4 sm:p-5 rounded-3xl bg-slate-900 text-white space-y-3.5 shadow-xl border border-slate-800">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-2.5">
                <div className="w-9 h-9 rounded-2xl bg-red-600 flex items-center justify-center text-white font-bold shadow-md">
                  <MapPin className="w-5 h-5" />
                </div>
                <div>
                  <h4 className="font-extrabold text-sm text-white">Trans Globe Indore Center</h4>
                  <p className="text-[10px] text-slate-400">Headquarters for Central India</p>
                </div>
              </div>
              <span className="text-[10px] bg-emerald-500/20 text-emerald-400 px-2.5 py-0.5 rounded-full font-bold border border-emerald-500/30">
                Office hours: {GEIC_BRAND.officeHours}
              </span>
            </div>

            <div className="text-xs text-slate-300 space-y-1 bg-white/5 p-3 rounded-2xl border border-white/10">
              <p className="font-medium text-[11px] text-slate-200 leading-relaxed">{GEIC_BRAND.address}</p>
              <div className="pt-1 flex items-center justify-between text-[11px]">
                <span className="text-slate-400">Direct Helpline:</span>
                <strong className="text-white font-black">{GEIC_BRAND.displayPhone}</strong>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-2 pt-1">
              <a
                href="tel:+919826666886"
                className="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-3 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-sm"
              >
                <Phone className="w-3.5 h-3.5" />
                <span>Call Indore Office</span>
              </a>

              <a
                href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I want to visit the Indore office for study abroad counselling.')}`}
                target="_blank"
                rel="noreferrer"
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-3 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-sm"
              >
                <MessageCircle className="w-3.5 h-3.5" />
                <span>WhatsApp Desk</span>
              </a>
            </div>
          </div>
        </section>
      </main>

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

      {/* Blog Article Reader Modal */}
      <BlogDetailModal
        post={selectedBlogPost}
        onClose={() => setSelectedBlogPost(null)}
        onOpenBooking={() => onOpenBooking()}
      />
    </div>
  );
};
