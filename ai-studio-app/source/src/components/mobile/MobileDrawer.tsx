import React, { useState } from 'react';
import { 
  X, 
  Home, 
  Compass, 
  Bot, 
  ShieldCheck, 
  Ticket, 
  Briefcase, 
  Calendar, 
  BookOpen, 
  Award, 
  Phone, 
  Mail, 
  MessageCircle, 
  MapPin, 
  ChevronRight, 
  ChevronDown,
  ExternalLink,
  Sparkles,
  Heart,
  ArrowLeftRight
} from 'lucide-react';
import { AppTab, MoreSection } from '../../types';
import { GEIC_BRAND, GEIC_SERVICES, ENGLISH_TESTS, UPCOMING_EVENTS_LIST } from '../../data/liveCatalog';
import { useBookmarks } from '../../context/BookmarksContext';

interface MobileDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  activeTab: AppTab;
  onNavigateTab: (tab: AppTab) => void;
  onNavigateMoreSection: (section: MoreSection) => void;
  onOpenBooking: () => void;
  onNavigateService?: (serviceId: string) => void;
  onNavigateTest?: (testId: string) => void;
  onNavigateEvent?: (eventId: string) => void;
  onNavigateServicesList?: () => void;
  onNavigateTestsList?: () => void;
  onNavigateEventsList?: () => void;
  onCompareUniversities?: () => void;
}

export const MobileDrawer: React.FC<MobileDrawerProps> = ({
  isOpen,
  onClose,
  activeTab,
  onNavigateTab,
  onNavigateMoreSection,
  onOpenBooking,
  onNavigateService,
  onNavigateTest,
  onNavigateEvent,
  onNavigateServicesList,
  onNavigateTestsList,
  onNavigateEventsList,
  onCompareUniversities,
}) => {
  const { totalSavedCount } = useBookmarks();
  const [servicesExpanded, setServicesExpanded] = useState(false);
  const [testsExpanded, setTestsExpanded] = useState(false);
  const [eventsExpanded, setEventsExpanded] = useState(false);

  if (!isOpen) return null;

  const handleNav = (tab: AppTab) => {
    onNavigateTab(tab);
    onClose();
  };

  const handleMoreNav = (section: MoreSection) => {
    onNavigateMoreSection(section);
    onClose();
  };

  const handleServiceClick = (serviceId: string) => {
    if (onNavigateService) {
      onNavigateService(serviceId);
      onClose();
    } else {
      handleMoreNav('services');
    }
  };

  const handleTestClick = (testId: string) => {
    if (onNavigateTest) {
      onNavigateTest(testId);
      onClose();
    } else {
      handleMoreNav('tests');
    }
  };

  const handleEventClick = (eventId: string) => {
    if (onNavigateEvent) {
      onNavigateEvent(eventId);
      onClose();
    } else {
      handleMoreNav('events');
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex">
      {/* Backdrop */}
      <div
        onClick={onClose}
        className="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in"
      />

      {/* Drawer Container */}
      <div className="relative w-[320px] max-w-[85vw] bg-slate-900 text-white h-full shadow-2xl flex flex-col z-10 animate-in slide-in-from-left duration-300 border-r border-slate-800">
        {/* Drawer Header */}
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950">
          <div className="flex items-center space-x-2.5">
            <div className="w-9 h-9 rounded-2xl bg-gradient-to-tr from-red-600 via-rose-500 to-amber-500 flex items-center justify-center font-black text-white text-xs shadow-md">
              TG
            </div>
            <div>
              <div className="flex items-center space-x-1.5">
                <span className="font-extrabold text-sm text-white">Trans Globe</span>
                <span className="text-[9px] bg-red-600/30 text-red-300 font-black px-1.5 py-0.2 rounded border border-red-500/40">
                  INDORE
                </span>
              </div>
              <p className="text-[10px] text-slate-400">Managed by GEIC Global • Est. 1992</p>
            </div>
          </div>

          <button
            onClick={onClose}
            className="p-1.5 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition cursor-pointer"
            aria-label="Close menu"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Status Pill in Drawer */}
        <div className="px-4 py-2 bg-slate-800/50 border-b border-slate-800 flex items-center justify-between text-[11px]">
          <span className="text-slate-300 flex items-center gap-1.5 font-medium">
            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
            Intake 2026 Admissions Open
          </span>
          <span className="text-amber-400 font-bold text-[10px]">
            Study guidance
          </span>
        </div>

        {/* Navigation Links Scrollable Area */}
        <div className="flex-1 overflow-y-auto p-3 space-y-1 text-xs">
          <div className="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 px-3 pt-2 pb-1">
            Main Features
          </div>

          {[
            { id: 'home' as AppTab, label: 'Home Dashboard', icon: Home },
            { id: 'explore' as AppTab, label: 'Explore Universities', icon: Compass },
            { id: 'counsellor' as AppTab, label: 'AI Study Abroad Advisor', icon: Bot, badge: 'AI' },
            { id: 'evaluator' as AppTab, label: 'Profile Evaluator & Visa Meter', icon: ShieldCheck },
            { id: 'expo' as AppTab, label: 'Events & Counselling', icon: Ticket, badge: '' },
          ].map((item) => {
            const Icon = item.icon;
            const isActive = activeTab === item.id;

            return (
              <button
                key={item.id}
                onClick={() => handleNav(item.id)}
                className={`w-full flex items-center justify-between px-3 py-2.5 rounded-2xl font-semibold transition cursor-pointer ${
                  isActive
                    ? 'bg-red-600 text-white shadow-md'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                }`}
              >
                <div className="flex items-center space-x-2.5">
                  <Icon className={`w-4 h-4 ${isActive ? 'text-white' : 'text-slate-400'}`} />
                  <span>{item.label}</span>
                </div>
                {item.badge && (
                  <span
                    className={`text-[9px] font-black px-1.5 py-0.2 rounded-full ${
                      isActive ? 'bg-white text-red-600' : 'bg-red-600/30 text-red-300 border border-red-500/40'
                    }`}
                  >
                    {item.badge}
                  </span>
                )}
              </button>
            );
          })}

          {/* My Saved Bookmarks Navigation Item */}
          <button
            onClick={() => handleMoreNav('saved')}
            className="w-full flex items-center justify-between px-3 py-2.5 rounded-2xl font-semibold text-rose-300 hover:bg-rose-950/40 hover:text-white transition cursor-pointer border border-rose-900/30 bg-rose-950/20"
          >
            <div className="flex items-center space-x-2.5">
              <Heart className="w-4 h-4 text-rose-400 fill-rose-500" />
              <span className="font-bold text-white">My Saved Collection</span>
            </div>
            <span className="text-[10px] bg-rose-600 text-white font-black px-2 py-0.5 rounded-full shadow-xs">
              {totalSavedCount}
            </span>
          </button>

          {/* Compare Universities Navigation Item */}
          <button
            onClick={() => {
              onClose();
              if (onCompareUniversities) {
                onCompareUniversities();
              } else {
                window.location.hash = 'compare';
              }
            }}
            className="w-full flex items-center justify-between px-3 py-2.5 rounded-2xl font-semibold text-stone-200 hover:bg-stone-850 hover:text-white transition cursor-pointer border border-stone-800 bg-stone-900/60"
          >
            <div className="flex items-center space-x-2.5">
              <ArrowLeftRight className="w-4 h-4 text-rose-400" />
              <span className="font-bold text-white">Compare Universities</span>
            </div>
            <span className="text-[10px] bg-rose-950 text-rose-300 border border-rose-800/60 font-bold px-2 py-0.5 rounded-full">
              Side-by-Side
            </span>
          </button>

          {/* Section 1: Services (Expandable) */}
          <div className="pt-2">
            <div className="flex items-center justify-between px-3 py-1.5 text-slate-300 hover:text-white rounded-xl hover:bg-slate-800/60 transition cursor-pointer">
              <button
                onClick={() => {
                  if (onNavigateServicesList) {
                    onNavigateServicesList();
                    onClose();
                  } else {
                    handleMoreNav('services');
                  }
                }}
                className="flex items-center space-x-2.5 font-bold text-left flex-1"
              >
                <Briefcase className="w-4 h-4 text-red-500" />
                <span>Our Services</span>
              </button>
              <button
                onClick={() => setServicesExpanded(!servicesExpanded)}
                className="p-1 text-slate-400 hover:text-white rounded"
                aria-label="Toggle services list"
              >
                <ChevronDown className={`w-3.5 h-3.5 transition-transform ${servicesExpanded ? 'rotate-180' : ''}`} />
              </button>
            </div>

            {servicesExpanded && (
              <div className="pl-6 pr-2 py-1 space-y-0.5 border-l border-slate-800 ml-5 my-1 animate-in fade-in duration-200">
                {GEIC_SERVICES.map((srv) => (
                  <button
                    key={srv.id}
                    onClick={() => handleServiceClick(srv.id)}
                    className="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center justify-between"
                  >
                    <span className="truncate">{srv.title}</span>
                    <span className="text-[9px] text-slate-500 font-mono">0{srv.stepNumber}</span>
                  </button>
                ))}
                {onNavigateServicesList && (
                  <button
                    onClick={() => {
                      onNavigateServicesList();
                      onClose();
                    }}
                    className="w-full text-left px-2.5 py-1.5 text-[10px] font-bold text-red-400 hover:underline pt-1"
                  >
                    View All Services Hub →
                  </button>
                )}
              </div>
            )}
          </div>

          {/* Section 2: Tests Prep (Expandable) */}
          <div>
            <div className="flex items-center justify-between px-3 py-1.5 text-slate-300 hover:text-white rounded-xl hover:bg-slate-800/60 transition cursor-pointer">
              <button
                onClick={() => {
                  if (onNavigateTestsList) {
                    onNavigateTestsList();
                    onClose();
                  } else {
                    handleMoreNav('tests');
                  }
                }}
                className="flex items-center space-x-2.5 font-bold text-left flex-1"
              >
                <BookOpen className="w-4 h-4 text-amber-400" />
                <span>Test Prep (8 Exams)</span>
              </button>
              <button
                onClick={() => setTestsExpanded(!testsExpanded)}
                className="p-1 text-slate-400 hover:text-white rounded"
                aria-label="Toggle tests list"
              >
                <ChevronDown className={`w-3.5 h-3.5 transition-transform ${testsExpanded ? 'rotate-180' : ''}`} />
              </button>
            </div>

            {testsExpanded && (
              <div className="pl-6 pr-2 py-1 space-y-0.5 border-l border-slate-800 ml-5 my-1 animate-in fade-in duration-200">
                {ENGLISH_TESTS.map((t) => (
                  <button
                    key={t.id}
                    onClick={() => handleTestClick(t.id)}
                    className="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center justify-between"
                  >
                    <span className="truncate">{t.name}</span>
                    <span className="text-[9px] text-amber-400/80 font-mono">{t.code}</span>
                  </button>
                ))}
                {onNavigateTestsList && (
                  <button
                    onClick={() => {
                      onNavigateTestsList();
                      onClose();
                    }}
                    className="w-full text-left px-2.5 py-1.5 text-[10px] font-bold text-amber-400 hover:underline pt-1"
                  >
                    Compare Tests & Scoring
                  </button>
                )}
              </div>
            )}
          </div>

          {/* Section 3: Events (Expandable) */}
          <div>
            <div className="flex items-center justify-between px-3 py-1.5 text-slate-300 hover:text-white rounded-xl hover:bg-slate-800/60 transition cursor-pointer">
              <button
                onClick={() => {
                  if (onNavigateEventsList) {
                    onNavigateEventsList();
                    onClose();
                  } else {
                    handleMoreNav('events');
                  }
                }}
                className="flex items-center space-x-2.5 font-bold text-left flex-1"
              >
                <Calendar className="w-4 h-4 text-emerald-400" />
                <span>Upcoming Events (7)</span>
              </button>
              <button
                onClick={() => setEventsExpanded(!eventsExpanded)}
                className="p-1 text-slate-400 hover:text-white rounded"
                aria-label="Toggle events list"
              >
                <ChevronDown className={`w-3.5 h-3.5 transition-transform ${eventsExpanded ? 'rotate-180' : ''}`} />
              </button>
            </div>

            {eventsExpanded && (
              <div className="pl-6 pr-2 py-1 space-y-0.5 border-l border-slate-800 ml-5 my-1 animate-in fade-in duration-200">
                {UPCOMING_EVENTS_LIST.map((ev) => (
                  <button
                    key={ev.id}
                    onClick={() => handleEventClick(ev.id)}
                    className="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center justify-between"
                  >
                    <span className="truncate">{ev.title}</span>
                    <span className="text-[9px] text-emerald-400/80 shrink-0 ml-1">{ev.badge}</span>
                  </button>
                ))}
                {onNavigateEventsList && (
                  <button
                    onClick={() => {
                      onNavigateEventsList();
                      onClose();
                    }}
                    className="w-full text-left px-2.5 py-1.5 text-[10px] font-bold text-emerald-400 hover:underline pt-1"
                  >
                    View All 7 Events Timeline →
                  </button>
                )}
              </div>
            )}
          </div>

          <div className="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 px-3 pt-3 pb-1">
            Indore Desk & About
          </div>

          <button
            onClick={() => handleMoreNav('about')}
            className="w-full flex items-center justify-between px-3 py-2 rounded-2xl font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition cursor-pointer"
          >
            <div className="flex items-center space-x-2.5">
              <Award className="w-4 h-4 text-slate-400" />
              <span>About Trans Globe (34+ Yrs)</span>
            </div>
            <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
          </button>

          <button
            onClick={() => handleMoreNav('contact')}
            className="w-full flex items-center justify-between px-3 py-2 rounded-2xl font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition cursor-pointer"
          >
            <div className="flex items-center space-x-2.5">
              <MapPin className="w-4 h-4 text-slate-400" />
              <span>Indore Office & Location</span>
            </div>
            <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
          </button>
        </div>

        {/* Drawer Footer Actions */}
        <div className="p-3 bg-slate-950 border-t border-slate-800 space-y-2 shrink-0">
          <button
            onClick={() => {
              onClose();
              onOpenBooking();
            }}
            className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-md cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span>Book Free Indore Counselling</span>
          </button>

          <div className="grid grid-cols-2 gap-1.5">
            <a
              href="tel:+919826666886"
              className="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold py-2 rounded-xl text-[11px] flex items-center justify-center space-x-1 transition border border-slate-700 text-center"
            >
              <Phone className="w-3 h-3 text-red-400" />
              <span>+91 98266 66886</span>
            </a>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I would like to enquire about studying abroad.')}`}
              target="_blank"
              rel="noreferrer"
              className="bg-emerald-950/80 hover:bg-emerald-900/90 text-emerald-300 font-semibold py-2 rounded-xl text-[11px] flex items-center justify-center space-x-1 transition border border-emerald-800/60 text-center"
            >
              <MessageCircle className="w-3 h-3 text-emerald-400" />
              <span>WhatsApp Desk</span>
            </a>
          </div>

          <p className="text-[9px] text-slate-500 text-center pt-0.5">
            10:00 AM – 6:30 PM • Office 503 THE VIEW, Indore
          </p>
        </div>
      </div>
    </div>
  );
};
