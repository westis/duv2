import type { Preferences } from "../../types/preferences";
import { getDefaultPreferences } from "../../types/preferences";

const STORAGE_KEY = "user-preferences";
const STORAGE_VERSION = 1;

interface StoredPreferences {
  version: number;
  preferences: Preferences;
}

/**
 * Save preferences to localStorage with versioning and error handling
 */
export function savePreferences(prefs: Preferences): void {
  if (typeof window === "undefined") return;
  try {
    const stored: StoredPreferences = {
      version: STORAGE_VERSION,
      preferences: prefs,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
  } catch (error) {
    console.error("Failed to save preferences to localStorage", error);
  }
}

/**
 * Load preferences from localStorage. Returns null if unavailable or version mismatch.
 */
export function loadPreferences(): Preferences | null {
  if (typeof window === "undefined") return null;
  try {
    const item = localStorage.getItem(STORAGE_KEY);
    if (!item) {
      return null;
    }
    const parsed = JSON.parse(item) as StoredPreferences;
    if (parsed.version === STORAGE_VERSION && parsed.preferences) {
      return parsed.preferences;
    }
    // Version mismatch or invalid data, clear storage
    clearPreferences();
    return null;
  } catch (error) {
    console.error("Failed to load preferences from localStorage", error);
    clearPreferences();
    return null;
  }
}

/**
 * Remove preferences from localStorage
 */
export function clearPreferences(): void {
  if (typeof window === "undefined") return;
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (error) {
    console.error("Failed to clear preferences from localStorage", error);
  }
}

/**
 * Load preferences or return defaults if none are stored
 */
export function loadPreferencesOrDefault(): Preferences {
  const prefs = loadPreferences();
  return prefs ?? getDefaultPreferences();
}
