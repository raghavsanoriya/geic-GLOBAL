import React, { useState, useEffect } from 'react';
import { MobileFrame } from './components/mobile/MobileFrame';
import { MobileDrawer } from './components/mobile/MobileDrawer';
import { HomeTab } from './components/tabs/HomeTab';
import { ExploreTab } from './components/tabs/ExploreTab';
import { AiCounsellorTab } from './components/tabs/AiCounsellorTab';
import { EvaluatorTab } from './components/tabs/EvaluatorTab';
import { ExpoBookingTab } from './components/tabs/ExpoBookingTab';
import { MoreTab } from './components/tabs/MoreTab';
import { ServiceDetailPage } from './components/pages/ServiceDetailPage';
import { TestDetailPage } from './components/pages/TestDetailPage';
import { EventDetailPage } from './components/pages/EventDetailPage';
import { ServicesIndexPage } from './components/pages/ServicesIndexPage';
import { TestsIndexPage } from './components/pages/TestsIndexPage';
import { EventsIndexPage } from './components/pages/EventsIndexPage';
import { DestinationDetailModal } from './components/modals/DestinationDetailModal';
import { BookingModal } from './components/modals/BookingModal';
import { CompareUniversitiesModal } from './components/modals/CompareUniversitiesModal';
import { 
  AppTab, 
  MoreSection, 
  StudyDestination, 
  BookingAppointment,
  ActivePageView 
} from './types';
import { 
  GEIC_SERVICES, 
  ENGLISH_TESTS, 
  UPCOMING_EVENTS_LIST 
} from './data/liveCatalog';
import { 
  UniversityComparisonData, 
  GLOBAL_UNIVERSITIES 
} from './data/liveCatalog';
import { Check } from 'lucide-react';
import { syncLaravelCatalog } from './api/laravel';

function LoadedApp() {
  const [activeTab, setActiveTab] = useState<AppTab>('home');
  const [activePageView, setActivePageView] = useState<ActivePageView>({ type: 'tab', tab: 'home' });
  const [activeMoreSection, setActiveMoreSection] = useState<MoreSection>('services');
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);
  const [selectedDestination, setSelectedDestination] = useState<StudyDestination | null>(null);
  const [bookingModalOpen, setBookingModalOpen] = useState(false);
  const [defaultBookingCountry, setDefaultBookingCountry] = useState<string>('Australia');
  const [toastMessage, setToastMessage] = useState<string | null>(null);
  const [compareModalOpen, setCompareModalOpen] = useState(false);
  const [compareUni1, setCompareUni1] = useState<UniversityComparisonData | null>(null);
  const [compareUni2, setCompareUni2] = useState<UniversityComparisonData | null>(null);
  // Hash Routing Listener for Direct Links & Browser Back/Forward
  useEffect(() => {
    const handleHashChange = () => {
      const hash = window.location.hash.replace(/^#/, '');
      if (!hash) {
        setActivePageView({ type: 'tab', tab: 'home' });
        setActiveTab('home');
        return;
      }

      if (hash.startsWith('service/')) {
        const srvId = hash.replace('service/', '');
        const exists = GEIC_SERVICES.find((s) => s.id === srvId);
        if (exists) {
          setActivePageView({ type: 'service', serviceId: srvId });
          setActiveTab('more');
          return;
        }
      }

      if (hash === 'services') {
        setActivePageView({ type: 'services-list' });
        setActiveTab('more');
        return;
      }

      if (hash.startsWith('test/')) {
        const tId = hash.replace('test/', '');
        const exists = ENGLISH_TESTS.find((t) => t.id === tId);
        if (exists) {
          setActivePageView({ type: 'test', testId: tId });
          setActiveTab('more');
          return;
        }
      }

      if (hash === 'tests') {
        setActivePageView({ type: 'tests-list' });
        setActiveTab('more');
        return;
      }

      if (hash.startsWith('event/')) {
        const evId = hash.replace('event/', '');
        const exists = UPCOMING_EVENTS_LIST.find((e) => e.id === evId);
        if (exists) {
          setActivePageView({ type: 'event', eventId: evId });
          setActiveTab('expo');
          return;
        }
      }

      if (hash === 'events') {
        setActivePageView({ type: 'events-list' });
        setActiveTab('more');
        return;
      }

      if (hash.startsWith('compare')) {
        const parts = hash.split('/');
        if (parts[1]) {
          const u1 = GLOBAL_UNIVERSITIES.find((u) => u.id === parts[1]);
          if (u1) setCompareUni1(u1);
        }
        if (parts[2]) {
          const u2 = GLOBAL_UNIVERSITIES.find((u) => u.id === parts[2]);
          if (u2) setCompareUni2(u2);
        }
        setCompareModalOpen(true);
        return;
      }

      if (hash === 'saved' || hash === 'more/saved') {
        setActiveMoreSection('saved');
        setActiveTab('more');
        setActivePageView({ type: 'tab', tab: 'more' });
        return;
      }

      const validTabs: AppTab[] = ['home', 'explore', 'counsellor', 'evaluator', 'expo', 'more'];
      if (validTabs.includes(hash as AppTab)) {
        setActiveTab(hash as AppTab);
        setActivePageView({ type: 'tab', tab: hash as AppTab });
      }
    };

    handleHashChange();
    window.addEventListener('hashchange', handleHashChange);
    return () => window.removeEventListener('hashchange', handleHashChange);
  }, []);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3000);
  };

  const handleOpenBooking = (countryOrService?: string) => {
    if (countryOrService) {
      setDefaultBookingCountry(countryOrService);
    }
    setBookingModalOpen(true);
  };

  const handleBookingSuccess = (appt: BookingAppointment) => {
    showToast(`Appointment booked for ${appt.date} at ${appt.timeSlot}!`);
  };

  const handleCompareUniversities = (uni1?: UniversityComparisonData, uni2?: UniversityComparisonData) => {
    const first = uni1 || GLOBAL_UNIVERSITIES[0];
    const second = uni2 || GLOBAL_UNIVERSITIES.find((u) => u.id !== first.id) || GLOBAL_UNIVERSITIES[1];
    setCompareUni1(first);
    setCompareUni2(second);
    setCompareModalOpen(true);
  };

  // Navigation Helpers
  const navigateToTab = (tab: AppTab) => {
    window.location.hash = tab === 'home' ? '' : tab;
    setActiveTab(tab);
    setActivePageView({ type: 'tab', tab });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToMoreSection = (section: MoreSection) => {
    setActiveMoreSection(section);
    navigateToTab('more');
  };

  const navigateToService = (serviceId: string) => {
    window.location.hash = `service/${serviceId}`;
    setActivePageView({ type: 'service', serviceId });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToTest = (testId: string) => {
    window.location.hash = `test/${testId}`;
    setActivePageView({ type: 'test', testId });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToEvent = (eventId: string) => {
    window.location.hash = `event/${eventId}`;
    setActivePageView({ type: 'event', eventId });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToServicesList = () => {
    window.location.hash = 'services';
    setActivePageView({ type: 'services-list' });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToTestsList = () => {
    window.location.hash = 'tests';
    setActivePageView({ type: 'tests-list' });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const navigateToEventsList = () => {
    window.location.hash = 'events';
    setActivePageView({ type: 'events-list' });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Find active items for detail pages
  const currentService = activePageView.type === 'service'
    ? GEIC_SERVICES.find((s) => s.id === activePageView.serviceId) || GEIC_SERVICES[0]
    : null;

  const currentTest = activePageView.type === 'test'
    ? ENGLISH_TESTS.find((t) => t.id === activePageView.testId) || ENGLISH_TESTS[0]
    : null;

  const currentEvent = activePageView.type === 'event'
    ? UPCOMING_EVENTS_LIST.find((e) => e.id === activePageView.eventId) || UPCOMING_EVENTS_LIST[0]
    : null;

  return (
    <>
      <MobileFrame
        activeTab={activeTab}
        onChangeTab={(tab) => {
          navigateToTab(tab);
        }}
        onOpenExpo={() => {
          navigateToTab('expo');
        }}
        onOpenBooking={() => {
          handleOpenBooking();
        }}
      >
        {/* VIEW 1: Dedicated Service Page */}
        {activePageView.type === 'service' && currentService && (
          <ServiceDetailPage
            service={currentService}
            onBack={() => {
              if (window.history.length > 1) {
                window.history.back();
              } else {
                navigateToServicesList();
              }
            }}
            onSelectService={navigateToService}
            onBookConsultation={(serviceTitle) => handleOpenBooking(serviceTitle)}
            onViewAllServices={navigateToServicesList}
          />
        )}

        {/* VIEW 2: Dedicated Test Page */}
        {activePageView.type === 'test' && currentTest && (
          <TestDetailPage
            test={currentTest}
            onBack={() => {
              if (window.history.length > 1) {
                window.history.back();
              } else {
                navigateToTestsList();
              }
            }}
            onSelectTest={navigateToTest}
            onBookConsultation={(testTitle) => handleOpenBooking(testTitle)}
            onViewAllTests={navigateToTestsList}
          />
        )}

        {/* VIEW 3: Dedicated Event Page */}
        {activePageView.type === 'event' && currentEvent && (
          <EventDetailPage
            event={currentEvent}
            onBack={() => {
              if (window.history.length > 1) {
                window.history.back();
              } else {
                navigateToEventsList();
              }
            }}
            onSelectEvent={navigateToEvent}
            onOpenBooking={() => handleOpenBooking()}
            onViewAllEvents={navigateToEventsList}
          />
        )}

        {/* VIEW 4: All Services Hub Directory */}
        {activePageView.type === 'services-list' && (
          <ServicesIndexPage
            onBack={() => navigateToTab('home')}
            onSelectService={navigateToService}
            onSelectTest={navigateToTest}
            onSelectEvent={navigateToEvent}
            onSelectDestination={(dest) => setSelectedDestination(dest)}
            onCompareUniversities={handleCompareUniversities}
            onOpenExpo={() => navigateToTab('expo')}
            onOpenBooking={handleOpenBooking}
            onOpenDrawer={() => setIsDrawerOpen(true)}
          />
        )}

        {/* VIEW 5: All Tests Hub Directory */}
        {activePageView.type === 'tests-list' && (
          <TestsIndexPage
            onBack={() => navigateToTab('home')}
            onSelectTest={navigateToTest}
            onSelectService={navigateToService}
            onSelectEvent={navigateToEvent}
            onSelectDestination={(dest) => setSelectedDestination(dest)}
            onCompareUniversities={handleCompareUniversities}
            onOpenExpo={() => navigateToTab('expo')}
            onBookConsultation={handleOpenBooking}
            onOpenDrawer={() => setIsDrawerOpen(true)}
          />
        )}

        {/* VIEW 6: All Events Hub Directory */}
        {activePageView.type === 'events-list' && (
          <EventsIndexPage
            onBack={() => navigateToTab('home')}
            onSelectEvent={navigateToEvent}
            onSelectService={navigateToService}
            onSelectTest={navigateToTest}
            onSelectDestination={(dest) => setSelectedDestination(dest)}
            onCompareUniversities={handleCompareUniversities}
            onOpenExpo={() => navigateToTab('expo')}
            onOpenBooking={handleOpenBooking}
            onOpenDrawer={() => setIsDrawerOpen(true)}
          />
        )}

        {/* STANDARD APP TABS (Active when type === 'tab') */}
        {activePageView.type === 'tab' && (
          <>
            {/* Tab 1: Home Feed */}
            {activeTab === 'home' && (
              <HomeTab
                onNavigateTab={navigateToTab}
                onNavigateMoreSection={navigateToMoreSection}
                onSelectDestination={(dest) => setSelectedDestination(dest)}
                onOpenBooking={handleOpenBooking}
                onOpenExpo={() => navigateToTab('expo')}
                onOpenDrawer={() => setIsDrawerOpen(true)}
                onNavigateService={navigateToService}
                onNavigateTest={navigateToTest}
                onNavigateEvent={navigateToEvent}
                onNavigateServicesList={navigateToServicesList}
                onNavigateTestsList={navigateToTestsList}
                onNavigateEventsList={navigateToEventsList}
                onCompareUniversities={handleCompareUniversities}
              />
            )}

            {/* Tab 2: Explore Destinations & Universities */}
            {activeTab === 'explore' && (
              <ExploreTab
                onSelectDestination={(dest) => setSelectedDestination(dest)}
                onOpenBooking={handleOpenBooking}
                onCompareUniversities={handleCompareUniversities}
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
                onOpenExpo={() => navigateToTab('expo')}
                onOpenDrawer={() => setIsDrawerOpen(true)}
                onSelectService={navigateToService}
                onSelectTest={navigateToTest}
                onSelectEvent={navigateToEvent}
                onViewAllServices={navigateToServicesList}
                onViewAllTests={navigateToTestsList}
                onViewAllEvents={navigateToEventsList}
              />
            )}
          </>
        )}

        {/* Side-by-Side Compare Universities Modal */}
        <CompareUniversitiesModal
          isOpen={compareModalOpen}
          onClose={() => setCompareModalOpen(false)}
          university1={compareUni1}
          university2={compareUni2}
          onSelectUniversity1={(uni) => setCompareUni1(uni)}
          onSelectUniversity2={(uni) => setCompareUni2(uni)}
          onBookConsultation={(uniName) => {
            setCompareModalOpen(false);
            handleOpenBooking(uniName);
          }}
        />

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
          navigateToTab(tab);
        }}
        onNavigateMoreSection={navigateToMoreSection}
        onOpenBooking={() => handleOpenBooking()}
        onNavigateService={navigateToService}
        onNavigateTest={navigateToTest}
        onNavigateEvent={navigateToEvent}
        onNavigateServicesList={navigateToServicesList}
        onNavigateTestsList={navigateToTestsList}
        onNavigateEventsList={navigateToEventsList}
        onCompareUniversities={handleCompareUniversities}
      />
    </>
  );
}

export default function App() {
  const [status, setStatus] = useState<'loading' | 'ready' | 'error'>('loading');
  const [error, setError] = useState('');
  const [attempt, setAttempt] = useState(0);
  useEffect(() => {
    let active = true;
    setStatus('loading');
    syncLaravelCatalog().then(() => {
      if (active) setStatus('ready');
    }).catch((reason) => {
      if (active) { setError(reason instanceof Error ? reason.message : 'Connection unavailable'); setStatus('error'); }
    });
    return () => { active = false; };
  }, [attempt]);
  if (status === 'ready') return <LoadedApp />;
  return <main className="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div className="max-w-sm rounded-xl bg-white border border-slate-200 p-6 space-y-4" role={status === 'error' ? 'alert' : 'status'}>
      <h1 className="text-xl font-bold">{status === 'loading' ? 'Loading your study options' : 'Unable to load current information'}</h1>
      <p className="text-sm text-slate-600">{status === 'loading' ? 'Connecting to GEIC.' : error}</p>
      {status === 'error' && <button className="w-full rounded-xl bg-red-600 text-white p-3 font-bold" onClick={() => setAttempt(value => value + 1)}>Try again</button>}
    </div>
  </main>;
}
