import React from 'react';
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
  ExternalLink,
  Sparkles
} from 'lucide-react';
import { AppTab, MoreSection } from '../../types';
import { GEIC_BRAND } from '../../data/geicData';

interface MobileDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  activeTab: AppTab;
  onNavigateTab: (tab: AppTab) => void;
  onNavigateMoreSection: (section: MoreSection) => void;
  onOpenBooking: () => void;
}

export const MobileDrawer: React.FC<MobileDrawerProps> = ({
  isOpen,
  onClose,
  activeTab,
  onNavigateTab,
  onNavigateMoreSection,
  onOpenBooking,
}) => {
  if (!isOpen) return null;

  const handleNav = (tab: AppTab) => {
    onNavigateTab(tab);
    onClose();
  };

  const handleMoreNav = (section: MoreSection) => {
    onNavigateMoreSection(section);
    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 flex">
      {/* Backdrop */}
      <div
        onClick={onClose}
        className="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in"
      />

      {/* Drawer Container */}
      <div className="relative w-[310px] max-w-[85vw] bg-slate-900 text-white h-full shadow-2xl flex flex-col z-10 animate-in slide-in-from-left duration-300 border-r border-slate-800">
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
            98.7% Visa Rate
          </span>
        </div>

        {/* Navigation Links Scrollable Area */}
        <div className="flex-1 overflow-y-auto p-3 space-y-1 text-xs">
          <div className="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 px-3 pt-2 pb-1">
            Main Features
          </div>

          {[
            { id: 'home' as AppTab, label: 'Home Dashboard', icon: Home },
            { id: 'explore' as AppTab, label: 'Explore 800+ Universities', icon: Compass },
            { id: 'counsellor' as AppTab, label: 'AI Study Abroad Advisor', icon: Bot, badge: 'AI' },
            { id: 'evaluator' as AppTab, label: 'Profile Evaluator & Visa Meter', icon: ShieldCheck },
            { id: 'expo' as AppTab, label: 'Global Uni Expo 2026 Pass', icon: Ticket, badge: 'Oct 18' },
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

          <div className="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 px-3 pt-4 pb-1">
            Explore More & Services
          </div>

          {[
            { id: 'services' as MoreSection, label: 'Our 6 Core Services', icon: Briefcase },
            { id: 'events' as MoreSection, label: 'Upcoming Events & Workshops', icon: Calendar, badge: '4 New' },
            { id: 'tests' as MoreSection, label: 'Test Prep (IELTS, PTE, TOEFL)', icon: BookOpen },
            { id: 'about' as MoreSection, label: 'About Trans Globe (34+ Yrs)', icon: Award },
            { id: 'contact' as MoreSection, label: 'Indore Office & Contact Us', icon: MapPin },
          ].map((item) => {
            const Icon = item.icon;

            return (
              <button
                key={item.id}
                onClick={() => handleMoreNav(item.id)}
                className="w-full flex items-center justify-between px-3 py-2.5 rounded-2xl font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition cursor-pointer"
              >
                <div className="flex items-center space-x-2.5">
                  <Icon className="w-4 h-4 text-amber-400" />
                  <span>{item.label}</span>
                </div>
                {item.badge ? (
                  <span className="text-[9px] font-black px-1.5 py-0.2 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/40">
                    {item.badge}
                  </span>
                ) : (
                  <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
                )}
              </button>
            );
          })}
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
            10:00 AM – 7:00 PM • Industry House / Palasia, Indore
          </p>
        </div>
      </div>
    </div>
  );
};
