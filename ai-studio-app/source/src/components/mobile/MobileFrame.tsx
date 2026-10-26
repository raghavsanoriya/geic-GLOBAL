import React, { useState, useEffect } from 'react';
import { 
  Wifi, 
  Battery, 
  Smartphone, 
  Maximize2, 
  Minimize2, 
  Globe, 
  Share2, 
  RotateCw,
  Phone,
  Sparkles
} from 'lucide-react';
import { DynamicIsland } from './DynamicIsland';
import { BottomNav } from './BottomNav';
import { AppTab } from '../../types';
import { GEIC_BRAND } from '../../data/liveCatalog';

interface MobileFrameProps {
  children: React.ReactNode;
  activeTab: AppTab;
  onChangeTab: (tab: AppTab) => void;
  onOpenExpo: () => void;
  onOpenBooking: () => void;
}

const formatDeviceTime = () => {
  const now = new Date();
  const hours = now.getHours() % 12 || 12;
  const minutes = now.getMinutes().toString().padStart(2, '0');
  return `${hours}:${minutes}`;
};

export const MobileFrame: React.FC<MobileFrameProps> = ({
  children,
  activeTab,
  onChangeTab,
  onOpenExpo,
  onOpenBooking,
}) => {
  const [useDeviceFrame, setUseDeviceFrame] = useState(true);
  const [currentTime, setCurrentTime] = useState(formatDeviceTime);

  useEffect(() => {
    const updateTime = () => setCurrentTime(formatDeviceTime());
    updateTime();
    const timer = setInterval(updateTime, 1000);
    window.addEventListener('focus', updateTime);
    document.addEventListener('visibilitychange', updateTime);
    return () => {
      clearInterval(timer);
      window.removeEventListener('focus', updateTime);
      document.removeEventListener('visibilitychange', updateTime);
    };
  }, []);

  return (
    <div className="app-frame min-h-screen bg-slate-950 text-slate-100 flex flex-col items-center justify-start font-sans antialiased selection:bg-red-600 selection:text-white">
      {/* Top Preview Control Bar (Visible in browser view) */}
      <header className="app-preview-controls w-full bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-4 py-2 flex items-center justify-between z-50 shrink-0 text-xs">
        <div className="flex items-center space-x-2.5">
          <div className="w-6 h-6 rounded-lg bg-red-600 flex items-center justify-center font-black text-white text-xs">
            TG
          </div>
          <div>
            <span className="font-extrabold text-white text-xs">Trans Globe Indore</span>
            <span className="text-[10px] text-slate-400 ml-1.5 hidden sm:inline">
              GEIC Global Study Abroad App
            </span>
          </div>
        </div>

        <div className="flex items-center space-x-2">
          {/* Quick Call / Helpline CTA */}
          <a
            href="tel:+919826666886"
            className="hidden sm:flex items-center space-x-1.5 bg-red-600/20 text-red-400 border border-red-500/40 px-2.5 py-1 rounded-full hover:bg-red-600/30 transition text-[11px] font-semibold"
          >
            <Phone className="w-3 h-3 text-red-500" />
            <span>Indore Desk: +91 98266 66886</span>
          </a>

          {/* Toggle View Mode */}
          <button
            onClick={() => setUseDeviceFrame(!useDeviceFrame)}
            className="flex items-center space-x-1 bg-slate-800 hover:bg-slate-700 text-slate-200 px-2.5 py-1 rounded-xl transition border border-slate-700 cursor-pointer text-[11px] font-medium"
            title="Toggle between phone device mockup and full fluid screen"
          >
            {useDeviceFrame ? (
              <>
                <Maximize2 className="w-3 h-3 text-amber-400" />
                <span>Fluid Screen</span>
              </>
            ) : (
              <>
                <Smartphone className="w-3 h-3 text-amber-400" />
                <span>Mobile Frame</span>
              </>
            )}
          </button>
        </div>
      </header>

      {/* Main Container */}
      <div className={`app-frame-stage w-full flex-1 flex items-center justify-center ${useDeviceFrame ? 'p-2 sm:p-6' : 'p-0'}`}>
        <div
          className={`app-device-shell w-full transition-all duration-300 relative flex flex-col ${
            useDeviceFrame
              ? 'max-w-[420px] h-[860px] bg-white rounded-[50px] shadow-[0_25px_70px_rgba(0,0,0,0.8),0_0_0_12px_#1e293b,0_0_0_14px_#334155] border-4 border-slate-900 overflow-hidden ring-1 ring-white/10'
              : 'max-w-xl min-h-[calc(100vh-50px)] bg-white rounded-none sm:rounded-3xl shadow-2xl overflow-hidden'
          }`}
        >
          {/* Mobile Status Bar */}
          <div className="app-status-row bg-white px-6 pt-3 pb-1 flex items-center justify-between text-slate-900 select-none z-40 shrink-0">
            <span className="app-device-clock text-[12px] font-bold tracking-tight tabular-nums">{currentTime}</span>

            {/* Dynamic Island pill */}
            <div className="app-island-slot flex-1 flex justify-center min-w-0">
              <DynamicIsland onOpenExpo={onOpenExpo} onOpenBooking={onOpenBooking} />
            </div>

            <div className="app-device-icons flex items-center space-x-1.5 text-slate-900">
              <span className="text-[10px] font-extrabold tracking-tighter">5G</span>
              <Wifi className="w-3.5 h-3.5" />
              <div className="relative flex items-center">
                <Battery className="w-4 h-4 fill-slate-900" />
              </div>
            </div>
          </div>

          {/* Scrollable Viewport Content */}
          <main className="app-device-content flex-1 overflow-y-auto bg-slate-50 relative scroll-smooth">
            {children}
          </main>

          {/* Bottom Floating Navigation */}
          <BottomNav activeTab={activeTab} onChangeTab={onChangeTab} />

          {/* Home Indicator Bar (iPhone style) */}
          {useDeviceFrame && (
            <div className="absolute bottom-1 left-0 right-0 flex justify-center pointer-events-none z-50">
              <div className="w-32 h-1 bg-slate-900/40 rounded-full" />
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
