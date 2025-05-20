"use client";
import React, { useState } from "react";
import { NavBar, NavItem } from "./nav-bar";
import { MobileMenu } from "./mobile-menu";
import { useTranslations } from "next-intl";

interface NavbarWrapperProps {
  locale: string;
}

export function NavbarWrapper({ locale }: NavbarWrapperProps) {
  const t = useTranslations("Navbar");
  const items: NavItem[] = [
    { label: t("home"), href: `/${locale}` },
    { label: t("calendarResults"), href: `/${locale}/races` },
    { label: t("topLists"), href: `/${locale}/toplists` },
    { label: t("records"), href: `/${locale}/records` },
    { label: t("submitRace"), href: `/${locale}/submit-race` },
    { label: t("search"), href: `/${locale}/search` },
  ];

  const [isOpen, setIsOpen] = useState(false);
  const handleOpen = () => setIsOpen(true);
  const handleClose = () => setIsOpen(false);

  const mobileItems = items
    .filter((item) => typeof item.href === "string")
    .map((item) => ({ label: item.label, href: item.href as string }));

  return (
    <>
      <NavBar items={items} />
      <button onClick={handleOpen} className="md:hidden p-2">
        Open Menu
      </button>
      <MobileMenu items={mobileItems} isOpen={isOpen} onClose={handleClose} />
    </>
  );
}
