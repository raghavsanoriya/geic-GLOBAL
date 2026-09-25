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
  Briefcase, 
  Clock, 
  Calendar, 
  FileText, 
  ShieldCheck, 
  Check, 
  Globe,
  Compass,
  Award,
  Users
} from 'lucide-react';
import { GeicService } from '../../types';
import { GEIC_SERVICES, GEIC_BRAND } from '../../data/liveCatalog';
import { BookmarkButton } from '../common/BookmarkButton';

interface ServiceDetailPageProps {
  service: GeicService;
  onBack: () => void;
  onSelectService: (serviceId: string) => void;
  onBookConsultation: (serviceTitle: string) => void;
  onViewAllServices: () => void;
}

export const ServiceDetailPage: React.FC<ServiceDetailPageProps> = ({
  service,
  onBack,
  onSelectService,
  onBookConsultation,
  onViewAllServices,
}) => {
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);
  const [copiedLink, setCopiedLink] = useState(false);

  const handleShare = () => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(window.location.href);
      setCopiedLink(true);
      setTimeout(() => setCopiedLink(false), 2500);
    }
  };

  const otherServices = GEIC_SERVICES.filter((s) => s.id !== service.id);

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
          <span>All Services</span>
        </button>

        <div className="flex items-center space-x-2">
          <BookmarkButton
            type="service"
            id={service.id}
            title={service.title}
            size="sm"
            variant="pill"
          />

          <button
            onClick={handleShare}
            className="p-2 rounded-full text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition cursor-pointer"
            title="Share this service"
            aria-label="Share this service"
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
            title="Call Indore Desk"
            aria-label="Call Indore Desk"
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
              src={service.bannerImage}
              alt={service.title}
              referrerPolicy="no-referrer"
              className="w-full h-full object-cover opacity-90"
            />
            {/* Gentle, calming ambient gradient */}
            <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/65 to-stone-950/20" />

            {/* Quiet, unboxed metadata kicker */}
            <div className="absolute top-3.5 left-4 flex items-center space-x-2 text-[11px] font-medium text-stone-300">
              <span className="bg-white/20 backdrop-blur-md px-2.5 py-0.5 rounded-full text-white font-semibold">
                Step {service.stepNumber} of 8
              </span>
              <span>·</span>
              <span className="text-stone-200">{service.category}</span>
            </div>

            <div className="absolute bottom-4 left-4 right-4 text-white space-y-1.5">
              <h1 className="text-2xl font-black tracking-tight text-white leading-snug">
                {service.title}
              </h1>
              <p className="text-xs text-stone-300 font-normal leading-relaxed line-clamp-2">
                {service.tagline}
              </p>
            </div>
          </div>

          {/* Quick Highlight Bar */}
          <div className="bg-stone-950/95 px-4 py-3 border-t border-stone-800/80 flex items-center justify-between text-[11px] text-stone-300">
            <span className="flex items-center gap-1.5 text-emerald-400 font-medium">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
              100% Free Guidance for Indore Students
            </span>
            <span className="text-stone-400">
              In-Office & Online
            </span>
          </div>
        </div>
      </section>

      {/* Main Service Description & Audience Insight */}
      <section className="px-4 space-y-3">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div>
            <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block mb-1">
              Service Overview
            </span>
            <p className="text-xs text-stone-700 leading-relaxed font-normal whitespace-pre-line">
              {service.fullDesc}
            </p>
          </div>

          {/* Calm Who Needs This Card */}
          <div className="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 space-y-1.5">
            <div className="flex items-center space-x-1.5 text-stone-900 text-xs font-bold">
              <Users className="w-3.5 h-3.5 text-stone-600" />
              <span>Who is this service designed for?</span>
            </div>
            <p className="text-xs text-stone-600 leading-relaxed">
              {service.whoNeedsThis}
            </p>
          </div>

          {/* Supported Destinations */}
          <div className="space-y-2 pt-1 border-t border-stone-100">
            <span className="text-[11px] font-medium text-stone-500 block">
              Supported Study Destinations:
            </span>
            <div className="flex flex-wrap gap-1.5">
              {service.popularDestinations.map((dest, i) => (
                <span
                  key={i}
                  className="text-[11px] font-medium bg-stone-100/80 text-stone-700 px-2.5 py-1 rounded-xl flex items-center gap-1.5"
                >
                  <Globe className="w-3 h-3 text-stone-500" />
                  <span>{dest}</span>
                </span>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Verified Benefits */}
      <section className="px-4">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3.5">
          <div className="flex items-center justify-between">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
                The Trans Globe Advantage
              </span>
              <h3 className="text-sm font-black text-stone-900">
                Key Benefits for Applicants
              </h3>
            </div>
            <span className="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/70">
              Verified Excellence
            </span>
          </div>

          <div className="space-y-2.5 pt-1">
            {service.keyBenefits.map((benefit, i) => (
              <div key={i} className="flex items-start space-x-3 text-xs text-stone-700">
                <div className="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                  <Check className="w-3 h-3 stroke-[2.5]" />
                </div>
                <span className="leading-relaxed font-normal">{benefit}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Step-by-Step Structured Roadmap */}
      <section className="px-4">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-4">
          <div className="space-y-0.5">
            <span className="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">
              Step-by-Step Roadmap
            </span>
            <h3 className="text-sm font-black text-stone-900">
              How Our Process Works
            </h3>
            <p className="text-[11px] text-stone-500">
              Structured multi-stage guidance ensuring precision, speed, and peace of mind
            </p>
          </div>

          <div className="space-y-3 relative pl-1 pt-1">
            <div className="absolute left-4.5 top-4 bottom-4 w-px bg-stone-200" />

            {service.detailedProcess.map((step) => (
              <div key={step.step} className="relative z-10 flex items-start space-x-3.5 text-xs">
                <div className="w-7 h-7 rounded-full bg-stone-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs ring-4 ring-white">
                  {step.step}
                </div>
                <div className="flex-1 bg-stone-50/80 border border-stone-200/70 rounded-2xl p-3.5 space-y-1">
                  <h4 className="font-bold text-xs text-stone-900">
                    {step.title}
                  </h4>
                  <p className="text-[11px] text-stone-600 leading-relaxed font-normal">
                    {step.description}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Student Deliverables Checklist */}
      <section className="px-4">
        <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3.5">
          <div className="flex items-center space-x-2">
            <div className="w-7 h-7 rounded-xl bg-stone-100 text-stone-700 flex items-center justify-center">
              <FileText className="w-3.5 h-3.5" />
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                Official Deliverables Checklist
              </h3>
              <p className="text-[11px] text-stone-500 font-normal">
                Tangible documents, portals, and reports provided:
              </p>
            </div>
          </div>

          <div className="grid grid-cols-1 gap-2 pt-1">
            {service.deliverables.map((item, i) => (
              <div
                key={i}
                className="flex items-center space-x-2.5 p-3 rounded-2xl bg-stone-50 border border-stone-200/70 text-xs font-medium text-stone-800"
              >
                <div className="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                  <Check className="w-3 h-3 stroke-[2.5]" />
                </div>
                <span className="leading-snug">{item}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Frequently Asked Questions */}
      {service.faqs && service.faqs.length > 0 && (
        <section className="px-4">
          <div className="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs space-y-3">
            <div className="space-y-0.5">
              <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
                Common Inquiries
              </span>
              <h3 className="text-xs font-black uppercase tracking-wider text-stone-900">
                Frequently Asked Questions
              </h3>
            </div>

            <div className="space-y-2 pt-1">
              {service.faqs.map((faq, idx) => {
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

      {/* Dedicated Booking CTA Card with Soothing Warm Lighting */}
      <section className="px-4">
        <div className="p-5 rounded-3xl bg-gradient-to-br from-stone-900 via-stone-850 to-stone-900 text-white space-y-3.5 shadow-lg border border-stone-800 relative overflow-hidden">
          <div className="absolute -top-12 -right-12 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none" />

          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-amber-300">
              Personalized Consultation
            </span>
            <h3 className="text-lg font-black text-white leading-tight">
              Schedule 1-on-1 Guidance for {service.title}
            </h3>
            <p className="text-xs text-stone-300 leading-relaxed font-normal">
              Meet a dedicated country counselor at our Indore office or join online via video call. Free of charge.
            </p>
          </div>

          <button
            onClick={() => onBookConsultation(service.title)}
            className="w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-3 rounded-2xl text-xs flex items-center justify-center space-x-2 transition shadow-md cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span>Book Free Appointment</span>
          </button>

          <div className="grid grid-cols-2 gap-2 pt-1 text-xs">
            <a
              href="tel:+919826666886"
              className="bg-stone-800/90 hover:bg-stone-700 text-stone-200 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-stone-700 transition"
            >
              <Phone className="w-3.5 h-3.5 text-stone-400" />
              <span>Call Indore Desk</span>
            </a>

            <a
              href={`https://wa.me/919826666886?text=${encodeURIComponent(`Hi Trans Globe Indore! I want to inquire about ${service.title}.`)}`}
              target="_blank"
              rel="noreferrer"
              className="bg-emerald-950/70 hover:bg-emerald-900 text-emerald-300 py-2.5 rounded-2xl flex items-center justify-center space-x-1.5 border border-emerald-800/50 transition"
            >
              <MessageCircle className="w-3.5 h-3.5 text-emerald-400" />
              <span>WhatsApp</span>
            </a>
          </div>

          <p className="text-[10px] text-stone-400 text-center pt-1 font-medium">
            📍 {GEIC_BRAND.shortAddress}
          </p>
        </div>
      </section>

      {/* Explore Other Services Horizontal Cards */}
      <section className="space-y-3 pt-2">
        <div className="flex items-center justify-between px-4">
          <div className="space-y-0.5">
            <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">
              Step Sequence
            </span>
            <h3 className="font-extrabold text-xs text-stone-900">
              Other Trans Globe Services
            </h3>
          </div>
          <button
            onClick={onViewAllServices}
            className="text-xs font-semibold text-rose-600 flex items-center gap-0.5 hover:underline cursor-pointer"
          >
            <span>View All (8)</span>
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="flex overflow-x-auto gap-3 px-4 pb-2 scrollbar-none snap-x snap-mandatory">
          {otherServices.map((other) => (
            <div
              key={other.id}
              onClick={() => onSelectService(other.id)}
              className="min-w-[200px] max-w-[200px] bg-white rounded-3xl border border-stone-200/80 p-4 shadow-xs hover:border-rose-300 transition cursor-pointer shrink-0 snap-start flex flex-col justify-between group"
            >
              <div className="space-y-2">
                <span className="text-[10px] font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-full inline-block">
                  Step {other.stepNumber}
                </span>
                <h4 className="font-bold text-xs text-stone-900 group-hover:text-rose-600 transition-colors line-clamp-2">
                  {other.title}
                </h4>
                <p className="text-[11px] text-stone-500 line-clamp-2 leading-relaxed">
                  {other.shortDesc}
                </p>
              </div>

              <div className="pt-3 mt-3 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold text-rose-600">
                <span>Explore</span>
                <ChevronRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
};
