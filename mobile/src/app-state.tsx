import AsyncStorage from '@react-native-async-storage/async-storage';
import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { fetchCatalog } from './api';
import { emptyEnquiryDraft, fallbackCatalog } from './catalog';
import type { CatalogData, EnquiryDraft } from './types';

const STORAGE_KEYS = {
  readNotifications: '@geic/v1/read-notifications',
  checklist: '@geic/v1/document-checklist',
  enquiryDraft: '@geic/v1/enquiry-draft',
} as const;

type AppStateValue = {
  catalog: CatalogData;
  catalogSource: 'verified-live' | 'bundled';
  isRefreshing: boolean;
  refreshCatalog: () => Promise<void>;
  readNotificationIds: string[];
  markNotificationRead: (id: string) => void;
  markAllNotificationsRead: () => void;
  checklist: string[];
  toggleChecklist: (id: string) => void;
  enquiryDraft: EnquiryDraft;
  updateEnquiryDraft: (draft: EnquiryDraft) => void;
  clearEnquiryDraft: () => void;
};

const AppStateContext = createContext<AppStateValue | null>(null);

export function AppStateProvider({ children }: { children: React.ReactNode }) {
  const [catalog, setCatalog] = useState(fallbackCatalog);
  const [catalogSource, setCatalogSource] = useState<'verified-live' | 'bundled'>('bundled');
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [readNotificationIds, setReadNotificationIds] = useState<string[]>([]);
  const [checklist, setChecklist] = useState<string[]>([]);
  const [enquiryDraft, setEnquiryDraft] = useState<EnquiryDraft>(emptyEnquiryDraft);

  useEffect(() => {
    AsyncStorage.multiGet(Object.values(STORAGE_KEYS)).then((values) => {
      for (const [key, value] of values) {
        if (!value) continue;
        try {
          if (key === STORAGE_KEYS.readNotifications) setReadNotificationIds(JSON.parse(value));
          if (key === STORAGE_KEYS.checklist) setChecklist(JSON.parse(value));
          if (key === STORAGE_KEYS.enquiryDraft) setEnquiryDraft({ ...emptyEnquiryDraft, ...JSON.parse(value) });
        } catch {
          void AsyncStorage.removeItem(key);
        }
      }
    }).catch(() => undefined);
    void refreshCatalog();
  }, []);

  async function refreshCatalog() {
    setIsRefreshing(true);
    try {
      setCatalog(await fetchCatalog());
      setCatalogSource('verified-live');
    } catch {
      setCatalog(fallbackCatalog);
      setCatalogSource('bundled');
    } finally {
      setIsRefreshing(false);
    }
  }

  function persistReadIds(ids: string[]) {
    setReadNotificationIds(ids);
    void AsyncStorage.setItem(STORAGE_KEYS.readNotifications, JSON.stringify(ids));
  }

  function markNotificationRead(id: string) {
    if (!readNotificationIds.includes(id)) persistReadIds([...readNotificationIds, id]);
  }

  function markAllNotificationsRead() {
    persistReadIds(catalog.events.map((event) => `event:${event.id}`));
  }

  function toggleChecklist(id: string) {
    const next = checklist.includes(id) ? checklist.filter((item) => item !== id) : [...checklist, id];
    setChecklist(next);
    void AsyncStorage.setItem(STORAGE_KEYS.checklist, JSON.stringify(next));
  }

  function updateEnquiryDraft(draft: EnquiryDraft) {
    setEnquiryDraft(draft);
    void AsyncStorage.setItem(STORAGE_KEYS.enquiryDraft, JSON.stringify(draft));
  }

  function clearEnquiryDraft() {
    setEnquiryDraft(emptyEnquiryDraft);
    void AsyncStorage.removeItem(STORAGE_KEYS.enquiryDraft);
  }

  const value = useMemo<AppStateValue>(() => ({
    catalog,
    catalogSource,
    isRefreshing,
    refreshCatalog,
    readNotificationIds,
    markNotificationRead,
    markAllNotificationsRead,
    checklist,
    toggleChecklist,
    enquiryDraft,
    updateEnquiryDraft,
    clearEnquiryDraft,
  }), [catalog, catalogSource, isRefreshing, readNotificationIds, checklist, enquiryDraft]);

  return <AppStateContext.Provider value={value}>{children}</AppStateContext.Provider>;
}

export function useAppState() {
  const value = useContext(AppStateContext);
  if (!value) throw new Error('useAppState must be used inside AppStateProvider.');
  return value;
}
