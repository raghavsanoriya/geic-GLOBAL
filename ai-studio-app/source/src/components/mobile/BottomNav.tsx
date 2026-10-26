import React from 'react';
import { Home, Compass, MessageSquareCode, ShieldCheck, MoreHorizontal, Grid } from 'lucide-react';
import { AppTab } from '../../types';

interface BottomNavProps {
  activeTab: AppTab;
  onChangeTab: (tab: AppTab) => void;
  moreBadge?: boolean;
}

export const BottomNav: React.FC<BottomNavProps> = ({ activeTab, onChangeTab, moreBadge = false }) => {
  const tabs = [
    { id: 'home' as AppTab, label: 'Home', icon: Home },
    { id: 'explore' as AppTab, label: 'Explore', icon: Compass },
    { id: 'counsellor' as AppTab, label: 'AI Advisor', icon: MessageSquareCode, isSpecial: true },
    { id: 'evaluator' as AppTab, label: 'Evaluate', icon: ShieldCheck },
    { id: 'more' as AppTab, label: 'More', icon: Grid, badge: moreBadge ? '•' : undefined },
  ];

  return (
    <div className="app-bottom-nav absolute bottom-0 left-0 right-0 z-40 px-3 pb-3 max-w-[440px] mx-auto pointer-events-none">
      <nav className="pointer-events-auto bg-slate-900/90 backdrop-blur-md border border-slate-700/60 rounded-3xl p-1.5 shadow-2xl flex items-center justify-around">
        {tabs.map((tab) => {
          const Icon = tab.icon;
          const isActive = activeTab === tab.id;

          if (tab.isSpecial) {
            return (
              <button
                key={tab.id}
                onClick={() => onChangeTab(tab.id)}
                className="relative -top-3 flex flex-col items-center group cursor-pointer focus-visible:outline-2"
                aria-label={tab.label}
                aria-current={isActive ? "page" : undefined}
              >
                <div
                  className={`w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 shadow-lg ${
                    isActive
                      ? 'bg-gradient-to-tr from-red-600 via-rose-500 to-amber-500 text-white scale-105 ring-4 ring-slate-900'
                      : 'bg-gradient-to-tr from-slate-800 to-slate-700 text-amber-400 hover:scale-105 ring-2 ring-slate-800'
                  }`}
                >
                  <Icon className="w-5 h-5" />
                </div>
                <span
                  className={`text-[10px] font-bold mt-1 tracking-tight transition ${
                    isActive ? 'text-amber-400' : 'text-slate-400'
                  }`}
                >
                  {tab.label}
                </span>
              </button>
            );
          }

          return (
            <button
              key={tab.id}
              onClick={() => onChangeTab(tab.id)}
              className={`relative flex flex-col items-center py-1.5 px-3 rounded-2xl transition-all duration-200 cursor-pointer focus:outline-none ${
                isActive
                  ? 'text-white'
                  : 'text-slate-400 hover:text-slate-200'
              }`}
              aria-label={tab.label}
                aria-current={isActive ? "page" : undefined}
            >
              <div className="relative">
                <Icon className={`w-5 h-5 transition-transform duration-200 ${isActive ? 'scale-110 text-red-500' : ''}`} />
                {tab.badge && !isActive && (
                  <span className="absolute -top-1.5 -right-2 bg-red-600 text-white text-[8px] font-extrabold px-1 py-0.2 rounded-full border border-slate-900 shadow-xs">
                    {tab.badge}
                  </span>
                )}
              </div>
              <span className={`text-[10px] mt-1 font-medium ${isActive ? 'text-white font-bold' : ''}`}>
                {tab.label}
              </span>
              {isActive && (
                <div className="w-1.5 h-1.5 rounded-full bg-red-500 mt-0.5 animate-pulse" />
              )}
            </button>
          );
        })}
      </nav>
    </div>
  );
};
