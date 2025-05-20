"use client";

import React from "react";
import { PreferencesProvider } from "@/lib/context/preferences-context";
import { SiteHeader } from "@/components/layout/site-header";
import { SiteFooter } from "@/components/layout/site-footer";

export default function Providers({
  children,
  locale,
}: {
  children: React.ReactNode;
  locale: string;
}) {
  return (
    <PreferencesProvider>
      <SiteHeader locale={locale} />
      {children}
      <SiteFooter />
    </PreferencesProvider>
  );
}
