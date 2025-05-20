"use client";

import React, {
  createContext,
  useContext,
  useState,
  useEffect,
  useMemo,
} from "react";
import type { ReactNode } from "react";
import type { Preferences } from "../../types/preferences";
import { getDefaultPreferences } from "../../types/preferences";
import {
  loadPreferencesOrDefault,
  savePreferences,
  clearPreferences,
} from "../utils/storage";

export interface PreferencesContextType {
  preferences: Preferences;
  updatePreferences: (updates: Partial<Preferences>) => void;
  resetPreferences: () => void;
}

const PreferencesContext = createContext<PreferencesContextType | undefined>(
  undefined,
);

export const PreferencesProvider = ({ children }: { children: ReactNode }) => {
  const [preferences, setPreferences] = useState<Preferences>(() =>
    loadPreferencesOrDefault(),
  );

  useEffect(() => {
    savePreferences(preferences);
  }, [preferences]);

  const updatePreferences = (updates: Partial<Preferences>) => {
    setPreferences((prev) => ({ ...prev, ...updates }));
  };

  const resetPreferences = () => {
    clearPreferences();
    setPreferences(getDefaultPreferences());
  };

  const value = useMemo(
    () => ({ preferences, updatePreferences, resetPreferences }),
    [preferences],
  );

  return (
    <PreferencesContext.Provider value={value}>
      {children}
    </PreferencesContext.Provider>
  );
};

export function usePreferencesContext(): PreferencesContextType {
  const context = useContext(PreferencesContext);
  if (!context) {
    throw new Error(
      "usePreferencesContext must be used within PreferencesProvider",
    );
  }
  return context;
}
