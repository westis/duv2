import React, { createContext, useContext } from "react";

const PreferencesContext = createContext({});

export function PreferencesProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <PreferencesContext.Provider value={{}}>
      {children}
    </PreferencesContext.Provider>
  );
}

export function usePreferencesContext() {
  return useContext(PreferencesContext);
}
