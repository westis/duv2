/**
 * Custom hooks for accessing and updating user preferences.
 */
import { usePreferencesContext } from "../context/preferences-context";
import type { Preferences } from "../../types/preferences";
import {
  UnitSystem,
  DateFormat,
  TimeFormatType,
  ThemeType,
  Language,
  FontSize,
} from "../../types/preferences";

/**
 * Hook to get the entire preferences object.
 */
export function usePreferences(): Preferences {
  return usePreferencesContext().preferences;
}

/**
 * Hook to get the updatePreferences function.
 */
export function useUpdatePreferences(): (
  updates: Partial<Preferences>,
) => void {
  return usePreferencesContext().updatePreferences;
}

/**
 * Hook to reset preferences to defaults.
 */
export function useResetPreferences(): () => void {
  return usePreferencesContext().resetPreferences;
}

/**
 * Hook for unit system preference with setter.
 */
export function useUnitSystem(): [UnitSystem, (unit: UnitSystem) => void] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setUnitSystem = (unit: UnitSystem) => {
    if (!Object.values(UnitSystem).includes(unit)) {
      console.warn(`Invalid unit system: ${unit}`);
      return;
    }
    updatePreferences({ unitSystem: unit });
  };
  return [preferences.unitSystem, setUnitSystem];
}

/**
 * Hook for date format preference with setter.
 */
export function useDateFormat(): [DateFormat, (format: DateFormat) => void] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setDateFormat = (format: DateFormat) => {
    if (!Object.values(DateFormat).includes(format)) {
      console.warn(`Invalid date format: ${format}`);
      return;
    }
    updatePreferences({ dateFormat: format });
  };
  return [preferences.dateFormat, setDateFormat];
}

/**
 * Hook for time format preference with setter.
 */
export function useTimeFormat(): [
  TimeFormatType,
  (format: TimeFormatType) => void,
] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setTimeFormat = (format: TimeFormatType) => {
    if (!Object.values(TimeFormatType).includes(format)) {
      console.warn(`Invalid time format: ${format}`);
      return;
    }
    updatePreferences({ timeFormat: format });
  };
  return [preferences.timeFormat, setTimeFormat];
}

/**
 * Hook for theme preference with setter.
 */
export function useTheme(): [ThemeType, (theme: ThemeType) => void] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setTheme = (theme: ThemeType) => {
    if (!Object.values(ThemeType).includes(theme)) {
      console.warn(`Invalid theme: ${theme}`);
      return;
    }
    updatePreferences({ theme });
  };
  return [preferences.theme, setTheme];
}

/**
 * Hook for language preference with setter.
 */
export function useLanguage(): [Language, (language: Language) => void] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setLanguage = (language: Language) => {
    if (!Object.values(Language).includes(language)) {
      console.warn(`Invalid language: ${language}`);
      return;
    }
    updatePreferences({ language });
  };
  return [preferences.language, setLanguage];
}

/**
 * Hook for font size preference with setter.
 */
export function useFontSize(): [FontSize, (size: FontSize) => void] {
  const { preferences, updatePreferences } = usePreferencesContext();
  const setFontSize = (size: FontSize) => {
    if (!Object.values(FontSize).includes(size)) {
      console.warn(`Invalid font size: ${size}`);
      return;
    }
    updatePreferences({ fontSize: size });
  };
  return [preferences.fontSize, setFontSize];
}
