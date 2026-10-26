import React, { createContext, useContext, useState, useEffect } from 'react';

export type BookmarkType = 'service' | 'test' | 'event';

interface BookmarksContextType {
  savedServices: string[];
  savedTests: string[];
  savedEvents: string[];
  isSaved: (type: BookmarkType, id: string) => boolean;
  toggleSave: (type: BookmarkType, id: string, title?: string) => boolean;
  removeSave: (type: BookmarkType, id: string) => void;
  clearAll: () => void;
  totalSavedCount: number;
}

const STORAGE_KEY_SERVICES = 'geic_saved_services';
const STORAGE_KEY_TESTS = 'geic_saved_tests';
const STORAGE_KEY_EVENTS = 'geic_saved_events';

const BookmarksContext = createContext<BookmarksContextType | undefined>(undefined);

export const BookmarksProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [savedServices, setSavedServices] = useState<string[]>(() => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY_SERVICES);
      return stored ? JSON.parse(stored) : ['counselling'];
    } catch {
      return ['counselling'];
    }
  });

  const [savedTests, setSavedTests] = useState<string[]>(() => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY_TESTS);
      return stored ? JSON.parse(stored) : ['ielts'];
    } catch {
      return ['ielts'];
    }
  });

  const [savedEvents, setSavedEvents] = useState<string[]>(() => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY_EVENTS);
      return stored ? JSON.parse(stored) : ['expo-2026'];
    } catch {
      return ['expo-2026'];
    }
  });

  // Sync to localStorage
  useEffect(() => {
    try {
      localStorage.setItem(STORAGE_KEY_SERVICES, JSON.stringify(savedServices));
    } catch {}
  }, [savedServices]);

  useEffect(() => {
    try {
      localStorage.setItem(STORAGE_KEY_TESTS, JSON.stringify(savedTests));
    } catch {}
  }, [savedTests]);

  useEffect(() => {
    try {
      localStorage.setItem(STORAGE_KEY_EVENTS, JSON.stringify(savedEvents));
    } catch {}
  }, [savedEvents]);

  const isSaved = (type: BookmarkType, id: string): boolean => {
    switch (type) {
      case 'service':
        return savedServices.includes(id);
      case 'test':
        return savedTests.includes(id);
      case 'event':
        return savedEvents.includes(id);
      default:
        return false;
    }
  };

  const toggleSave = (type: BookmarkType, id: string, title?: string): boolean => {
    let nowSaved = false;

    if (type === 'service') {
      setSavedServices((prev) => {
        if (prev.includes(id)) {
          nowSaved = false;
          return prev.filter((item) => item !== id);
        } else {
          nowSaved = true;
          return [...prev, id];
        }
      });
    } else if (type === 'test') {
      setSavedTests((prev) => {
        if (prev.includes(id)) {
          nowSaved = false;
          return prev.filter((item) => item !== id);
        } else {
          nowSaved = true;
          return [...prev, id];
        }
      });
    } else if (type === 'event') {
      setSavedEvents((prev) => {
        if (prev.includes(id)) {
          nowSaved = false;
          return prev.filter((item) => item !== id);
        } else {
          nowSaved = true;
          return [...prev, id];
        }
      });
    }

    return nowSaved;
  };

  const removeSave = (type: BookmarkType, id: string) => {
    if (type === 'service') {
      setSavedServices((prev) => prev.filter((item) => item !== id));
    } else if (type === 'test') {
      setSavedTests((prev) => prev.filter((item) => item !== id));
    } else if (type === 'event') {
      setSavedEvents((prev) => prev.filter((item) => item !== id));
    }
  };

  const clearAll = () => {
    setSavedServices([]);
    setSavedTests([]);
    setSavedEvents([]);
  };

  const totalSavedCount = savedServices.length + savedTests.length + savedEvents.length;

  return (
    <BookmarksContext.Provider
      value={{
        savedServices,
        savedTests,
        savedEvents,
        isSaved,
        toggleSave,
        removeSave,
        clearAll,
        totalSavedCount,
      }}
    >
      {children}
    </BookmarksContext.Provider>
  );
};

export const useBookmarks = (): BookmarksContextType => {
  const context = useContext(BookmarksContext);
  if (!context) {
    throw new Error('useBookmarks must be used within a BookmarksProvider');
  }
  return context;
};
