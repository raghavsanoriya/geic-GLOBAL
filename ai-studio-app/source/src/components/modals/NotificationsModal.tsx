import React, { useState } from 'react';
import { 
  X, 
  Bell, 
  Calendar, 
  Clock, 
  AlertCircle, 
  CheckCircle2, 
  Ticket, 
  GraduationCap, 
  ChevronRight, 
  Check, 
  Flame, 
  Sparkles,
  Award
} from 'lucide-react';
import { AppTab } from '../../types';

export interface AppNotification {
  id: string;
  title: string;
  category: 'deadline' | 'expo' | 'scholarship' | 'prep';
  categoryLabel: string;
  urgency: 'high' | 'medium' | 'info';
  date: string;
  summary: string;
  actionText: string;
  actionType: 'expo' | 'evaluator' | 'booking' | 'tests';
  country?: string;
  isRead: boolean;
}

import { NOTIFICATIONS } from '../../data/liveCatalog';

interface NotificationsModalProps {
  isOpen: boolean;
  onClose: () => void;
  onNavigateTab: (tab: AppTab) => void;
  onOpenBooking: (country?: string) => void;
}

export const NotificationsModal: React.FC<NotificationsModalProps> = ({
  isOpen,
  onClose,
  onNavigateTab,
  onOpenBooking,
}) => {
  const [notifications, setNotifications] = useState<AppNotification[]>(NOTIFICATIONS);
  const [activeFilter, setActiveFilter] = useState<'all' | 'deadline' | 'expo' | 'scholarship'>('all');

  if (!isOpen) return null;

  const unreadCount = notifications.filter((n) => !n.isRead).length;

  const filteredNotifications = notifications.filter((n) => {
    if (activeFilter === 'all') return true;
    return n.category === activeFilter;
  });

  const markAllAsRead = () => {
    setNotifications((prev) => prev.map((n) => ({ ...n, isRead: true })));
  };

  const markSingleAsRead = (id: string) => {
    setNotifications((prev) =>
      prev.map((n) => (n.id === id ? { ...n, isRead: true } : n))
    );
  };

  const handleAction = (notif: AppNotification) => {
    markSingleAsRead(notif.id);
    onClose();

    if (notif.actionType === 'expo') {
      onNavigateTab('expo');
    } else if (notif.actionType === 'evaluator') {
      onNavigateTab('evaluator');
    } else if (notif.actionType === 'booking') {
      onOpenBooking(notif.country || 'Australia');
    } else if (notif.actionType === 'tests') {
      onNavigateTab('more');
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/75 backdrop-blur-xs p-0 sm:p-4 animate-in fade-in duration-200">
      <div 
        className="relative w-full max-w-lg bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-300"
      >
        {/* Modal Header */}
        <div className="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
          <div className="flex items-center space-x-2.5">
            <div className="w-8 h-8 rounded-xl bg-red-600/20 text-red-400 border border-red-500/30 flex items-center justify-center">
              <Bell className="w-4 h-4" />
            </div>
            <div>
              <div className="flex items-center space-x-2">
                <h3 className="font-black text-sm text-white">Alerts & Deadlines</h3>
                {unreadCount > 0 && (
                  <span className="text-[10px] bg-red-600 text-white font-extrabold px-1.5 py-0.2 rounded-full">
                    {unreadCount} new
                  </span>
                )}
              </div>
              <p className="text-[10px] text-slate-400">
                Application cut-offs, Indore Expo passes & university updates
              </p>
            </div>
          </div>

          <div className="flex items-center space-x-2">
            {unreadCount > 0 && (
              <button
                onClick={markAllAsRead}
                className="text-[10px] font-bold text-amber-400 hover:text-amber-300 transition cursor-pointer flex items-center gap-1"
              >
                <Check className="w-3 h-3" />
                <span>Mark read</span>
              </button>
            )}
            <button
              onClick={onClose}
              className="p-1.5 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition cursor-pointer"
              aria-label="Close notifications"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Filter Pills */}
        <div className="px-4 py-2 bg-slate-50 border-b border-slate-200 flex items-center space-x-1.5 overflow-x-auto no-scrollbar shrink-0">
          {[
            { id: 'all', label: 'All Updates' },
            { id: 'deadline', label: 'Deadlines' },
            { id: 'expo', label: 'Expo & Summits' },
            { id: 'scholarship', label: 'Scholarships' },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveFilter(tab.id as any)}
              className={`px-3 py-1 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer ${
                activeFilter === tab.id
                  ? 'bg-slate-900 text-white shadow-xs'
                  : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Notification Cards List */}
        <div className="p-4 overflow-y-auto space-y-3 flex-1">
          {filteredNotifications.length === 0 ? (
            <div className="text-center py-10 space-y-2 text-slate-500">
              <CheckCircle2 className="w-10 h-10 text-emerald-500 mx-auto" />
              <p className="text-xs font-semibold">You’re all caught up!</p>
              <p className="text-[11px]">No active notifications under this filter.</p>
            </div>
          ) : (
            filteredNotifications.map((item) => (
              <div
                key={item.id}
                onClick={() => markSingleAsRead(item.id)}
                className={`p-3.5 rounded-2xl border transition-all space-y-2 relative ${
                  item.isRead
                    ? 'bg-white border-slate-200 text-slate-700 opacity-90'
                    : 'bg-gradient-to-br from-red-50/40 via-white to-amber-50/30 border-red-200 text-slate-900 shadow-xs'
                }`}
              >
                {/* Header row with badges and timestamp */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-1.5">
                    <span
                      className={`text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md ${
                        item.urgency === 'high'
                          ? 'bg-red-600 text-white'
                          : item.urgency === 'medium'
                          ? 'bg-amber-100 text-amber-900'
                          : 'bg-blue-100 text-blue-900'
                      }`}
                    >
                      {item.categoryLabel}
                    </span>

                    {!item.isRead && (
                      <span className="w-2 h-2 rounded-full bg-red-600 animate-pulse" />
                    )}
                  </div>

                  <span className="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                    <Clock className="w-2.5 h-2.5 text-slate-400" />
                    {item.date}
                  </span>
                </div>

                {/* Title and summary */}
                <div>
                  <h4 className="font-extrabold text-xs text-slate-900 leading-snug">
                    {item.title}
                  </h4>
                  <p className="text-[11px] text-slate-600 mt-1 leading-relaxed">
                    {item.summary}
                  </p>
                </div>

                {/* Action button */}
                <div className="pt-1 flex items-center justify-between">
                  <span className="text-[10px] text-slate-500 font-medium">
                    {item.country ? `Target: ${item.country}` : 'Trans Globe Indore'}
                  </span>

                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      handleAction(item);
                    }}
                    className="bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl transition flex items-center space-x-1 shadow-xs cursor-pointer active:scale-95"
                  >
                    <span>{item.actionText}</span>
                    <ChevronRight className="w-3 h-3 text-amber-400" />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>

        {/* Modal Footer */}
        <div className="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-600 shrink-0">
          <span className="flex items-center gap-1">
            <Sparkles className="w-3 h-3 text-red-600" />
            Official Trans Globe Indore alerts desk
          </span>
          <button
            onClick={onClose}
            className="font-bold text-slate-700 hover:text-slate-950 px-2 py-1 cursor-pointer"
          >
            Dismiss
          </button>
        </div>
      </div>
    </div>
  );
};
