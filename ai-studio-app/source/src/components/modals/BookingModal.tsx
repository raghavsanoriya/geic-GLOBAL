import React, { useState, useEffect } from 'react';
import { X, Calendar, Clock, MapPin, Video, CheckCircle2, User, Phone, Mail, Sparkles, PartyPopper, Check, ExternalLink } from 'lucide-react';
import confetti from 'canvas-confetti';
import { GEIC_BRAND, STUDY_DESTINATIONS } from '../../data/liveCatalog';
import { BookingAppointment } from '../../types';
import { submitEnquiry } from '../../api/laravel';

interface BookingModalProps {
  isOpen: boolean;
  onClose: () => void;
  defaultDestination?: string;
  onBookingSuccess: (appt: BookingAppointment) => void;
}

export const BookingModal: React.FC<BookingModalProps> = ({
  isOpen,
  onClose,
  defaultDestination = 'Australia',
  onBookingSuccess,
}) => {
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [email, setEmail] = useState('');
  const [date, setDate] = useState(new Date().toLocaleDateString('en-CA'));
  const [timeSlot, setTimeSlot] = useState('11:30 AM');
  const [mode, setMode] = useState<'Indore Office (In-Person)' | 'Online (Video Call)'>('Indore Office (In-Person)');
  const [destination, setDestination] = useState(defaultDestination);
  const [studyLevel, setStudyLevel] = useState('Masters / PG');
  const [submitted, setSubmitted] = useState(false);
  const [bookedDetails, setBookedDetails] = useState<BookingAppointment | null>(null);
  const [consent, setConsent] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [bookingError, setBookingError] = useState('');

  useEffect(() => {
    if (isOpen) {
      setDestination(defaultDestination);
      setSubmitted(false);
    }
  }, [isOpen, defaultDestination]);

  if (!isOpen) return null;

  const timeSlots = [
    '10:30 AM', '11:30 AM', '1:00 PM', '2:30 PM', '4:00 PM', '5:30 PM'
  ];

  const triggerSubtleConfetti = () => {
    try {
      // Left celebratory fountain
      confetti({
        particleCount: 45,
        angle: 60,
        spread: 55,
        origin: { x: 0.2, y: 0.7 },
        colors: ['#dc2626', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'],
        disableForReducedMotion: true,
        zIndex: 99999,
        scalar: 0.9,
      });

      // Right celebratory fountain
      confetti({
        particleCount: 45,
        angle: 120,
        spread: 55,
        origin: { x: 0.8, y: 0.7 },
        colors: ['#dc2626', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'],
        disableForReducedMotion: true,
        zIndex: 99999,
        scalar: 0.9,
      });

      // Subtle gentle center sparkles
      setTimeout(() => {
        confetti({
          particleCount: 30,
          spread: 85,
          origin: { x: 0.5, y: 0.45 },
          colors: ['#f59e0b', '#10b981', '#ef4444', '#6366f1'],
          disableForReducedMotion: true,
          zIndex: 99999,
          scalar: 0.8,
          gravity: 0.8,
        });
      }, 220);
    } catch {
      // Graceful fallback if confetti canvas cannot be initialized
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (submitting || !name.trim() || !phone.trim() || !consent) return;
    setSubmitting(true);
    setBookingError('');

    try {
      const response = await submitEnquiry({
        kind: 'counselling',
        fullName: name.trim(),
        phone: phone.trim(),
        email: email.trim(),
        destination,
        studyLevel,
        appointmentDate: date,
        timeSlot,
        meetingMode: mode,
        message: `Preferred appointment: ${date} at ${timeSlot}; ${mode}.`,
      });

      const appointment: BookingAppointment = {
        id: response.reference,
        name,
        email,
        phone,
        date,
        timeSlot,
        mode,
        destinationInterest: destination,
        studyLevel,
      };

      setBookedDetails(appointment);
      onBookingSuccess(appointment);
      setSubmitted(true);
      triggerSubtleConfetti();

    } catch (error) {
      setBookingError(error instanceof Error ? error.message : 'Could not book this session. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  const handleClose = () => {
    setSubmitted(false);
    onClose();
  };

  return (
    <div role="dialog" aria-modal="true" aria-label="Book Free Counselling" className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/70 backdrop-blur-xs p-0 sm:p-4">
      <div 
        className="bg-white w-full max-w-[440px] max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-200"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="p-4 bg-slate-900 text-white flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <div className="w-8 h-8 rounded-xl bg-red-600 flex items-center justify-center text-white">
              <Calendar className="w-4 h-4" />
            </div>
            <div>
              <h3 className="font-bold text-sm">Book Free Counselling</h3>
              <p className="text-[11px] text-slate-300">100% Free with Trans Globe Indore Expert</p>
            </div>
          </div>
          <button
            onClick={handleClose}
            className="p-1 rounded-full text-slate-400 hover:text-white transition"
            aria-label="Close modal"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {submitted ? (
          <div className="p-6 text-center space-y-4 flex-1 flex flex-col items-center justify-center animate-in zoom-in-95 duration-300">
            {/* Success Icon with Glow and Ring Pulse */}
            <div className="relative my-2">
              <div className="absolute -inset-3 rounded-full bg-emerald-400/20 blur-lg animate-pulse" />
              <div className="relative w-16 h-16 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 ring-4 ring-emerald-100">
                <CheckCircle2 className="w-9 h-9 animate-in zoom-in-75 duration-300" />
              </div>
              <div className="absolute -top-1 -right-1 bg-amber-400 text-slate-900 rounded-full p-1 shadow-xs animate-bounce">
                <PartyPopper className="w-3.5 h-3.5 text-amber-950" />
              </div>
            </div>

            <div className="space-y-1">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <Sparkles className="w-3 h-3 text-emerald-600" />
                <span>Request Received!</span>
              </div>
              <h4 className="text-lg font-bold text-slate-900">You're All Set!</h4>
              <p className="text-xs text-slate-600 max-w-xs mx-auto leading-relaxed">
                Thank you, <span className="font-semibold text-slate-900">{name || 'Student'}</span>! Your 1-on-1 overseas admission request has been received. Our counsellor will confirm the date and time with you.
              </p>
            </div>

            {/* Appointment Summary Card */}
            <div className="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-left space-y-2 text-xs">
              <div className="flex items-center justify-between pb-2 border-b border-slate-200">
                <span className="text-[11px] text-slate-500 font-medium">Destination & Target</span>
                <span className="font-bold text-slate-800 text-[11px]">
                  {destination} • {studyLevel}
                </span>
              </div>
              
              <div className="flex items-center justify-between">
                <span className="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                  <Calendar className="w-3.5 h-3.5 text-slate-400" /> Date & Time:
                </span>
                <span className="font-bold text-slate-900 text-[11px]">
                  {date} at {timeSlot}
                </span>
              </div>

              <div className="flex items-center justify-between">
                <span className="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                  {mode.includes('Office') ? (
                    <MapPin className="w-3.5 h-3.5 text-red-500" />
                  ) : (
                    <Video className="w-3.5 h-3.5 text-blue-500" />
                  )}
                  Consultation Mode:
                </span>
                <span className="font-semibold text-slate-800 text-[11px]">
                  {mode}
                </span>
              </div>

              <div className="pt-2 border-t border-slate-200 text-[10px] text-slate-500 flex items-center gap-1">
                <Check className="w-3 h-3 text-emerald-600 shrink-0" />
                <span>Request reference: <strong>{bookedDetails?.id}</strong>. Our counsellor will contact <strong>{phone}</strong>.</span>
              </div>
            </div>

            {/* Close Button and Auto-dismiss Indicator */}
            <div className="w-full space-y-2 pt-1">
              <button
                type="button"
                onClick={handleClose}
                className="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition cursor-pointer shadow-xs flex items-center justify-center space-x-1.5"
              >
                <span>Done / Back to App</span>
              </button>
              <div className="flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping" />
                <span>Keep your request reference for follow-up.</span>
              </div>
            </div>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs text-slate-800">
            {/* Consultation Mode Picker */}
            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1.5">
                Consultation Preference
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setMode('Indore Office (In-Person)')}
                  className={`p-2.5 rounded-xl border text-left flex items-center space-x-2 transition cursor-pointer ${
                    mode === 'Indore Office (In-Person)'
                      ? 'border-red-600 bg-red-50/70 text-red-900 font-bold ring-1 ring-red-500'
                      : 'border-slate-200 hover:bg-slate-50 text-slate-700'
                  }`}
                >
                  <MapPin className="w-4 h-4 text-red-600 shrink-0" />
                  <div className="leading-tight">
                    <span className="block text-[11px]">Indore Office</span>
                    <span className="text-[9px] text-slate-500 font-normal">Face-to-face</span>
                  </div>
                </button>

                <button
                  type="button"
                  onClick={() => setMode('Online (Video Call)')}
                  className={`p-2.5 rounded-xl border text-left flex items-center space-x-2 transition cursor-pointer ${
                    mode === 'Online (Video Call)'
                      ? 'border-red-600 bg-red-50/70 text-red-900 font-bold ring-1 ring-red-500'
                      : 'border-slate-200 hover:bg-slate-50 text-slate-700'
                  }`}
                >
                  <Video className="w-4 h-4 text-red-600 shrink-0" />
                  <div className="leading-tight">
                    <span className="block text-[11px]">Online Video</span>
                    <span className="text-[9px] text-slate-500 font-normal">Google Meet</span>
                  </div>
                </button>
              </div>
            </div>

            {/* Target Destination & Level */}
            <div className="grid grid-cols-2 gap-2">
              <div>
                <label className="block text-[11px] font-bold text-slate-700 mb-1">
                  Destination
                </label>
                <select
                  value={destination}
                  onChange={(e) => setDestination(e.target.value)}
                  className="w-full p-2 rounded-xl border border-slate-200 text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  {STUDY_DESTINATIONS.map((d) => (
                    <option key={d.id} value={d.name}>
                      {d.flagEmoji} {d.name}
                    </option>
                  ))}
                  <option value="Undecided / Multiple">🌍 Need Guidance</option>
                </select>
              </div>

              <div>
                <label className="block text-[11px] font-bold text-slate-700 mb-1">
                  Study Level
                </label>
                <select
                  value={studyLevel}
                  onChange={(e) => setStudyLevel(e.target.value)}
                  className="w-full p-2 rounded-xl border border-slate-200 text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  <option value="Masters / PG">Masters / PG Degree</option>
                  <option value="Bachelors / UG">Bachelors / UG Degree</option>
                  <option value="Diploma / Pathway">Diploma / Pathway</option>
                  <option value="PhD / Research">PhD / Research</option>
                </select>
              </div>
            </div>

            {/* Personal Details */}
            <div className="space-y-2">
              <div>
                <label className="block text-[11px] font-bold text-slate-700 mb-1">
                  Your Full Name *
                </label>
                <div className="relative">
                  <User className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" />
                  <input
                    type="text"
                    required
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="e.g. Raghav Sanoriya"
                    className="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-[11px] font-bold text-slate-700 mb-1">
                    Phone / WhatsApp *
                  </label>
                  <div className="relative">
                    <Phone className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" />
                    <input
                      type="tel"
                      required
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      placeholder="+91 98266..."
                      className="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-bold text-slate-700 mb-1">
                    Email Address
                  </label>
                  <div className="relative">
                    <Mail className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" />
                    <input
                      type="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="you@email.com"
                      className="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                </div>
              </div>
            </div>

            {/* Date & Time Slot */}
            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1">
                Preferred Date
              </label>
              <input
                type="date"
                required
                min={new Date().toLocaleDateString("en-CA")}
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full p-2 rounded-xl border border-slate-200 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
              />
            </div>

            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1.5">
                Preferred Time (Indore IST) — subject to confirmation
              </label>
              <div className="grid grid-cols-3 gap-1.5">
                {timeSlots.map((slot) => (
                  <button
                    key={slot}
                    type="button"
                    onClick={() => setTimeSlot(slot)}
                    className={`py-1.5 px-2 rounded-lg text-[11px] font-semibold transition cursor-pointer border ${
                      timeSlot === slot
                        ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                        : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                    }`}
                  >
                    {slot}
                  </button>
                ))}
              </div>
            </div>

            <label className="flex items-start gap-3 py-2"><input type="checkbox" required checked={consent} onChange={(e) => setConsent(e.target.checked)} /><span>I agree to be contacted by a GEIC counsellor about this request.</span></label>
            <div className="pt-2">
              <button
                type="submit"
                disabled={submitting || !consent}
                className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
              >
                <Sparkles className="w-4 h-4" />
                <span>{submitting ? "Sending request…" : "Request Free Session"}</span>
              </button>
              <p className="text-[10px] text-center text-slate-500 mt-2">
                🔒 No hidden consultancy fees. 100% Free guidance for students.
              </p>
            </div>
          {bookingError && <p role="alert" className="text-xs text-red-600 px-4 pb-2">{bookingError}</p>}
          </form>
        )}
      </div>
    </div>
  );
};
