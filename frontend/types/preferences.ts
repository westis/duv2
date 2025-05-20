export enum UnitSystem {
  Kilometers = "kilometers",
  Miles = "miles",
}

export enum DateFormat {
  YYYY_MM_DD = "YYYY-MM-DD",
  DD_MM_YYYY = "DD/MM/YYYY",
  MM_DD_YYYY = "MM/DD/YYYY",
  DD_DOT_MM_DOT_YYYY = "DD.MM.YYYY",
}

export enum TimeFormatType {
  H12 = "12h",
  H24 = "24h",
}

export enum ThemeType {
  Light = "light",
  Dark = "dark",
}

export enum Language {
  En = "en",
  De = "de",
  Sv = "sv",
}

export enum FontSize {
  Small = "small",
  Medium = "medium",
  Large = "large",
}

export interface Preferences {
  unitSystem: UnitSystem;
  dateFormat: DateFormat;
  timeFormat: TimeFormatType;
  theme: ThemeType;
  language: Language;
  fontSize: FontSize;
  // Add new preference fields here as needed
}

/**
 * Returns the default user preferences.
 */
export function getDefaultPreferences(): Preferences {
  return {
    unitSystem: UnitSystem.Kilometers,
    dateFormat: DateFormat.YYYY_MM_DD,
    timeFormat: TimeFormatType.H24,
    theme: ThemeType.Light,
    language: Language.En,
    fontSize: FontSize.Medium,
  };
}
