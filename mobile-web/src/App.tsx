import React, { useState } from 'react';
import { MobileFrame } from './components/mobile/MobileFrame';
import { MobileDrawer } from './components/mobile/MobileDrawer';
import { HomeTab } from './components/tabs/HomeTab';
import { ExploreTab } from './components/tabs/ExploreTab';
import { AiCounsellorTab } from './components/tabs/AiCounsellorTab';
import { EvaluatorTab } from './components/tabs/EvaluatorTab';
import { ExpoBookingTab } from './components/tabs/ExpoBookingTab';
import { MoreTab } from './components/tabs/MoreTab';
import { DestinationDetailModal } from './components/modals/DestinationDetailModal';
import { BookingModal } from './components/modals/BookingModal';
import { AppTab, MoreSection, StudyDestination, BookingAppointment } from './types';
import { Check, Sparkles } from 'lucide-react';

export default function App() {
  const [activeTab, setActiveTab] = useState<AppTab>('home');
  const [activeMoreSection, setActiveMoreSection] = useState<MoreSection>('services');
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);
  const [selectedDestination, setSelectedDestination] = useState<StudyDestination | null>(null);
  const [bookingModalOpen, setBookingModalOpen] = useState(false);
  const [defaultBookingCountry, setDefaultBookingCountry] = useState<string>('Australia');
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3000);
  };

  const handleOpenBooking = (country?: string) => {
    if (country) {
      setDefaultBookingCountry(country);
    }
    setBookingModalOpen(true);
  };

  const handleBookingSuccess = (appt: BookingAppointment) => {
    showToast(`Appointment booked for ${appt.date} at ${appt.timeSlot}!`);
  };

  const handleNavigateMoreSection = (section: MoreSection) => {
    setActiveMoreSection(section);
    setActiveTab('more');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <>
      <MobileFrame
        activeTab={activeTab}
        onChangeTab={(tab) => {
          setActiveTab(tab);
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }}
        onOpenExpo={() => {
          setActiveTab('expo');
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }}
        onOpenBooking={() => {
          handleOpenBooking();
        }}
      >
        {/* Tab 1: Home Feed */}
        {activeTab === 'home' && (
          <HomeTab
            onNavigateTab={(tab) => {
              setActiveTab(tab);
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }}
            onNavigateMoreSection={handleNavigateMoreSection}
            onSelectDestination={(dest) => setSelectedDestination(dest)}
            onOpenBooking={handleOpenBooking}
            onOpenExpo={() => {
              setActiveTab('expo');
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }}
            onOpenDrawer={() => setIsDrawerOpen(true)}
          />
        )}

        {/* Tab 2: Explore Destinations & Universities */}
        {activeTab === 'explore' && (
          <ExploreTab
            onSelectDestination={(dest) => setSelectedDestination(dest)}
            onOpenBooking={handleOpenBooking}
          />
        )}

        {/* Tab 3: AI Advisor (Trans Globe Assistant) */}
        {activeTab === 'counsellor' && (
          <AiCounsellorTab onOpenBooking={() => handleOpenBooking()} />
        )}

        {/* Tab 4: Profile Evaluator & Visa Meter */}
        {activeTab === 'evaluator' && (
          <EvaluatorTab onOpenBooking={(country) => handleOpenBooking(country)} />
        )}

        {/* Tab 5: Expo 2026 Summit & Indore Office Booking */}
        {activeTab === 'expo' && (
          <ExpoBookingTab onOpenBooking={() => handleOpenBooking()} />
        )}

        {/* Tab 6: More (Services, Events, Tests, About, Contact Us) */}
        {activeTab === 'more' && (
          <MoreTab
            initialSection={activeMoreSection}
            onOpenBooking={handleOpenBooking}
            onOpenExpo={() => {
              setActiveTab('expo');
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }}
            onOpenDrawer={() => setIsDrawerOpen(true)}
          />
        )}

        {/* Destination Detailed Information Modal */}
        <DestinationDetailModal
          destination={selectedDestination}
          onClose={() => setSelectedDestination(null)}
          onBookConsultation={(country) => {
            setSelectedDestination(null);
            handleOpenBooking(country);
          }}
        />

        {/* General 1-on-1 Consultation Booking Modal */}
        <BookingModal
          isOpen={bookingModalOpen}
          onClose={() => setBookingModalOpen(false)}
          defaultDestination={defaultBookingCountry}
          onBookingSuccess={handleBookingSuccess}
        />

        {/* Toast Notification */}
        {toastMessage && (
          <div className="fixed top-16 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white px-4 py-2 rounded-2xl shadow-xl text-xs font-semibold flex items-center space-x-2 border border-slate-700 animate-in fade-in duration-200">
            <Check className="w-3.5 h-3.5 text-emerald-400" />
            <span>{toastMessage}</span>
          </div>
        )}
      </MobileFrame>

      {/* Global Slide-Over Hamburger Mobile Drawer */}
      <MobileDrawer
        isOpen={isDrawerOpen}
        onClose={() => setIsDrawerOpen(false)}
        activeTab={activeTab}
        onNavigateTab={(tab) => {
          setActiveTab(tab);
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }}
        onNavigateMoreSection={handleNavigateMoreSection}
        onOpenBooking={() => handleOpenBooking()}
      />
    </>
  );
}
