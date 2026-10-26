import React, { useState } from 'react';
import { 
  Ticket, 
  Calendar, 
  Clock, 
  MapPin, 
  Sparkles, 
  CheckCircle2, 
  QrCode, 
  Download, 
  Share2, 
  Phone, 
  Mail, 
  MessageCircle, 
  Building2, 
  HelpCircle,
  ChevronDown,
  UserCheck
} from 'lucide-react';
import { GEIC_BRAND, UPCOMING_EXPO, FAQS } from '../../data/liveCatalog';
import { BookingModal } from '../modals/BookingModal';
import { submitEnquiry } from '../../api/laravel';
import { BookingAppointment } from '../../types';

interface ExpoBookingTabProps {
  onOpenBooking: () => void;
}

export const ExpoBookingTab: React.FC<ExpoBookingTabProps> = ({ onOpenBooking }) => {
  const [passName, setPassName] = useState('');
  const [passPhone, setPassPhone] = useState('');
  const [passGenerated, setPassGenerated] = useState(false);
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);
  const [bookingModalOpen, setBookingModalOpen] = useState(false);
  const [bookedSuccess, setBookedSuccess] = useState<BookingAppointment | null>(null);

  const [passId, setPassId] = useState('');
  const [passError, setPassError] = useState('');
  const [passSubmitting, setPassSubmitting] = useState(false);
  const handleGeneratePass = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!passName.trim() || !passPhone.trim() || passSubmitting || !UPCOMING_EXPO.registrationOpen) return;
    setPassSubmitting(true);
    setPassError('');
    try {
      const response = await submitEnquiry({
        kind: 'expo',
        fullName: passName.trim(),
        phone: passPhone.trim(),
        referenceId: UPCOMING_EXPO.id,
        message: `VIP pass request for ${UPCOMING_EXPO.title} on ${UPCOMING_EXPO.date}.`,
      });
      setPassId(response.reference);
      setPassGenerated(true);
    } catch (error) {
      setPassError(error instanceof Error ? error.message : 'Could not register for the expo. Please try again.');
    } finally { setPassSubmitting(false); }
  };

  const toggleFaq = (idx: number) => {
    setOpenFaqIndex(openFaqIndex === idx ? null : idx);
  };

  return (
    <div className="pb-24 text-slate-800 space-y-4">
      {/* Top Header */}
      <div className="pt-2 px-4 space-y-2">
        <span className="text-[10px] font-bold text-red-600 uppercase tracking-wider">
          Event & Booking Center
        </span>
        <h2 className="text-xl font-black text-slate-900 leading-tight">
          Global Uni Expo & Indore Desk
        </h2>
        <p className="text-xs text-slate-500">
          View announced events or request a counselling appointment.
        </p>
      </div>

      {/* Expo 2026 Ticket Generator Card */}
      <div className="px-4">
        <div className="bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 text-white rounded-3xl p-4 shadow-xl border border-slate-800 space-y-3.5 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-32 h-32 bg-red-600/20 rounded-full blur-2xl pointer-events-none" />

          {/* Banner Details */}
          <div className="flex items-start justify-between">
            <div className="space-y-1">
              <span className="text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-400/30">
                Exclusive Indore Summit
              </span>
              <h3 className="text-base font-black text-white mt-1">
                {UPCOMING_EXPO.title}
              </h3>
              <p className="text-xs text-slate-300 font-medium">
                {UPCOMING_EXPO.subtitle}
              </p>
            </div>

            <div className="w-12 h-12 rounded-2xl bg-red-600/40 border border-red-500/50 flex flex-col items-center justify-center text-center shrink-0">
              <span className="text-[9px] uppercase font-bold text-red-300">FREE</span>
              <span className="text-xs font-black text-white">PASS</span>
            </div>
          </div>

          <div className="space-y-1.5 text-xs text-slate-300">
            <div className="flex items-center space-x-2">
              <Calendar className="w-3.5 h-3.5 text-red-400 shrink-0" />
              <span>{UPCOMING_EXPO.date} ({UPCOMING_EXPO.time})</span>
            </div>
            <div className="flex items-center space-x-2">
              <MapPin className="w-3.5 h-3.5 text-red-400 shrink-0" />
              <span className="truncate">{UPCOMING_EXPO.location}, {UPCOMING_EXPO.city}</span>
            </div>
          </div>

          {/* Highlights list */}
          <div className="bg-slate-900/80 p-3 rounded-2xl border border-slate-800 space-y-1.5 text-[11px] text-slate-300">
            {UPCOMING_EXPO.highlights.slice(0, 3).map((h, i) => (
              <div key={i} className="flex items-start space-x-2">
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400 shrink-0 mt-0.5" />
                <span>{h}</span>
              </div>
            ))}
          </div>

          {/* Interactive Digital Boarding Pass */}
          {passGenerated ? (
            <div className="bg-white text-slate-900 rounded-2xl p-4 shadow-lg border border-slate-200 space-y-3 animate-in zoom-in-95 duration-200">
              <div className="flex items-center justify-between border-b border-dashed border-slate-300 pb-3">
                <div>
                  <span className="text-[9px] font-black text-red-600 uppercase tracking-widest block">
                    VIP ADMISSION PASS
                  </span>
                  <h4 className="text-sm font-black text-slate-900">{passName}</h4>
                  <p className="text-[10px] text-slate-500">{passPhone}</p>
                </div>
                <div className="text-right">
                  <span className="text-[9px] text-slate-400 font-mono block">PASS ID</span>
                  <span className="font-mono text-xs font-black text-slate-900">{passId}</span>
                </div>
              </div>

              {/* Barcode / QR Section */}
              <div className="flex items-center justify-between py-1">
                <div className="space-y-1">
                  <span className="text-[10px] font-bold text-slate-800 block">
                    {UPCOMING_EXPO.location}
                  </span>
                  <span className="text-[10px] text-slate-500 block">
                    {UPCOMING_EXPO.date} · {UPCOMING_EXPO.venue}
                  </span>
                  <span className="inline-block bg-emerald-100 text-emerald-800 text-[9px] font-bold px-2 py-0.5 rounded-full">
                    Registration request received
                  </span>
                </div>

                <div className="w-16 h-16 bg-slate-100 rounded-xl p-1 border border-slate-200 flex flex-col items-center justify-center">
                  <Ticket aria-label="Registration reference" className="w-12 h-12 text-slate-900" />
                </div>
              </div>

              <div className="flex items-center space-x-2 pt-1">
                <button
                  onClick={async () => {
                    const text = `GEIC registration request\nReference: ${passId}\nName: ${passName}\nEvent: ${UPCOMING_EXPO.title}\nAttendance awaits team confirmation.`;
                    if (navigator.share) { try { await navigator.share({ title: 'Registration request', text }); } catch {} return; }
                    const url = URL.createObjectURL(new Blob([text], { type: 'text/plain' }));
                    const link = document.createElement('a'); link.href = url; link.download = `${passId}.txt`; link.click();
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                  }}
                  className="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Download Pass</span>
                </button>
                <button
                  onClick={() => setPassGenerated(false)}
                  className="px-3 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-50 cursor-pointer"
                >
                  Edit
                </button>
              </div>
            </div>
          ) : (
            <form onSubmit={handleGeneratePass} className="space-y-2 pt-1 text-xs">
              <div className="space-y-1.5">
                <input
                  type="text"
                  required
                  value={passName}
                  onChange={(e) => setPassName(e.target.value)}
                  placeholder="Your Full Name"
                  className="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500"
                />
                <input
                  type="tel"
                  required
                  value={passPhone}
                  onChange={(e) => setPassPhone(e.target.value)}
                  placeholder="WhatsApp / Phone Number"
                  className="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500"
                />
              </div>

              <button
                type="submit"
                disabled={passSubmitting || !UPCOMING_EXPO.registrationOpen}
                className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
              >
                <Sparkles className="w-4 h-4" />
                <span>{UPCOMING_EXPO.registrationOpen ? (passSubmitting ? 'Submitting…' : 'Request event registration') : 'Registration unavailable'}</span>
              {passError && <p role="alert" className="text-xs text-red-300">{passError}</p>}
              </button>
            </form>
          )}
        </div>
      </div>

      {/* 1-on-1 Consultation Booking Card */}
      <div className="px-4">
        <div className="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs space-y-3 text-xs">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <UserCheck className="w-4 h-4" />
              </div>
              <div>
                <h4 className="font-bold text-xs text-slate-900">Personal Counselling Session</h4>
                <p className="text-[10px] text-slate-500">In-person at Indore or online video call</p>
              </div>
            </div>
            <span className="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded-full">
              Free
            </span>
          </div>

          <p className="text-[11px] text-slate-600 leading-relaxed font-normal">
            Meet our senior study abroad advisors in Indore to discuss university shortlisting, Statement of Purpose (SOP) drafting, scholarship applications, and visa documentation.
          </p>

          <button
            onClick={() => setBookingModalOpen(true)}
            className="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer"
          >
            <Calendar className="w-4 h-4 text-amber-400" />
            <span>Select Date & Time Slot</span>
          </button>
        </div>
      </div>

      {/* FAQ Section */}
      <div className="px-4 space-y-2.5">
        <div className="flex items-center space-x-1.5">
          <HelpCircle className="w-4 h-4 text-red-600" />
          <h3 className="font-extrabold text-xs text-slate-900 uppercase tracking-wider">
            Frequently Asked Questions
          </h3>
        </div>

        <div className="space-y-2 text-xs">
          {FAQS.map((faq, idx) => {
            const isOpen = openFaqIndex === idx;

            return (
              <div
                key={idx}
                className="bg-white border border-slate-200 rounded-2xl overflow-hidden transition"
              >
                <button
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-3 text-left font-bold text-slate-900 flex items-center justify-between gap-2 hover:bg-slate-50 transition cursor-pointer"
                >
                  <span className="text-[11px]">{faq.question}</span>
                  <ChevronDown
                    className={`w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200 ${
                      isOpen ? 'rotate-180 text-red-600' : ''
                    }`}
                  />
                </button>
                {isOpen && (
                  <div className="p-3 pt-0 text-[11px] text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    {faq.answer}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>

      {/* Direct Contact Links */}
      <div className="px-4">
        <div className="bg-slate-50 border border-slate-200 rounded-3xl p-3.5 space-y-2.5 text-xs text-slate-700">
          <h4 className="font-bold text-slate-900">Trans Globe Indore Headquarters</h4>
          <p className="text-[11px] text-slate-500 leading-relaxed">{GEIC_BRAND.address}</p>

          <div className="flex items-center space-x-2 pt-1">
            <a
              href="tel:+919826666886"
              className="flex-1 bg-white border border-slate-200 text-slate-800 font-bold py-2 rounded-xl text-center flex items-center justify-center space-x-1 hover:bg-slate-100 transition shadow-2xs"
            >
              <Phone className="w-3.5 h-3.5 text-red-600" />
              <span>+91 98266 66886</span>
            </a>

            <a
              href="mailto:info@geic.in"
              className="flex-1 bg-white border border-slate-200 text-slate-800 font-bold py-2 rounded-xl text-center flex items-center justify-center space-x-1 hover:bg-slate-100 transition shadow-2xs"
            >
              <Mail className="w-3.5 h-3.5 text-red-600" />
              <span>info@geic.in</span>
            </a>
          </div>
        </div>
      </div>

      {/* Modal for consultation booking */}
      <BookingModal
        isOpen={bookingModalOpen}
        onClose={() => setBookingModalOpen(false)}
        onBookingSuccess={(appt) => {
          setBookedSuccess(appt);
        }}
      />
    </div>
  );
};
