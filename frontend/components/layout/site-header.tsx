// components/layout/site-header.tsx
"use client";

import React from "react";
import { MainNav } from "./main-nav";
import ThemeToggle from "@/theme/theme-toggle";
import { useTranslations } from "next-intl";
import Link from "next/link";
import { MenuIcon } from "lucide-react";
import { MobileNav } from "@/components/navigation/mobile-menu";

interface SiteHeaderProps {
  locale: string;
}

export function SiteHeader({ locale }: SiteHeaderProps) {
  const [showMobileMenu, setShowMobileMenu] = React.useState(false);
  const t = useTranslations("Navbar");

  const navItems = [
    { label: t("home"), href: `/${locale}` },
    { label: t("calendarResults"), href: `/${locale}/races` },
    { label: t("runners"), href: `/${locale}/runners` },
    { label: t("records"), href: `/${locale}/records` },
    { label: t("topLists"), href: `/${locale}/toplists` },
    { label: t("submitRace"), href: `/${locale}/submit-race` },
    { label: t("search"), href: `/${locale}/search` },
  ];

  return (
    <header className="sticky top-0 z-50 bg-gradient-to-b from-background to-background/95 backdrop-blur-sm shadow-md">
      <div className="container max-w-7xl mx-auto px-4 flex h-16 items-center">
        <div className="flex items-center justify-between w-full">
          {/* Logo */}
          <Link href={`/${locale}`} className="flex items-center space-x-2">
            <img
              src="/logo/duv_logo_with_name.png"
              alt="DUV Logo"
              className="h-8 w-auto dark:hidden"
            />
            <img
              src="/logo/duv_logo_with_name_white.png"
              alt="DUV Logo"
              className="h-8 w-auto hidden dark:block"
            />
          </Link>

          {/* Desktop Navigation - Centered */}
          <div className="hidden md:flex justify-center flex-1">
            <MainNav items={navItems} />
          </div>

          {/* Right side controls */}
          <div className="flex items-center gap-2">
            <ThemeToggle />
            <button
              className="inline-flex items-center justify-center rounded-md p-2 text-foreground/60 hover:text-foreground hover:bg-muted md:hidden"
              onClick={(e) => {
                e.stopPropagation();
                setShowMobileMenu(!showMobileMenu);
              }}
            >
              <MenuIcon className="h-5 w-5" />
              <span className="sr-only">Open menu</span>
            </button>
          </div>
        </div>
      </div>
      {showMobileMenu && (
        <MobileNav
          items={navItems}
          isOpen={showMobileMenu}
          onClose={() => setShowMobileMenu(false)}
        />
      )}
    </header>
  );
}
