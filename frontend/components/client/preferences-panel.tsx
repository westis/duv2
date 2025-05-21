"use client";

import React, { useEffect } from "react";
import {
  useUnitSystem,
  useDateFormat,
  useTheme,
  useLanguage,
  useResetPreferences,
} from "../../lib/hooks/use-preferences";
import { useTranslations } from "next-intl";
import {
  UnitSystem,
  DateFormat,
  ThemeType,
  Language,
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
  const [theme, setTheme] = useTheme();
  const [language, setLanguage] = useLanguage();
  const resetPreferences = useResetPreferences();
  const t = useTranslations("PreferencesPanel");

  // Initialize theme based on system preference or current HTML class
  useEffect(() => {
    // Check if there's already a dark class on the HTML element
    const isDarkModeActive =
      document.documentElement.classList.contains("dark");

    // Synchronize our theme state with what's actually on the HTML element
    // This ensures we don't override the existing theme when the panel opens
    if (isDarkModeActive && theme !== ThemeType.Dark) {
      setTheme(ThemeType.Dark);
    } else if (!isDarkModeActive && theme !== ThemeType.Light) {
      setTheme(ThemeType.Light);
    }
  }, [theme, setTheme]);

  // Apply theme class to HTML element when theme changes - we keep this
  // separate and will only apply changes when the user explicitly clicks
  // a theme button
  useEffect(() => {
    function handleThemeChange(newTheme: ThemeType) {
      const htmlElement = document.documentElement;
      if (newTheme === ThemeType.Dark) {
        htmlElement.classList.add("dark");
      } else {
        htmlElement.classList.remove("dark");
      }
    }

    // Only run this effect when theme changes due to user action
    const themeButtons = document.querySelectorAll<HTMLButtonElement>(
      "button[data-theme-action]",
    );
    themeButtons.forEach((button) => {
      button.addEventListener("click", () => {
        if (button.dataset.themeAction === "light") {
          handleThemeChange(ThemeType.Light);
        } else {
          handleThemeChange(ThemeType.Dark);
        }
      });
    });

    return () => {
      themeButtons.forEach((button) => {
        button.removeEventListener("click", () => {});
      });
    };
  }, []);

  return (
    <div className="space-y-6 p-0">
      <h2 className="text-xl font-semibold">{t("settingsTitle")}</h2>

      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium">
            {t("unitSystemLabel")}
          </label>
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
              {t("kilometersOption")}
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
              {t("milesOption")}
            </button>
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium">
            {t("dateFormatLabel")}
          </label>
          <Select value={dateFormat} onValueChange={setDateFormat}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder={t("dateFormatLabel")} />
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
          <label className="block text-sm font-medium">{t("themeLabel")}</label>
          <div className="flex mt-1 overflow-hidden rounded-md border border-border">
            <button
              data-theme-action="light"
              onClick={() => setTheme(ThemeType.Light)}
              className={cn(
                "flex-1 py-2 text-sm font-medium",
                theme === ThemeType.Light
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              {t("lightOption")}
            </button>
            <button
              data-theme-action="dark"
              onClick={() => setTheme(ThemeType.Dark)}
              className={cn(
                "flex-1 py-2 text-sm font-medium border-l border-border",
                theme === ThemeType.Dark
                  ? "bg-primary text-primary-fg"
                  : "bg-card text-card-fg",
              )}
            >
              {t("darkOption")}
            </button>
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium">
            {t("languageLabel")}
          </label>
          <Select value={language} onValueChange={setLanguage}>
            <SelectTrigger className="mt-1 w-full">
              <SelectValue placeholder={t("languageLabel")} />
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
      </div>

      <button
        type="button"
        onClick={resetPreferences}
        className="btn-primary w-full mt-4"
      >
        {t("resetButton")}
      </button>
    </div>
  );
}
