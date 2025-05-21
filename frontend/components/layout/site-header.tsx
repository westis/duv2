"use client";

import React from "react";
import Link from "next/link";
import { useTranslations } from "next-intl";
import { MenuIcon, SettingsIcon } from "lucide-react";
import {
  Popover,
  PopoverTrigger,
  PopoverContent,
} from "@/components/ui/popover";

import { MainNav } from "./main-nav";
import { MobileMenu } from "@/components/navigation/mobile-menu";
import PreferencesPanel from "@/components/client/preferences-panel";

interface SiteHeaderProps {
  locale: string;
}

export function SiteHeader({ locale }: SiteHeaderProps) {
  const [open, setOpen] = React.useState(false);
  const t = useTranslations("Navbar");

  const navItems = [
    { label: t("home"), href: `/${locale}` },
    { label: t("calendarResults"), href: `/${locale}/races` },
    { label: t("topLists"), href: `/${locale}/toplists` },
    { label: t("records"), href: `/${locale}/records` },
    { label: t("submitRace"), href: `/${locale}/submit-race` },
    { label: t("search"), href: `/${locale}/search` },
  ];

  return (
    <header className="sticky top-0 z-50 w-full border-b border-[var(--color-border)] bg-[var(--color-header)] text-[var(--color-header-foreground)] backdrop-blur">
      {/* inner wrapper – same 96 rem as .container */}
      <div className="mx-auto flex h-16 w-full max-w-[96rem] items-center justify-between px-4 sm:px-6 lg:px-8">
        {/* Logo */}
        <Link href={`/${locale}`} className="flex items-center space-x-2">
          <img
            src="/logo/duv_logo_with_name.png"
            alt="DUV Logo"
            className="h-9 w-auto dark:hidden"
          />
          <img
            src="/logo/duv_logo_with_name_white.png"
            alt="DUV Logo"
            className="hidden h-9 w-auto dark:block"
          />
        </Link>

        {/* Desktop nav */}
        <nav className="hidden flex-1 justify-center md:flex uppercase">
          <MainNav items={navItems} />
        </nav>

        {/* Controls */}
        <div className="flex items-center gap-2">
          <Popover>
            <PopoverTrigger asChild>
              <button
                className="inline-flex items-center justify-center rounded-md p-2 text-[var(--color-foreground)]/60 hover:bg-[var(--color-muted)]/20 hover:text-[var(--color-foreground)]"
                aria-label="Preferences"
              >
                <SettingsIcon className="h-5 w-5" />
              </button>
            </PopoverTrigger>
            <PopoverContent align="end" className="w-80 p-0">
              <PreferencesPanel />
            </PopoverContent>
          </Popover>
          <button
            onClick={() => setOpen((s) => !s)}
            className="inline-flex items-center justify-center rounded-md p-2 text-[var(--color-foreground)]/60 hover:bg-[var(--color-muted)]/20 hover:text-[var(--color-foreground)] md:hidden"
          >
            <MenuIcon className="h-5 w-5" />
            <span className="sr-only">Menu</span>
          </button>
        </div>
      </div>

      {/* Mobile overlay */}
      {open && (
        <MobileMenu
          items={navItems}
          isOpen={open}
          onClose={() => setOpen(false)}
        />
      )}
    </header>
  );
}
