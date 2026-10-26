import React, { useState } from 'react';
import { X, Calendar, Clock, MapPin, Video, CheckCircle2, User, Phone, Mail, Sparkles } from 'lucide-react';
import { GEIC_BRAND, STUDY_DESTINATIONS } from '../../data/geicData';
import { BookingAppointment } from '../../types';

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
  const [date, setDate] = useState('2026-10-12');
  const [timeSlot, setTimeSlot] = useState('11:30 AM');
  const [mode, setMode] = useState<'Indore Office (In-Person)' | 'Online (Video Call)'>('Indore Office (In-Person)');
  const [destination, setDestination] = useState(defaultDestination);
  const [studyLevel, setStudyLevel] = useState('Masters / PG');
  const [submitted, setSubmitted] = useState(false);

  if (!isOpen) return null;

  const timeSlots = [
    '10:30 AM', '11:30 AM', '1:00 PM', '2:30 PM', '4:00 PM', '5:30 PM'
  ];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim() || !phone.trim()) return;

    const appointment: BookingAppointment = {
      id: `appt_${Date.now()}`,
      name,
      email: email || 'student@geic.in',
      phone,
      date,
      timeSlot,
      mode,
      destinationInterest: destination,
      studyLevel,
    };

    onBookingSuccess(appointment);
    setSubmitted(true);
    setTimeout(() => {
      setSubmitted(false);
      onClose();
    }, 2500);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/70 backdrop-blur-xs p-0 sm:p-4">
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
            onClick={onClose}
            className="p-1 rounded-full text-slate-400 hover:text-white transition"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {submitted ? (
          <div className="p-8 text-center space-y-3 flex-1 flex flex-col items-center justify-center">
            <div className="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center animate-bounce">
              <CheckCircle2 className="w-8 h-8" />
            </div>
            <h4 className="text-base font-bold text-slate-900">Session Confirmed!</h4>
            <p className="text-xs text-slate-600 max-w-xs leading-relaxed">
              Your appointment on <strong>{date} at {timeSlot}</strong> has been scheduled. Our Indore advisor will connect with you on {phone}.
            </p>
            <span className="text-[10px] bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-semibold">
              Mode: {mode}
            </span>
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
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full p-2 rounded-xl border border-slate-200 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500"
              />
            </div>

            <div>
              <label className="block text-[11px] font-bold text-slate-700 mb-1.5">
                Available Time Slots (Indore IST)
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

            <div className="pt-2">
              <button
                type="submit"
                className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md cursor-pointer"
              >
                <Sparkles className="w-4 h-4" />
                <span>Confirm Free Session</span>
              </button>
              <p className="text-[10px] text-center text-slate-500 mt-2">
                🔒 No hidden consultancy fees. 100% Free guidance for students.
              </p>
            </div>
          </form>
        )}
      </div>
    </div>
  );
};
