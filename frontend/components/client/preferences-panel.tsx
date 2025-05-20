"use client";

import React from "react";
import {
  useUnitSystem,
  useDateFormat,
  useTimeFormat,
  useTheme,
  useLanguage,
  useFontSize,
  useResetPreferences,
} from "../../lib/hooks/use-preferences";
import {
  UnitSystem,
  DateFormat,
  TimeFormatType,
  ThemeType,
  Language,
  FontSize,
} from "../../types/preferences";
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from "@/components/ui/select";

export default function PreferencesPanel() {
  const [unitSystem, setUnitSystem] = useUnitSystem();
  const [dateFormat, setDateFormat] = useDateFormat();
  const [timeFormat, setTimeFormat] = useTimeFormat();
  const [theme, setTheme] = useTheme();
  const [language, setLanguage] = useLanguage();
  const [fontSize, setFontSize] = useFontSize();
  const resetPreferences = useResetPreferences();

  return (
    <div className="card space-y-6">
      <h2 className="text-xl font-semibold">Settings</h2>

      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium">Unit System</label>
          <Select value={unitSystem} onValueChange={setUnitSystem}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select unit" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value={UnitSystem.Kilometers}>Kilometers</SelectItem>
              <SelectItem value={UnitSystem.Miles}>Miles</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div>
          <label className="block text-sm font-medium">Date Format</label>
          <Select value={dateFormat} onValueChange={setDateFormat}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select date format" />
            </SelectTrigger>
            <SelectContent>
              {Object.values(DateFormat).map((fmt) => (
                <SelectItem key={fmt} value={fmt}>
                  {fmt}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div>
          <label className="block text-sm font-medium">Time Format</label>
          <Select value={timeFormat} onValueChange={setTimeFormat}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select time" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value={TimeFormatType.H12}>12-hour</SelectItem>
              <SelectItem value={TimeFormatType.H24}>24-hour</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div>
          <label className="block text-sm font-medium">Theme</label>
          <Select value={theme} onValueChange={setTheme}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select theme" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value={ThemeType.Light}>Light</SelectItem>
              <SelectItem value={ThemeType.Dark}>Dark</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div>
          <label className="block text-sm font-medium">Language</label>
          <Select value={language} onValueChange={setLanguage}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select language" />
            </SelectTrigger>
            <SelectContent>
              {Object.values(Language).map((lang) => (
                <SelectItem key={lang} value={lang}>
                  {lang}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div>
          <label className="block text-sm font-medium">Font Size</label>
          <Select value={fontSize} onValueChange={setFontSize}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder="Select font size" />
            </SelectTrigger>
            <SelectContent>
              {Object.values(FontSize).map((size) => (
                <SelectItem key={size} value={size}>
                  {size}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>

      <button
        type="button"
        onClick={resetPreferences}
        className="btn-primary w-full mt-4"
      >
        Reset to Defaults
      </button>
    </div>
  );
}
