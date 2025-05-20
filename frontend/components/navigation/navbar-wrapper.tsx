"use client";
import React from "react";
import { NavBar, NavItem } from "./nav-bar";
import { MobileMenu, MobileNavItem } from "./mobile-menu";
import { useTranslations } from "next-intl";

interface NavbarWrapperProps {
  locale: string;
}

export function NavbarWrapper({ locale }: NavbarWrapperProps) {
  const t = useTranslations("Navbar");
  const items: NavItem[] = [
    { label: t("home"), href: `/${locale}` },
    { label: t("calendarResults"), href: `/${locale}/races` },
    { label: t("runners"), href: `/${locale}/runners` },
    { label: t("records"), href: `/${locale}/records` },
    { label: t("topLists"), href: `/${locale}/toplists` },
    { label: t("submitRace"), href: `/${locale}/submit-race` },
    { label: t("search"), href: `/${locale}/search` },
  ];

  const mobileItems: MobileNavItem[] = items.map(({ label, href }) => ({
    label,
    href,
  }));

  return (
    <>
      <NavBar items={items} />
      <MobileMenu items={mobileItems} />
    </>
  );
}
