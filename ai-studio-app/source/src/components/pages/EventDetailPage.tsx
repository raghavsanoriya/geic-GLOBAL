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
  Calendar, 
  Clock, 
  Ticket, 
  Building, 
  QrCode, 
  Download, 
  Check, 
  Gift, 
  CheckCheck,
  Users
} from 'lucide-react';
import { UpcomingEventInfo } from '../../types';
import { UPCOMING_EVENTS_LIST, GEIC_BRAND } from '../../data/liveCatalog';
import { submitEnquiry } from '../../api/laravel';
import { BookmarkButton } from '../common/BookmarkButton';

interface EventDetailPageProps {
  event: UpcomingEventInfo;
  onBack: () => void;
  onSelectEvent: (eventId: string) => void;
  onOpenBooking: () => void;
  onViewAllEvents: () => void;
}

export const EventDetailPage: React.FC<EventDetailPageProps> = ({
  event,
  onBack,
  onSelectEvent,
  onOpenBooking,
  onViewAllEvents,
}) => {
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);
  const [passName, setPassName] = useState('');
  const [passPhone, setPassPhone] = useState('');
  const [passGenerated, setPassGenerated] = useState(false);
  const [passId, setPassId] = useState('');
  const [copiedLink, setCopiedLink] = useState(false);
  const [passError, setPassError] = useState('');
  const [passSubmitting, setPassSubmitting] = useState(false);

  const handleShare = () => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(window.location.href);
      setCopiedLink(true);
      setTimeout(() => setCopiedLink(false), 2500);
    }
  };

  const handleClaimPass = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!passName.trim() || !passPhone.trim() || passSubmitting || !event.registrationOpen) return;
    setPassSubmitting(true);
    setPassError('');

    try {
      const response = await submitEnquiry({
        kind: 'event',
        fullName: passName.trim(),
        phone: passPhone.trim(),
        referenceId: event.id,
        destination: event.city,
        message: `VIP pass request for ${event.title} on ${event.date}.`,
      });
      setPassId(response.reference);
      setPassGenerated(true);
    } catch (error) {
      setPassError(error instanceof Error ? error.message : 'Could not register for this event. Please try again.');
    } finally { setPassSubmitting(false); }
  };

  const otherEvents = UPCOMING_EVENTS_LIST.filter((ev) => ev.id !== event.id);

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
          <span>All Events</span>
        </button>

        <div className="flex items-center space-x-2">
          <BookmarkButton
            type="event"
            id={event.id}
            title={event.title}
            size="sm"
            variant="pill"
          />

          <button
            onClick={handleShare}
            className="p-2 rounded-full text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition cursor-pointer"
            title="Share this event"
            aria-label="Share this event"
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
            title="Call Event Helpline"
            aria-label="Call Event Helpline"
          >
            <Phone className="w-4 h-4" />
          </a>
        </div>
      </header>

      {/* Hero Banner Card with Soothing Aesthetic */}
      <section className="px-4">
        <div className="relative rounded-3xl overflow-hidden bg-stone-900 border border-stone-200/80 shadow-md">
          <div className="h-56 w-full relative">
            <img
              src={event.bannerUrl}
              alt={event.title}
              referrerPolicy="no-referrer"
              className="w-full h-full object-cover opacity-90"
            />
            {/* Soft, calming ambient gradient */}
            <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/65 to-stone-950/20" />

            <div className="absolute top-3.5 left-4 flex items-center space-x-2 text-[11px] font-medium text-stone-300">
              <span className="bg-white/20 backdrop-blur-md px-2.5 py-0.5 rounded-full text-white font-semibold">
                {event.badge}
              </span>
              <span>·</span>
              <span className="text-stone-200">{event.category}</span>
            </div>

            <div className="absolute top-3.5 right-4">
              <span className="text-[10px] font-bold text-emerald-300 bg-emerald-950/80 border border-emerald-500/40 px-3 py-1 rounded-full shadow-sm">
                100% Free Entry
              </span>
            </div>

            <div className="absolute bottom-4 left-4 right-4 text-white space-y-1.5">
              <h1 className="text-2xl font-black tracking-tight text-white leading-snug">
                {event.title}
              </h1>
              {event.subtitle && (
                <p className="text-xs text-amber-300 font-medium line-clamp-1">
                  {event.subtitle}
                </p>
              )}
            </div>
          </div>

          {/* Quick Date, Time & Venue Bar */}
          <div className="bg-stone-950/95 p-4 border-t border-stone-800/80 space-y-2.5 text-xs text-stone-300">
            <div className="grid grid-cols-2 gap-2 text-xs">
              <div className="flex items-center space-x-2">
                <Calendar className="w-4 h-4 text-rose-400 shrink-0" />
                <span className="font-semibold text-white truncate">{event.date}</span>
              </div>
              <div className="flex items-center space-x-2">
                <Clock className="w-4 h-4 text-rose-400 shrink-0" />
                <span className="font-semibold text-white truncate">{event.time}</span>
              </div>
            </div>

            <div className="flex items-start space-x-2 text-xs text-stone-400 pt-1.5 border-t border-stone-850">
              <MapPin className="w-4 h-4 text-rose-400 shrink-0 mt-0.5" />
              <span className="leading-relaxed">{event.venue}, {event.city}</span>
            </div>
          </div>
        </div>
      </section>

      {/* Description & Target Audience */}
      <section className="px-4 space-y-3">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div>
            <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block mb-1">
              Event Briefing
            </span>
            <p className="text-xs text-stone-700 leading-relaxed font-normal whitespace-pre-line">
              {event.description}
            </p>
          </div>

          {/* Target Audience */}
          <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200/80 space-y-1">
            <div className="flex items-center space-x-1.5 text-stone-900 text-xs font-bold">
              <Users className="w-3.5 h-3.5 text-stone-600" />
              <span>Who Should Attend?</span>
            </div>
            <p className="text-xs text-stone-600 leading-relaxed">
              {event.targetAudience}
            </p>
          </div>

          {/* Key Event Highlights */}
          <div className="space-y-2 pt-1 border-t border-stone-100">
            <span className="text-[11px] font-medium text-stone-500 block">
              Summit Highlights & Opportunities:
            </span>
            <div className="space-y-2">
              {event.highlights.map((hl, i) => (
                <div key={i} className="flex items-start space-x-3 text-xs text-stone-700">
                  <div className="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <Check className="w-3 h-3 stroke-[2.5]" />
                  </div>
                  <span className="leading-relaxed font-normal">{hl}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Participating Universities */}
      {event.participatingUniversitiesList && event.participatingUniversitiesList.length > 0 && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
            <div className="flex items-center justify-between">
              <div className="space-y-0.5">
                <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
                  Foreign Delegation
                </span>
                <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                  Participating Universities
                </h3>
              </div>
              <span className="text-[10px] bg-stone-100 text-stone-700 font-bold px-2.5 py-0.5 rounded-full">
                {event.participatingUniversitiesCount || event.participatingUniversitiesList.length}+ Global Unis
              </span>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
              {event.participatingUniversitiesList.map((uni, i) => (
                <div
                  key={i}
                  className="flex items-center space-x-2.5 p-3 rounded-2xl bg-stone-50 border border-stone-200/70 text-xs font-medium text-stone-800"
                >
                  <Building className="w-3.5 h-3.5 text-rose-600 shrink-0" />
                  <span className="truncate">{uni}</span>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Event Agenda & Schedule */}
      {event.agenda && event.agenda.length > 0 && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
                Session Timetable
              </span>
              <h3 className="text-sm font-black text-stone-900">
                Official Event Agenda
              </h3>
            </div>

            <div className="space-y-2.5 pt-1">
              {event.agenda.map((item, idx) => (
                <div
                  key={idx}
                  className="p-3.5 rounded-2xl bg-stone-50/80 border border-stone-200/70 space-y-1 text-xs"
                >
                  <div className="flex items-center justify-between text-xs">
                    <span className="font-bold text-rose-700 flex items-center gap-1.5">
                      <Clock className="w-3.5 h-3.5 text-rose-500" />
                      {item.time}
                    </span>
                    {item.speaker && (
                      <span className="text-[11px] text-stone-500 italic">
                        {item.speaker}
                      </span>
                    )}
                  </div>
                  <h4 className="font-bold text-xs text-stone-900">
                    {item.title}
                  </h4>
                  <p className="text-[11px] text-stone-600 leading-relaxed font-normal">
                    {item.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Registration Perks */}
      {event.registrationPerks && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3.5">
            <div className="flex items-center space-x-2">
              <div className="w-7 h-7 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <Gift className="w-4 h-4" />
              </div>
              <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                Perks for Registered Attendees
              </h3>
            </div>

            <div className="grid grid-cols-1 gap-2 pt-1">
              {event.registrationPerks.map((perk, i) => (
                <div
                  key={i}
                  className="flex items-center space-x-2.5 p-3 rounded-2xl bg-amber-50/40 border border-amber-200/50 text-xs font-medium text-stone-800"
                >
                  <Check className="w-3.5 h-3.5 text-amber-600 shrink-0 stroke-[2.5]" />
                  <span>{perk}</span>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Interactive Free VIP Pass Generator with Soothing Aesthetic */}
      <section className="px-4">
        <div className="bg-gradient-to-br from-stone-900 via-stone-850 to-stone-900 text-white rounded-3xl p-5 shadow-lg border border-stone-800 space-y-4 relative overflow-hidden">
          <div className="absolute -top-12 -right-12 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none" />

          <div className="flex items-start justify-between">
            <div className="space-y-1">
              <span className="text-[10px] font-bold uppercase tracking-wider text-amber-300">
                Digital Pass
              </span>
              <h3 className="text-lg font-black text-white leading-tight">
                Claim Fast-Track Entry Pass
              </h3>
              <p className="text-xs text-stone-300 leading-relaxed font-normal">
                Send your registration request. Attendance is confirmed by our team.
              </p>
            </div>

            <div className="w-10 h-10 rounded-2xl bg-stone-800/90 border border-stone-700/80 flex items-center justify-center shrink-0">
              <Ticket className="w-5 h-5 text-amber-300" />
            </div>
          </div>

          {passGenerated ? (
            <div className="bg-white text-stone-900 rounded-3xl p-5 space-y-3.5 shadow-xl border border-amber-300/40 animate-in zoom-in-95 duration-200">
              <div className="flex items-start justify-between border-b border-dashed border-stone-300 pb-3.5">
                <div>
                  <span className="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full inline-block">
                    CONFIRMED VIP TICKET
                  </span>
                  <h4 className="text-sm font-black text-stone-900 mt-1">
                    {event.title}
                  </h4>
                  <p className="text-xs text-stone-500 mt-0.5">{event.date} · {event.venue}</p>
                </div>
                <div className="p-2 bg-stone-900 text-white rounded-2xl shadow-xs">
                  <Ticket aria-label="Registration reference" className="w-12 h-12 text-slate-900" />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span className="text-[10px] text-stone-400 font-bold block uppercase tracking-wider">Attendee</span>
                  <span className="font-bold text-stone-900">{passName}</span>
                </div>
                <div>
                  <span className="text-[10px] text-stone-400 font-bold block uppercase tracking-wider">Pass ID</span>
                  <span className="font-mono text-xs font-bold text-rose-700">{passId}</span>
                </div>
              </div>

              <div className="p-2.5 bg-emerald-50 rounded-2xl text-[11px] text-emerald-800 font-medium flex items-center space-x-2 border border-emerald-200/60">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>Registration request received. Our team will confirm attendance.</span>
              </div>

              <button
                onClick={() => setPassGenerated(false)}
                className="w-full text-center text-xs font-bold text-stone-500 hover:text-stone-800 underline cursor-pointer"
              >
                Generate another attendee pass
              </button>
            </div>
          ) : (
            <form onSubmit={handleClaimPass} className="space-y-3 pt-1 text-xs">
              <div>
                <label className="text-[10px] font-bold text-stone-300 block mb-1">
                  Attendee Full Name *
                </label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Raghav Sanoriya"
                  value={passName}
                  onChange={(e) => setPassName(e.target.value)}
                  className="w-full bg-stone-950/80 border border-stone-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-rose-500 transition"
                />
              </div>

              <div>
                <label className="text-[10px] font-bold text-stone-300 block mb-1">
                  WhatsApp Contact *
                </label>
                <input
                  type="tel"
                  required
                  placeholder="+91 98266 66886"
                  value={passPhone}
                  onChange={(e) => setPassPhone(e.target.value)}
                  className="w-full bg-stone-950/80 border border-stone-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-rose-500 transition"
                />
              </div>

              <button
                type="submit"
                disabled={passSubmitting || !event.registrationOpen}
                className="w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-3 rounded-2xl text-xs flex items-center justify-center space-x-2 transition shadow-md cursor-pointer mt-1"
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>{event.registrationOpen ? 'Submit registration request' : 'Registration closed'}</span>
              </button>
              {passError && <p role="alert" className="text-xs text-rose-300">{passError}</p>}
            </form>
          )}

          <div className="grid grid-cols-2 gap-2 pt-1 text-xs">
            <a
              href="tel:+919826666886"
              className="bg-stone-800/90 hover:bg-stone-700 text-stone-200 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-stone-700 transition"
            >
              <Phone className="w-3.5 h-3.5 text-stone-400" />
              <span>Call Helpline</span>
            </a>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent(`Hi Trans Globe! I have an inquiry about attending ${event.title}.`)}`}
              target="_blank"
              rel="noreferrer"
              className="bg-emerald-950/70 hover:bg-emerald-900 text-emerald-300 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-emerald-800/50 transition"
            >
              <MessageCircle className="w-3.5 h-3.5 text-emerald-400" />
              <span>WhatsApp Desk</span>
            </a>
          </div>
        </div>
      </section>

      {/* Frequently Asked Questions */}
      {event.faqs && event.faqs.length > 0 && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
                Attendee Guidance
              </span>
              <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                Frequently Asked Questions
              </h3>
            </div>

            <div className="space-y-2 pt-1">
              {event.faqs.map((faq, idx) => {
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

      {/* Explore Other Events Carousel */}
      <section className="space-y-3 pt-2">
        <div className="flex items-center justify-between px-4">
          <div className="space-y-0.5">
            <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
              Upcoming Calendar
            </span>
            <h3 className="font-extrabold text-xs text-stone-900">
              Other Summits & Masterclasses
            </h3>
          </div>
          <button
            onClick={onViewAllEvents}
            className="text-xs font-semibold text-rose-600 flex items-center gap-0.5 hover:underline cursor-pointer"
          >
            <span>View All ({UPCOMING_EVENTS_LIST.length})</span>
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="flex overflow-x-auto gap-3 px-4 pb-2 scrollbar-none snap-x snap-mandatory">
          {otherEvents.map((other) => (
            <div
              key={other.id}
              onClick={() => onSelectEvent(other.id)}
              className="min-w-[200px] max-w-[200px] bg-white rounded-3xl border border-stone-200/80 p-4 shadow-xs hover:border-rose-300 transition cursor-pointer shrink-0 snap-start flex flex-col justify-between group"
            >
              <div className="space-y-2">
                <span className="text-[10px] font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-full inline-block">
                  {other.badge}
                </span>
                <h4 className="font-bold text-xs text-stone-900 group-hover:text-rose-600 transition-colors line-clamp-1">
                  {other.title}
                </h4>
                <p className="text-[11px] text-stone-500 line-clamp-2 leading-relaxed">
                  {other.subtitle || other.description}
                </p>
              </div>

              <div className="pt-3 mt-3 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold text-rose-600">
                <span>View Event</span>
                <ChevronRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
};
