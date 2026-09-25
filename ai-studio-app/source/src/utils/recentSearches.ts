const RECENT_SEARCHES_STORAGE_KEY = 'tg_indore_recent_searches_v1';
export const MAX_RECENT_SEARCHES = 5;

// Initial helpful default searches if the user has no history yet, so the UI is immediately interactive
export const DEFAULT_RECENT_SEARCHES = [
  'IELTS Band 7.5',
  'USA Student Visa',
  'Global Uni Expo',
  'Germany Free Tuition',
  'STEM Scholarships'
];

/**
 * Retrieves the user's last 5 search queries from localStorage.
 */
export const getRecentSearches = (): string[] => {
  if (typeof window === 'undefined') return [];
  try {
    const raw = localStorage.getItem(RECENT_SEARCHES_STORAGE_KEY);
    if (!raw) {
      // Seed with initial realistic recent searches for a first-time Indore user
      localStorage.setItem(RECENT_SEARCHES_STORAGE_KEY, JSON.stringify(DEFAULT_RECENT_SEARCHES));
      return DEFAULT_RECENT_SEARCHES;
    }
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) {
      return parsed
        .filter((q): q is string => typeof q === 'string' && q.trim().length > 0)
        .slice(0, MAX_RECENT_SEARCHES);
    }
    return [];
  } catch (e) {
    console.error('Failed to read recent searches from localStorage:', e);
    return [];
  }
};

/**
 * Adds a new search query to localStorage (up to 5 items, most recent first, deduplicated).
 */
export const saveRecentSearch = (query: string): string[] => {
  const trimmed = query.trim();
  if (!trimmed || trimmed.length < 2) return getRecentSearches();

  try {
    const existing = getRecentSearches();
    // Remove if already exists (case-insensitive deduplication)
    const filtered = existing.filter((item) => item.toLowerCase() !== trimmed.toLowerCase());
    const updated = [trimmed, ...filtered].slice(0, MAX_RECENT_SEARCHES);
    localStorage.setItem(RECENT_SEARCHES_STORAGE_KEY, JSON.stringify(updated));
    return updated;
  } catch (e) {
    console.error('Failed to save recent search to localStorage:', e);
    return getRecentSearches();
  }
};

/**
 * Removes a specific search query from localStorage.
 */
export const removeRecentSearch = (queryToRemove: string): string[] => {
  try {
    const existing = getRecentSearches();
    const updated = existing.filter((item) => item.toLowerCase() !== queryToRemove.toLowerCase());
    localStorage.setItem(RECENT_SEARCHES_STORAGE_KEY, JSON.stringify(updated));
    return updated;
  } catch (e) {
    console.error('Failed to remove recent search:', e);
    return getRecentSearches();
  }
};

/**
 * Clears all recent searches from localStorage.
 */
export const clearAllRecentSearches = (): void => {
  try {
    localStorage.removeItem(RECENT_SEARCHES_STORAGE_KEY);
  } catch (e) {
    console.error('Failed to clear recent searches:', e);
  }
};
