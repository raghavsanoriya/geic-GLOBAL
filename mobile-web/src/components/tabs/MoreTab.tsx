import React, { useState, useEffect } from 'react';
import { 
  Briefcase, 
  Calendar, 
  BookOpen, 
  Award, 
  Phone, 
  MapPin, 
  Mail, 
  Clock, 
  CheckCircle2, 
  ArrowRight, 
  Sparkles, 
  MessageCircle, 
  ExternalLink,
  Star,
  Users,
  Building,
  ShieldCheck,
  Plane,
  ChevronRight,
  GraduationCap,
  Send,
  Ticket,
  Menu
} from 'lucide-react';
import { 
  GEIC_BRAND, 
  GEIC_SERVICES, 
  ENGLISH_TESTS, 
  UPCOMING_EVENTS_LIST, 
  ABOUT_INFO, 
  GEIC_STATS 
} from '../../data/geicData';
import { MoreSection } from '../../types';

interface MoreTabProps {
  initialSection?: MoreSection;
  onOpenBooking: (country?: string) => void;
  onOpenExpo: () => void;
  onOpenDrawer?: () => void;
}

export const MoreTab: React.FC<MoreTabProps> = ({
  initialSection = 'services',
  onOpenBooking,
  onOpenExpo,
  onOpenDrawer,
}) => {
  const [activeSection, setActiveSection] = useState<MoreSection>(initialSection);
  const [selectedTest, setSelectedTest] = useState(ENGLISH_TESTS[0]);
  const [selectedService, setSelectedService] = useState(GEIC_SERVICES[0]);

  // Contact form state
  const [contactName, setContactName] = useState('');
  const [contactPhone, setContactPhone] = useState('');
  const [contactEmail, setContactEmail] = useState('');
  const [contactCountry, setContactCountry] = useState('Australia');
  const [contactMessage, setContactMessage] = useState('');
  const [contactSubmitted, setContactSubmitted] = useState(false);

  useEffect(() => {
    if (initialSection) {
      setActiveSection(initialSection);
    }
  }, [initialSection]);

  const handleContactSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!contactName.trim() || !contactPhone.trim()) return;
    setContactSubmitted(true);
  };

  const navItems: { id: MoreSection; label: string; icon: any }[] = [
    { id: 'services', label: 'Services', icon: Briefcase },
    { id: 'events', label: 'Events', icon: Calendar },
    { id: 'tests', label: 'Tests', icon: BookOpen },
    { id: 'about', label: 'About', icon: Award },
    { id: 'contact', label: 'Contact Us', icon: MapPin },
  ];

  return (
    <div className="pb-24 text-slate-800 space-y-4">
      {/* Top Header */}
      <div className="pt-2 px-4 space-y-2">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            {onOpenDrawer && (
              <button
                onClick={onOpenDrawer}
                className="p-2 -ml-1.5 text-slate-700 hover:text-slate-900 rounded-2xl hover:bg-slate-200/60 active:bg-slate-200 transition cursor-pointer"
                aria-label="Open navigation menu"
              >
                <Menu className="w-5 h-5 text-slate-800" />
              </button>
            )}
            <div>
              <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider block">
                Student Portal & Resources
              </span>
              <h2 className="text-xl font-black text-slate-900 leading-tight">
                Explore Trans Globe
              </h2>
            </div>
          </div>

          <div className="flex items-center space-x-1">
            <a
              href="tel:+919826666886"
              className="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition border border-red-200"
              title="Call Indore Desk"
            >
              <Phone className="w-4 h-4" />
            </a>
          </div>
        </div>
        <p className="text-xs text-slate-500">
          Services, upcoming seminars, test prep center, and Indore headquarters.
        </p>

        {/* Section Pill Switcher */}
        <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-1">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isSelected = activeSection === item.id;

            return (
              <button
                key={item.id}
                onClick={() => setActiveSection(item.id)}
                className={`flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0 ${
                  isSelected
                    ? 'bg-red-600 text-white shadow-xs'
                    : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'
                }`}
              >
                <Icon className="w-3.5 h-3.5" />
                <span>{item.label}</span>
              </button>
            );
          })}
        </div>
      </div>

      {/* ================= SECTION 1: SERVICES ================= */}
      {activeSection === 'services' && (
        <div className="px-4 space-y-3.5 animate-in fade-in duration-200">
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="font-extrabold text-sm text-slate-900">
                  Our 6 Core Global Services
                </h3>
                <p className="text-[11px] text-slate-500">
                  Comprehensive guidance from your first question to post-landing
                </p>
              </div>
              <span className="text-[10px] bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded-full border border-red-100">
                100% Free Guidance
              </span>
            </div>

            <div className="grid grid-cols-1 gap-3 pt-1">
              {GEIC_SERVICES.map((srv) => (
                <div
                  key={srv.id}
                  className="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-red-200 transition space-y-2 shadow-2xs"
                >
                  <div className="flex items-start justify-between">
                    <div className="flex items-center space-x-2">
                      <span className="text-xs font-black text-red-600 bg-red-100 px-2 py-0.5 rounded-lg">
                        {srv.stepNumber}
                      </span>
                      <h4 className="font-extrabold text-xs text-slate-900">{srv.title}</h4>
                    </div>

                    <button
                      onClick={() => onOpenBooking()}
                      className="text-[10px] font-bold text-red-600 hover:text-red-700 flex items-center space-x-0.5"
                    >
                      <span>Inquire</span>
                      <ChevronRight className="w-3 h-3" />
                    </button>
                  </div>

                  <p className="text-[11px] text-slate-600 leading-relaxed">{srv.fullDesc}</p>

                  <div className="pt-1 flex flex-wrap gap-1.5">
                    {srv.keyBenefits.map((b, i) => (
                      <span
                        key={i}
                        className="text-[10px] font-medium bg-white border border-slate-200 text-slate-700 px-2 py-0.5 rounded-md flex items-center gap-1"
                      >
                        <CheckCircle2 className="w-2.5 h-2.5 text-emerald-500" />
                        <span>{b}</span>
                      </span>
                    ))}
                  </div>
                </div>
              ))}
            </div>

            <button
              onClick={() => onOpenBooking()}
              className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-sm cursor-pointer mt-2"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Book In-Person Session in Indore</span>
            </button>
          </div>
        </div>
      )}

      {/* ================= SECTION 2: EVENTS ================= */}
      {activeSection === 'events' && (
        <div className="px-4 space-y-3.5 animate-in fade-in duration-200">
          <div className="flex items-center justify-between">
            <h3 className="font-extrabold text-xs text-slate-900 uppercase tracking-wider">
              Upcoming Summits & Workshops ({UPCOMING_EVENTS_LIST.length})
            </h3>
            <span className="text-[10px] text-red-600 font-bold">Free Passes</span>
          </div>

          <div className="space-y-3">
            {UPCOMING_EVENTS_LIST.map((event) => {
              const isExpo = event.id === 'expo-2026';

              return (
                <div
                  key={event.id}
                  className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs space-y-3"
                >
                  {/* Event Banner Image */}
                  <div className="relative h-32 w-full overflow-hidden bg-slate-900">
                    <img
                      src={event.bannerUrl}
                      alt={event.title}
                      referrerPolicy="no-referrer"
                      className="w-full h-full object-cover object-center"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

                    <div className="absolute top-2.5 left-2.5">
                      <span className="text-[9px] font-black uppercase tracking-wider bg-red-600 text-white px-2 py-0.5 rounded-md shadow-xs">
                        {event.badge}
                      </span>
                    </div>

                    <div className="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                      <h4 className="font-extrabold text-xs leading-tight line-clamp-1">
                        {event.title}
                      </h4>
                    </div>
                  </div>

                  <div className="p-3.5 pt-0 space-y-2 text-xs">
                    <div className="grid grid-cols-2 gap-2 text-[11px] text-slate-600">
                      <div className="flex items-center space-x-1.5">
                        <Calendar className="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <span className="truncate">{event.date}</span>
                      </div>
                      <div className="flex items-center space-x-1.5">
                        <Clock className="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <span className="truncate">{event.time}</span>
                      </div>
                    </div>

                    <div className="flex items-start space-x-1.5 text-[11px] text-slate-500">
                      <MapPin className="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" />
                      <span>{event.venue}, {event.city}</span>
                    </div>

                    <p className="text-[11px] text-slate-600 leading-relaxed font-normal pt-1">
                      {event.description}
                    </p>

                    <div className="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 space-y-1 text-[10px] text-slate-700">
                      {event.highlights.map((h, i) => (
                        <div key={i} className="flex items-center space-x-1.5">
                          <CheckCircle2 className="w-3 h-3 text-emerald-500 shrink-0" />
                          <span>{h}</span>
                        </div>
                      ))}
                    </div>

                    <button
                      onClick={() => {
                        if (isExpo) {
                          onOpenExpo();
                        } else {
                          onOpenBooking();
                        }
                      }}
                      className="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-xs cursor-pointer mt-1"
                    >
                      <Ticket className="w-3.5 h-3.5 text-amber-400" />
                      <span>{isExpo ? 'Get Expo 2026 VIP Pass' : 'Reserve Free Seat'}</span>
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* ================= SECTION 3: TESTS ================= */}
      {activeSection === 'tests' && (
        <div className="px-4 space-y-3.5 animate-in fade-in duration-200">
          {/* Test Selector Tabs */}
          <div className="flex items-center space-x-1.5 overflow-x-auto no-scrollbar pb-1">
            {ENGLISH_TESTS.map((test) => {
              const isSelected = selectedTest.id === test.id;

              return (
                <button
                  key={test.id}
                  onClick={() => setSelectedTest(test)}
                  className={`px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0 border ${
                    isSelected
                      ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                      : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                  }`}
                >
                  {test.code}
                </button>
              );
            })}
          </div>

          {/* Test Detail Card */}
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3.5">
            <div className="flex items-start justify-between border-b border-slate-100 pb-3">
              <div>
                <span className="text-[9px] font-black uppercase tracking-wider text-red-600 bg-red-50 px-2 py-0.5 rounded-md">
                  Official Test Prep
                </span>
                <h3 className="text-base font-black text-slate-900 mt-1">
                  {selectedTest.name}
                </h3>
                <p className="text-[11px] text-slate-500 font-medium">
                  {selectedTest.tagline}
                </p>
              </div>

              <div className="text-right">
                <span className="text-[9px] text-slate-400 font-bold block">TARGET</span>
                <span className="text-xs font-black text-emerald-600">{selectedTest.minTargetBand}</span>
              </div>
            </div>

            {/* Test Metrics Grid */}
            <div className="grid grid-cols-2 gap-2 text-xs">
              <div className="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 space-y-0.5">
                <span className="text-[10px] text-slate-400 font-bold">Duration</span>
                <p className="font-bold text-slate-800 text-[11px]">{selectedTest.duration}</p>
              </div>

              <div className="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 space-y-0.5">
                <span className="text-[10px] text-slate-400 font-bold">Scoring Scale</span>
                <p className="font-bold text-slate-800 text-[11px]">{selectedTest.scoringScale}</p>
              </div>
            </div>

            <div className="space-y-1">
              <span className="text-[11px] font-bold text-slate-700 block">Test Format & Structure:</span>
              <p className="text-[11px] text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 leading-relaxed">
                {selectedTest.testFormat}
              </p>
            </div>

            <div className="space-y-1.5">
              <span className="text-[11px] font-bold text-slate-700 block">Why Prep with Trans Globe Indore:</span>
              <div className="space-y-1">
                {selectedTest.transGlobeHighlights.map((h, i) => (
                  <div key={i} className="flex items-start space-x-2 text-[11px] text-slate-600">
                    <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                    <span>{h}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-red-50/70 border border-red-200/80 p-2.5 rounded-xl text-[11px] text-red-900 space-y-0.5">
              <span className="font-bold block">Available Batch Timings in Indore:</span>
              <p className="text-slate-600">{selectedTest.batchTimings}</p>
            </div>

            <div className="grid grid-cols-2 gap-2 pt-1">
              <button
                onClick={() => onOpenBooking()}
                className="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center space-x-1 shadow-sm cursor-pointer"
              >
                <span>Book Free Demo Class</span>
              </button>

              <button
                onClick={() => alert(`Registration for free ${selectedTest.code} mock test initiated. Trans Globe Indore will coordinate your time slot.`)}
                className="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center space-x-1 cursor-pointer"
              >
                <span>Free Mock Test</span>
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ================= SECTION 4: ABOUT ================= */}
      {activeSection === 'about' && (
        <div className="px-4 space-y-3.5 animate-in fade-in duration-200">
          <div className="bg-slate-950 text-white rounded-3xl p-4 shadow-xl border border-slate-800 space-y-3 relative overflow-hidden">
            <div className="absolute top-0 right-0 w-32 h-32 bg-red-600/20 rounded-full blur-2xl pointer-events-none" />

            <span className="text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-400/20 px-2 py-0.5 rounded-full border border-amber-400/30">
              Legacy Since {ABOUT_INFO.foundedYear}
            </span>

            <h3 className="text-lg font-black text-white leading-tight">
              {ABOUT_INFO.headline}
            </h3>

            <p className="text-xs text-slate-300 leading-relaxed font-normal whitespace-pre-line">
              {ABOUT_INFO.story}
            </p>

            {/* Quick Metrics */}
            <div className="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800">
              <div className="bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
                <span className="text-lg font-black text-amber-400">{ABOUT_INFO.placedCount}</span>
                <p className="text-[10px] text-slate-400">Global Placements</p>
              </div>

              <div className="bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
                <span className="text-lg font-black text-emerald-400">{ABOUT_INFO.universitiesCount}</span>
                <p className="text-[10px] text-slate-400">University Partners</p>
              </div>

              <div className="bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
                <span className="text-lg font-black text-rose-400">{ABOUT_INFO.visaRate}</span>
                <p className="text-[10px] text-slate-400">Visa Success Rate</p>
              </div>

              <div className="bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
                <span className="text-lg font-black text-blue-400">{ABOUT_INFO.scholarshipsTotal}</span>
                <p className="text-[10px] text-slate-400">Scholarships Secured</p>
              </div>
            </div>
          </div>

          {/* Accreditations */}
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3">
            <h4 className="font-extrabold text-xs text-slate-900 uppercase tracking-wider">
              Official Accreditations & Certifications
            </h4>

            <div className="space-y-2">
              {ABOUT_INFO.accreditations.map((acc, i) => (
                <div
                  key={i}
                  className="p-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between"
                >
                  <div>
                    <span className="font-bold text-xs text-slate-900 block">{acc.title}</span>
                    <span className="text-[10px] text-slate-500">{acc.desc}</span>
                  </div>
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                </div>
              ))}
            </div>
          </div>

          {/* Core Pillars */}
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3">
            <h4 className="font-extrabold text-xs text-slate-900 uppercase tracking-wider">
              Our Core Principles
            </h4>

            <div className="space-y-2">
              {ABOUT_INFO.pillars.map((pil, i) => (
                <div key={i} className="p-2.5 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-0.5">
                  <span className="font-bold text-xs text-slate-800 block">{pil.title}</span>
                  <p className="text-[11px] text-slate-500 leading-relaxed">{pil.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* ================= SECTION 5: CONTACT US ================= */}
      {activeSection === 'contact' && (
        <div className="px-4 space-y-3.5 animate-in fade-in duration-200">
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3 text-xs">
            <div>
              <span className="text-[9px] font-black uppercase tracking-wider text-red-600 bg-red-50 px-2 py-0.5 rounded-md">
                Indore Headquarters
              </span>
              <h3 className="text-base font-black text-slate-900 mt-1">
                Trans Globe Study Abroad
              </h3>
              <p className="text-[11px] text-slate-500">Managed by GEIC Global Consultants</p>
            </div>

            <div className="space-y-2 text-slate-700">
              <div className="flex items-start space-x-2.5 p-2 rounded-xl bg-slate-50">
                <MapPin className="w-4 h-4 text-red-600 shrink-0 mt-0.5" />
                <span className="text-[11px] leading-relaxed">{GEIC_BRAND.address}</span>
              </div>

              <div className="flex items-center space-x-2.5 p-2 rounded-xl bg-slate-50">
                <Phone className="w-4 h-4 text-red-600 shrink-0" />
                <a href="tel:+919826666886" className="text-[11px] font-bold hover:text-red-600">
                  +91 98266 66886 / +91 98260 66886
                </a>
              </div>

              <div className="flex items-center space-x-2.5 p-2 rounded-xl bg-slate-50">
                <Mail className="w-4 h-4 text-red-600 shrink-0" />
                <a href="mailto:info@geic.in" className="text-[11px] font-medium hover:text-red-600">
                  info@geic.in • admissions@transglobe.in
                </a>
              </div>

              <div className="flex items-center space-x-2.5 p-2 rounded-xl bg-slate-50">
                <Clock className="w-4 h-4 text-red-600 shrink-0" />
                <span className="text-[11px] text-slate-600">
                  Mon – Sat: 10:00 AM – 7:00 PM IST (Sunday Closed)
                </span>
              </div>
            </div>

            {/* Quick Action Dial Buttons */}
            <div className="grid grid-cols-2 gap-2 pt-1">
              <a
                href="tel:+919826666886"
                className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-xl text-center flex items-center justify-center space-x-1.5 transition shadow-xs"
              >
                <Phone className="w-3.5 h-3.5" />
                <span>Call Specialist</span>
              </a>

              <a
                href={`https://wa.me/919826666886?text=${encodeURIComponent('Hi Trans Globe Indore! I want to visit your office for study abroad counselling.')}`}
                target="_blank"
                rel="noreferrer"
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-center flex items-center justify-center space-x-1.5 transition shadow-xs"
              >
                <MessageCircle className="w-3.5 h-3.5" />
                <span>WhatsApp Us</span>
              </a>
            </div>
          </div>

          {/* Interactive Inquiry Form */}
          <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3 text-xs">
            <h4 className="font-extrabold text-xs text-slate-900 flex items-center gap-1.5">
              <Send className="w-3.5 h-3.5 text-red-600" />
              <span>Send Message to Indore Counsellors</span>
            </h4>

            {contactSubmitted ? (
              <div className="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-center space-y-1.5 animate-in zoom-in-95">
                <CheckCircle2 className="w-6 h-6 text-emerald-600 mx-auto" />
                <h5 className="font-bold text-emerald-900 text-xs">Inquiry Received!</h5>
                <p className="text-[10px] text-emerald-700">
                  Thank you {contactName}. A senior Trans Globe study advisor will connect with you on {contactPhone} within 4 business hours.
                </p>
                <button
                  onClick={() => setContactSubmitted(false)}
                  className="mt-2 text-[10px] font-bold text-emerald-800 underline"
                >
                  Send another inquiry
                </button>
              </div>
            ) : (
              <form onSubmit={handleContactSubmit} className="space-y-2.5">
                <div>
                  <label className="block text-[10px] font-bold text-slate-600 mb-0.5">Your Name</label>
                  <input
                    type="text"
                    required
                    value={contactName}
                    onChange={(e) => setContactName(e.target.value)}
                    placeholder="Raghav Sanoriya"
                    className="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                  />
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <div>
                    <label className="block text-[10px] font-bold text-slate-600 mb-0.5">Phone / WhatsApp</label>
                    <input
                      type="tel"
                      required
                      value={contactPhone}
                      onChange={(e) => setContactPhone(e.target.value)}
                      placeholder="+91 98266 66886"
                      className="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>

                  <div>
                    <label className="block text-[10px] font-bold text-slate-600 mb-0.5">Target Country</label>
                    <select
                      value={contactCountry}
                      onChange={(e) => setContactCountry(e.target.value)}
                      className="w-full p-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium"
                    >
                      <option value="Australia">Australia</option>
                      <option value="United Kingdom">United Kingdom</option>
                      <option value="Germany">Germany</option>
                      <option value="Canada">Canada</option>
                      <option value="United States">United States</option>
                      <option value="Ireland">Ireland</option>
                      <option value="Dubai">Dubai & UAE</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-slate-600 mb-0.5">Query or Course of Interest</label>
                  <textarea
                    rows={2}
                    value={contactMessage}
                    onChange={(e) => setContactMessage(e.target.value)}
                    placeholder="e.g. Inquiring about Germany public universities or IELTS weekend coaching..."
                    className="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                  />
                </div>

                <button
                  type="submit"
                  className="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition shadow-sm cursor-pointer"
                >
                  <Send className="w-3.5 h-3.5" />
                  <span>Submit Quick Inquiry</span>
                </button>
              </form>
            )}
          </div>
        </div>
      )}
    </div>
  );
};
