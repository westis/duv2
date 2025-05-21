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
import { cn } from "@/lib/utils";
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
    <div className="space-y-6 p-0">
      <h2 className="text-xl font-semibold">Settings</h2>

      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium">Unit System</label>
          <div className="flex mt-1 overflow-hidden rounded-md border border-border">
            <button
              onClick={() => setUnitSystem(UnitSystem.Kilometers)}
              className={cn(
                "flex-1 py-2 text-sm font-medium",
                unitSystem === UnitSystem.Kilometers
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              Kilometers
            </button>
            <button
              onClick={() => setUnitSystem(UnitSystem.Miles)}
              className={cn(
                "flex-1 py-2 text-sm font-medium border-l border-border",
                unitSystem === UnitSystem.Miles
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              Miles
            </button>
          </div>
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
          <div className="flex mt-1 overflow-hidden rounded-md border border-border">
            <button
              onClick={() => setTimeFormat(TimeFormatType.H12)}
              className={cn(
                "flex-1 py-2 text-sm font-medium",
                timeFormat === TimeFormatType.H12
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              12-hour
            </button>
            <button
              onClick={() => setTimeFormat(TimeFormatType.H24)}
              className={cn(
                "flex-1 py-2 text-sm font-medium border-l border-border",
                timeFormat === TimeFormatType.H24
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              24-hour
            </button>
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium">Theme</label>
          <div className="flex mt-1 overflow-hidden rounded-md border border-border">
            <button
              onClick={() => setTheme(ThemeType.Light)}
              className={cn(
                "flex-1 py-2 text-sm font-medium",
                theme === ThemeType.Light
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              Light
            </button>
            <button
              onClick={() => setTheme(ThemeType.Dark)}
              className={cn(
                "flex-1 py-2 text-sm font-medium border-l border-border",
                theme === ThemeType.Dark
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              Dark
            </button>
          </div>
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
